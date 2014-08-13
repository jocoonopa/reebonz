<?php

namespace Woojin\ApiBundle\Controller;

//Third Party
use Nelmio\ApiDocBundle\Annotation\ApiDoc;

//Component
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Exception\InvalidParameterException;
use Symfony\Component\Security\Core\Exception\InsufficientAuthenticationException;
use Symfony\Component\PropertyAccess\PropertyAccess;

//Default
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Cache;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;

// Entity
use Woojin\GoodsBundle\Entity\GoodsPassport;
use Woojin\OrderBundle\Entity\Orders;

/**
 * 關於 GoodsPassport(商品) CRUD 動作，
 *
 * @Route("/goodsPassport")
 */
class GoodsPassportController extends Controller
{
    const NONE_ENTITY       = 0;
    const NO_IMG            = '/img/404.png';
    const ROW_START         = 1;
    const IS_ALLOW          = 1;
    const IS_NORMAL         = 0;
    
    const GS_ON_SALE        = 1;
    const GS_OFF_SALE       = 4;
    const OK_IN             = 1;
    const OK_EXCHANGE_IN    = 2;
    const OK_TURN_IN        = 3;
    const OK_MOVE_IN        = 4;
    const OK_CONSIGN_IN     = 5;
    const OK_OUT            = 6;
    const OK_EXCHANGE_OUT   = 7;
    const OK_TURN_OUT       = 8;
    const OK_MOVE_OUT       = 9;
    const OK_FEEDBACK       = 10;
    const OK_WEB_OUT        = 11;
    const OK_SPECIAL_SELL   = 12;
    const OK_SAME_BS        = 13;
    const OS_HANDLING       = 1;
    const OS_COMPLETE       = 2;
    const OS_CANCEL         = 3;

    /**
     * 取得商品列表
     * 
     * @Route("", name="api_goodsPassport_list",options={"expose"=true})
     * @Method("GET")
     *
     * @ApiDoc(
     *  resource=true,
     *  description="取得商品列表",
     *  statusCodes={
     *    200="Returned when successful",
     *    403="Returned when the ApiKey is not matched to say hello",
     *    404={
     *     "Returned when the ApiKey is not matched",
     *     "Returned when something else is not found"
     *    },
     *    500={
     *     "Please contact author to fix it"
     *    }
     *  }
     * )
     */
    public function listAction()
    {
        /**
         * GoodsPassport entity array 
         * @var array{ object }
         */
        $goodsPassports = $this->getDoctrine()->getRepository('WoojinGoodsBundle:GoodsPassport')->findAll();

        $serializer = \JMS\Serializer\SerializerBuilder::create()->build();
        
        $jsonGoodsPassports = $serializer->serialize($goodsPassports, 'json');

        return new Response($jsonGoodsPassports);
    }

    /**
     * 根據搜尋條件取得商品列表，將搜尋條件以json 格式傳入，其格式如下:
     *
     * {
     *     "Gname": {
     *         "like": [品名](string)
     *     },
     *     "Gsn": {
     *         "in": [[產編](string)],
     *         "notIn": [[產編](string)]
     *     },
     *     "Gdpo": {
     *         "in": [[系統內部編號](string)], 
     *         "notIn": [[系統內部編號](string)]
     *     },
     *     "Gorg_sn": {
     *         "in": [[原廠型號](string)],
     *         "notIn": [[原廠型號](string)]
     *     },
     *     "Gbrand_sn": {
     *         "in": [[品牌型號](string)], 
     *         "notIn": [[品牌型號](string)]
     *     },
     *     "Gpurchase_at": {
     *         "gte": [起](string, format=yyyy-mm-dd),
     *         "lte": [迄](string, format=yyyy-mm-dd)
     *     },
     *     "Gexpirate_at": {
     *         "gte": [起](string, format=yyyy-mm-dd),
     *         "lte": [迄](string, format=yyyy-mm-dd)
     *     },
     *     "Gallow_discount": {
     *         "eq": [是否允許打折{1: 是, 0: 否}](int)
     *     },
     *     "Gis_web": {
     *         "eq": [是否在網路也有販售{1: 是, 0: 否}](int)
     *     },
     *     "Gbrand": {
     *         "in": [[品牌的id](int)],
     *         "notIn": [[品牌的id](int)]
     *     },
     *     "Gpattern": {
     *         "in": [[款式的id](int)],
     *         "notIn": [[款式的id](int)]
     *     },
     *     "Gsource": {
     *         "in": [[商品來源的id](int)],
     *         "notIn": [[商品來源的id](int)]
     *     },
     *     "Glevel": {
     *         "in": [[商品新舊的id](int)],
     *         "notIn": [[商品新舊的id](int)]
     *     },
     *     "Gstatus": {
     *         "in": [[商品狀態的id](int)],
     *         "notIn": [[商品狀態的id](int)]
     *     },
     *     "Gstore": {
     *         "in": [[商店的id](int)],
     *         "notIn": [[商店的id](int)]
     *     },
     *     "Gcolor": {
     *         "in": [[顏色的id](int)],
     *         "notIn": [[顏色的id](int)]
     *     },
     *     "Gmt": {
     *         "in": [[材料的id](int)],
     *         "notIn": [[材料的id](int)]
     *     },
     *     "Gsupplier": {
     *         "in": [[供貨商的id](int)],
     *         "notIn": [[供貨商的id](int)]
     *     },
     *     "Gactivity": {
     *         "in": [[活動的id](int)],
     *         "notIn": [[活動的id](int)]
     *     },
     *     "Gimgpath": [
     *         "isNotNull",
     *         "isNull"
     *     ],
     *     "Gin_type": {
     *         "eq":[商品為一般進貨或是寄賣{0: 一般, 1: 寄賣}](int)
     *     },
     *     "Gcreate_at": {
     *         "gte": [起](string, format=yyyy-mm-dd),
     *         "lte": [迄](string, format=yyyy-mm-dd)
     *     },
     *     "Gupdate_at": {
     *         "gte": [起](string, format=yyyy-mm-dd),
     *         "lte": [迄](string, format=yyyy-mm-dd)
     *     },
     *     "Gdiscount": {"eq": [打幾折](float)},
     *     "Gmemo": {
     *         "like": [備註包含的字串, 必須在 memo.has 為 1 時才會作用](string)
     *     }
     * }
     *
     * jsonOrderBy: {"attr": 屬性, "dir": "ASC|DESC"},
     * page: 頁數 ,
     * perPage: 每頁幾個,
     * _format: 格式
     *
     * 回傳的資料格式為 json 字串 或是 xlsx檔案，端看您的 _format決定。
     * 以下為json欄位介紹 {
     *  "count": 總共多少筆資料,
     *  "page": 目前為第幾頁資料,
     *  "perPage": 每頁幾筆資料,
     *  "goods": [{
     *      "name": 品名,
     *      "sn": 產編,
     *      "org_sn": 原廠型號,
     *      "brand_sn": 品牌型號,
     *      "cost": 成本,
     *      "price": 優惠價,
     *      "fake_price": 一般價,
     *      "dpo": 系統內部編號,
     *      "memo": 備註,
     *      "imgpath": 圖片路徑,
     *      "in_type": 進貨類型,
     *      "is_web": 是否允許在網路販售,
     *      "brand": 品牌,
     *      "pattern": 款式,
     *      "supplier": 供貨商,
     *      "activity": 活動,
     *      "color": 顏色,
     *      "status": 狀態,
     *      "mt": 材料,
     *      "level": 新舊,
     *      "source": 來源,
     *      "allow_discount": 是否允許折扣,
     *      "purchase_at": 進貨日期,
     *      "expirate_at": 過期日期,
     *      "store": 商店     
     *  }, ...,]
     * } 
     * 
     * 
     * @Route(
     *     "/filter/{jsonCondition}/{jsonOrderBy}/{page}/{perPage}/{_format}", 
     *     requirements={"page"="\d+", "perPage"="\d+"},
     *     defaults={
     *         "jsonCondition"="{}",
     *         "jsonOrderBy"="{}",
     *         "page"=1,
     *         "perPage"=100,
     *         "_format"="html"
     *     },
     *     name="api_goodsPassport_filter",
     *     options={"expose"=true}
     * )
     * @Method("GET")
     *
     * @ApiDoc(
     *  resource=true,
     *  description="根據搜尋條件取得商品列表",
     *  statusCodes={
     *    200="Returned when successful",
     *    403="Returned when the ApiKey is not matched to say hello",
     *    404={
     *     "Returned when the ApiKey is not matched",
     *     "Returned when something else is not found"
     *    },
     *    500={
     *     "Please contact author to fix it"
     *    }
     *  }
     * )
     */
    public function filterAction($jsonCondition, $jsonOrderBy, $page, $perPage, $_format)
    {
        /**
         * 將搜尋條件的 json 字串轉換成搜尋陣列
         * @var array
         */
        $conditions = json_decode($jsonCondition, true);

        /**
         * 排序參數
         * @var array
         */
        $orderBy = json_decode($jsonOrderBy, true);

        /**
         * serializer
         * @var object
         */
        $serializer = \JMS\Serializer\SerializerBuilder::create()->build();

        /**
         * 商品repo
         * @var object
         */
        $goodsRepo = $this->getDoctrine()->getRepository('WoojinGoodsBundle:GoodsPassport');

        /**
         * 回傳資料
         * @var 
         */
        $response = $goodsRepo->findByFilter($conditions, $orderBy, $page, $perPage);

        $jsonResponse = $serializer->serialize($response, 'json');

        return new Response($jsonResponse);
    }

    /**
     * 回傳該條件下(jsonCondition)查詢會得到的結果數量，用來形成頁籤
     * 
     * @Route(
     *     "/filterCount/{jsonCondition}", 
     *     defaults={"jsonCondition"="{}"},
     *     name="api_goodsPassport_filter_count",
     *     options={"expose"=true}
     * )
     * @Method("GET")
     *
     * @ApiDoc(
     *  resource=true,
     *  description="根據搜尋條件取得商品列表個數",
     *  statusCodes={
     *    200="Returned when successful",
     *    403="Returned when the ApiKey is not matched to say hello",
     *    404={
     *     "Returned when the ApiKey is not matched",
     *     "Returned when something else is not found"
     *    },
     *    500={
     *     "Please contact author to fix it"
     *    }
     *  }
     * )
     */
    public function filterCountAction($jsonCondition)
    {
        /**
         * 將搜尋條件的 json 字串轉換成搜尋陣列
         * @var array
         */
        $conditions = json_decode($jsonCondition, true);

        /**
         * 商品repo
         * @var object
         */
        $goodsRepo = $this->getDoctrine()->getRepository('WoojinGoodsBundle:GoodsPassport');

        /**
         * 該條件下會得到的商品數
         * @var integer
         */
        $count = $goodsRepo->findCountByFilter($conditions);

        return new Response($count);
    }

    /**
     * 取得單一商品實體
     * 
     * @Route("/{id}", requirements={"id" = "\d+"}, name="api_goodsPassport_show",options={"expose"=true})
     * @ParamConverter("goodsPassport", class="WoojinGoodsBundle:GoodsPassport")
     * @Method("GET")
     *
     * @ApiDoc(
     *  resource=true,
     *  description="根據傳入的 id 取得單一指定商品(goodsPassport)",
     *  requirements={{"name"="id", "dataType"="integer", "required"=true, "description"="商品的 id "}},
     *  statusCodes={
     *    200="Returned when successful",
     *    404={
     *     "Returned when something else is not found"
     *    },
     *    500={
     *     "Please contact author to fix it"
     *    }
     *  }
     * )
     */
    public function showAction(GoodsPassport $goodsPassport)
    {
        $serializer = \JMS\Serializer\SerializerBuilder::create()->build();
        
        $jsonGoodsPassport = $serializer->serialize($goodsPassport, 'json');

        return new Response($jsonGoodsPassport);
    }

    /**
     * 修改商品
     * 
     * @Route("/{id}", requirements={"id" = "\d+"}, name="api_goodsPassport_update", options={"expose"=true})
     * @ParamConverter("goodsPassport", class="WoojinGoodsBundle:GoodsPassport")
     * @Method("PUT")
     * 
     * @ApiDoc(
     *  resource=true,
     *  description="更新指定(id)商品",
     *  requirements={{"name"="id", "dataType"="integer", "required"=true, "description"="商品的 id "}},
     *  statusCodes={
     *    200="Returned when successful",
     *    404={
     *     "Returned when something else is not found"
     *    },
     *    500={
     *     "Please contact author to fix it"
     *    }
     *  }
     * )
     */
    public function updateAction(GoodsPassport $goodsPassport, Request $request)
    {
        /**
         * 這個工廠將會替我們創建新的商品實體
         * @var object
         */
        $GoodsFactory = $this->get('goods.factory');

        /**
         * 提供給工廠的參數陣列
         * @var array
         */
        $settings = array();

        /**
         * Symfony 的屬性套件，透過它可以用物件方式讀寫陣列
         * @var object
         */
        $accessor = PropertyAccess::createPropertyAccessor();

        /**
         * 目前使用者的所屬商店
         * @var object
         */
        $store = $this->get('security.context')->getToken()->getUser()->getStore();

        /**
         * serializer
         * @var object
         */
        $serializer = \JMS\Serializer\SerializerBuilder::create()->build();

        $accessor->setValue($settings, '[setName]', $request->request->get('name'));
        // $accessor->setValue($settings, '[setInType]', $request->request->get('inType')); 不可更改進貨類型
        $accessor->setValue($settings, '[setDpo]', $request->request->get('dpo'));
        $accessor->setValue($settings, '[setPrice]', $request->request->get('price'));
        $accessor->setValue($settings, '[setFakePrice]', $request->request->get('fake_price'));
        $accessor->setValue($settings, '[setCost]', $request->request->get('cost'));
        $accessor->setValue($settings, '[setOrgSn]', $request->request->get('org_sn'));
        $accessor->setValue($settings, '[setBrandSn]', $request->request->get('brand_sn'));
        $accessor->setValue($settings, '[setDes]', $request->request->get('des'));
        $accessor->setValue($settings, '[setMemo]', $request->request->get('memo'));
        $accessor->setValue($settings, '[setIsWeb]', $request->request->get('is_web'));
        $accessor->setValue($settings, '[setPurchaseAt]', new \DateTime($request->request->get('purchase_at')));
        $accessor->setValue($settings, '[setExpirateAt]', new \DateTime($request->request->get('expirate_at')));
        $accessor->setValue($settings, '[setAllowDiscount]', $request->request->get('allow_discount'));
        $accessor->setValue($settings, '[setBrand]', $this->getDoctrine()->getRepository('WoojinGoodsBundle:Brand')->find($request->request->get('brand', self::NONE_ENTITY)));
        $accessor->setValue($settings, '[setColor]', $this->getDoctrine()->getRepository('WoojinGoodsBundle:Color')->find($request->request->get('color', self::NONE_ENTITY)));
        $accessor->setValue($settings, '[setPattern]', $this->getDoctrine()->getRepository('WoojinGoodsBundle:Pattern')->find($request->request->get('pattern', self::NONE_ENTITY)));
        $accessor->setValue($settings, '[setLevel]', $this->getDoctrine()->getRepository('WoojinGoodsBundle:GoodsLevel')->find($request->request->get('level', self::NONE_ENTITY)));
        $accessor->setValue($settings, '[setSource]', $this->getDoctrine()->getRepository('WoojinGoodsBundle:GoodsSource')->find($request->request->get('source', self::NONE_ENTITY)));
        $accessor->setValue($settings, '[setMt]', $this->getDoctrine()->getRepository('WoojinGoodsBundle:GoodsMT')->find($request->request->get('mt', self::NONE_ENTITY)));
        $accessor->setValue($settings, '[setSupplier]', $this->getDoctrine()->getRepository('WoojinGoodsBundle:Supplier')->find($request->request->get('supplier', self::NONE_ENTITY)));
        $accessor->setValue($settings, '[setStatus]', $this->getDoctrine()->getRepository('WoojinGoodsBundle:GoodsStatus')->find($request->request->get('status', self::GS_ON_SALE)));
        //$accessor->setValue($settings, '[setStore]', $store); // 不可直接修改所屬店，需透過調貨
        
        // 若是404表示移除圖片，需要更新，其他的動作都交給ImgController.php，
        // 這邊會這樣處理的原因是，移除圖片本身沒有檔案上傳，所以ImgController.php 不會被呼叫，
        // 連帶的商品的圖片路徑也不會被ImgController.php 修改，因此在商品資訊更新就要自行先處理，
        // 而如果確實有修改圖片且上傳的話，就不需要進行此動作
        if ($request->request->get('imgpath') === '/img/404.png') {
            $accessor->setValue($settings, '[setImgpath]', '/img/404.png');
        } 

        $jsonGoodsPassports = $serializer->serialize($GoodsFactory->update($settings, $goodsPassport), 'json');

        return new Response($jsonGoodsPassports);
    }

    /**
     * 新增商品，為了讓controller乾淨點，做一個商品工廠來處理產生新的商品的動作，
     * controller只處理傳入的參數之檢查以及最後回傳序列商品實體之json字串
     * 
     * @Route("", name="api_goodsPassport_create", options={"expose"=true})
     * @Method("POST")
     * 
     * @ApiDoc(
     *  resource=true,
     *  description="新增商品",
     *  statusCodes={
     *    200="Returned when successful",
     *    404={
     *     "Returned when something else is not found"
     *    },
     *    500={
     *     "Please contact author to fix it"
     *    }
     *  }
     * )
     */
    public function createAction(Request $request)
    {
        /**
         * 這個工廠將會替我們創建新的商品實體
         * @var object
         */
        $GoodsFactory = $this->get('goods.factory');

        /**
         * 提供給工廠的參數陣列
         * @var array
         */
        $settings = array();

        /**
         * Symfony 的屬性套件，透過它可以用物件方式讀寫陣列
         * @var object
         */
        $accessor = PropertyAccess::createPropertyAccessor();

        /**
         * 目前使用者的所屬商店
         * @var object
         */
        $store = $this->get('security.context')->getToken()->getUser()->getStore();

        /**
         * serializer
         * @var object
         */
        $serializer = \JMS\Serializer\SerializerBuilder::create()->build();

        $accessor->setValue($settings, '[setName]', $request->request->get('name'));
        $accessor->setValue($settings, '[setInType]', $request->request->get('in_type'));
        $accessor->setValue($settings, '[setDpo]', $request->request->get('dpo'));
        $accessor->setValue($settings, '[setPrice]', $request->request->get('price'));
        $accessor->setValue($settings, '[setFakePrice]', $request->request->get('fake_price'));
        $accessor->setValue($settings, '[setCost]', $request->request->get('cost'));
        $accessor->setValue($settings, '[setOrgSn]', $request->request->get('org_sn'));
        $accessor->setValue($settings, '[setBrandSn]', $request->request->get('brand_sn'));
        $accessor->setValue($settings, '[setDes]', $request->request->get('des'));
        $accessor->setValue($settings, '[setMemo]', $request->request->get('memo'));
        $accessor->setValue($settings, '[setIsWeb]', $request->request->get('is_web'));
        $accessor->setValue($settings, '[setPurchaseAt]', new \DateTime($request->request->get('purchase_at')));
        $accessor->setValue($settings, '[setExpirateAt]', new \DateTime($request->request->get('expirate_at')));
        $accessor->setValue($settings, '[setAllowDiscount]', $request->request->get('allow_discount'));
        $accessor->setValue($settings, '[setBrand]', $this->getDoctrine()->getRepository('WoojinGoodsBundle:Brand')->find($request->request->get('brand', self::NONE_ENTITY)));
        $accessor->setValue($settings, '[setColor]', $this->getDoctrine()->getRepository('WoojinGoodsBundle:Color')->find($request->request->get('color', self::NONE_ENTITY)));
        $accessor->setValue($settings, '[setPattern]', $this->getDoctrine()->getRepository('WoojinGoodsBundle:Pattern')->find($request->request->get('pattern', self::NONE_ENTITY)));
        $accessor->setValue($settings, '[setLevel]', $this->getDoctrine()->getRepository('WoojinGoodsBundle:GoodsLevel')->find($request->request->get('level', self::NONE_ENTITY)));
        $accessor->setValue($settings, '[setSource]', $this->getDoctrine()->getRepository('WoojinGoodsBundle:GoodsSource')->find($request->request->get('source', self::NONE_ENTITY)));
        $accessor->setValue($settings, '[setMt]', $this->getDoctrine()->getRepository('WoojinGoodsBundle:GoodsMT')->find($request->request->get('mt', self::NONE_ENTITY)));
        $accessor->setValue($settings, '[setSupplier]', $this->getDoctrine()->getRepository('WoojinGoodsBundle:Supplier')->find($request->request->get('supplier', self::NONE_ENTITY)));
        $accessor->setValue($settings, '[setStatus]', $this->getDoctrine()->getRepository('WoojinGoodsBundle:GoodsStatus')->find($request->request->get('status', self::GS_ON_SALE)));
        $accessor->setValue($settings, '[setStore]', $store); 
        $accessor->setValue($settings, '[setImgpath]', self::NO_IMG); 
        $accessor->setValue($settings, '[amount]', $request->request->get('amount')); 

        $jsonGoodsPassports = $serializer->serialize($GoodsFactory->create($settings), 'json');

        return new Response($jsonGoodsPassports);
    }

    /**
     * 刪除商品
     * 
     * 
     * @Route("/{id}", requirements={"id" = "\d+"}, name="api_goodsPassport_delete", options={"expose"=true})
     * @ParamConverter("goodsPassport", class="WoojinGoodsBundle:GoodsPassport")
     * @Method("DELETE")
     * 
     * @ApiDoc(
     *  resource=true,
     *  description="刪除指定(id)商品",
     *  requirements={{"name"="id", "dataType"="integer", "required"=true, "description"="商品的 id "}},
     *  statusCodes={
     *    200="Returned when successful",
     *    404={
     *     "Returned when something else is not found"
     *    },
     *    500={
     *     "Please contact author to fix it"
     *    }
     *  }
     * )
     */
    public function destroyAction(GoodsPassport $goodsPassport)
    {
        try {
            $em = $this->getDoctrine()->getManager();
            $em->remove($goodsPassport);
            $em->flush();

            /**
             * 回傳訊息
             * @var array
             */
            $returnMsg = array('status' => 'OK');

            return new Response(json_encode($returnMsg));
        } catch (Exception $e) {
            throw $e;
        }
    }

    /**
     * 還原批次上傳, 傳入參數 $jsonIds ( javasript 陣列組成的json 字串)，系統會將符合的商品實體連同訂單以及操作記錄一同刪除
     * 
     * @Route("/import/{jsonIds}", name="api_goodsPassport_reverse", options={"expose"=true})
     * @Method("DELETE")
     * 
     */
    public function reverseAction(Request $request, $jsonIds)
    {
        /**
         * 從post中取出 id 陣列
         * 
         * @var array
         */
        $ids = json_decode($jsonIds, true);

        /**
         * 商品實體陣列
         * 
         * @var array{object}
         */
        $goodsGroup = $this->getDoctrine()->getRepository('WoojinGoodsBundle:GoodsPassport')->findByIds($ids);


        try {
            $em = $this->getDoctrine()->getManager();
            
            foreach ($goodsGroup as $goods) {
                $em->remove($goods);
            }

            $em->flush();

            /**
             * 回傳訊息
             * @var array
             */
            $returnMsg = array('status' => 'OK');

            return new Response(json_encode($returnMsg));
        } catch (Exception $e) {
            throw $e;
        }
    }

    /**
     * 取得商品匯出excel
     * 
     * @Route("/export/{jsonCondition}", name="api_goodsPassport_export",options={"expose"=true})
     * defaults={"jsonCondition"="{}"},
     * @Method("GET")
     *
     * @ApiDoc(
     *  resource=true,
     *  description="匯出檔案",
     *  statusCodes={
     *    200="Returned when successful",
     *    403="Returned when the ApiKey is not matched to say hello",
     *    404={
     *     "Returned when the ApiKey is not matched",
     *     "Returned when something else is not found"
     *    },
     *    500={
     *     "Please contact author to fix it"
     *    }
     *  }
     * )
     */
    public function exportAction($jsonCondition)
    {
        /**
         * 使用者
         * 
         * @var object
         */
        $user = $this->get('security.context')->getToken()->getUser();

        /**
         * 將搜尋條件的 json 字串轉換成搜尋陣列
         * 
         * @var array
         */
        $conditions = json_decode($jsonCondition, true);

        /**
         * 商品repo
         * 
         * @var object
         */
        $goodsRepo = $this->getDoctrine()->getRepository('WoojinGoodsBundle:GoodsPassport');

        /**
         * 取得的商品資料
         * 
         * @var array(object)
         */
        $goods = $goodsRepo->findByFilter($conditions);

        /**
         * phpExcel Service
         * 
         * @var object
         */
        $phpExcelObject = $this->get('phpexcel')->createPHPExcelObject();

        // 設置excel 的 一些meta資訊
        $phpExcelObject->getProperties()
            ->setCreator('ReebonzSystem')
            ->setLastModifiedBy($user->getUsername())
            ->setTitle('商品報表')
            ->setSubject('商品報表')
            ->setDescription('根據傳入條件匯出excel商品報表')
            ->setKeywords('Reebonz Export')
            ->setCategory('Goods');

        // 設置各欄位名稱
        $phpExcelObject->setActiveSheetIndex(0)
            ->setCellValue('A1', '部門*')
            ->setCellValue('B1', '進貨日*')
            ->setCellValue('C1', '到期日')
            ->setCellValue('D1', '廠商*')
            ->setCellValue('E1', '品牌*')
            ->setCellValue('F1', 'SKU') // 廠商型號
            ->setCellValue('G1', '商品描述*') // 商品名稱
            ->setCellValue('H1', '款式*')
            ->setCellValue('I1', '花色*')
            ->setCellValue('J1', '商品狀況')
            ->setCellValue('K1', 'DPO#') // 系統內部編號
            ->setCellValue('L1', '單價成本*(含稅)')
            ->setCellValue('M1', '市價')
            ->setCellValue('N1', '優惠價*') // 真實顯示價格為此
            ->setCellValue('O1', '備註')
            ->setCellValue('P1', '允許折扣')// {0: 否,1: 是}
            ->setCellValue('Q1', '允許網路販售')// {0: 否,1: 是}
            ->setCellValue('R1', '進貨類型') // {0: 否,1: 是}
            ->setCellValue('S1', '對應圖片') // 請將對應的圖片丟到 /img/yy-mm-dd/ 裡
            ->setCellValue('T1', '狀態')
            ->setCellValue('U1', '產編')
        ;

        // 迭代商品陣列，逐行->逐格填入對應資訊
        foreach ($goods as $key => $eachOne) {

            $phpExcelObject->setActiveSheetIndex(0)
                ->setCellValue('A' . ($key + 2), substr($eachOne->getSn(), 0, 1))
                ->setCellValue('B' . ($key + 2), (is_object($purchaseAt = $eachOne->getPurchaseAt())) ? $purchaseAt->format('Y-m-d') : '')
                ->setCellValue('C' . ($key + 2), (is_object($expirateAt = $eachOne->getExpirateAt())) ? $expirateAt->format('Y-m-d') : '')
                ->setCellValue('D' . ($key + 2), (is_object($supplier = $eachOne->getSupplier())) ? $supplier->getName() : '')
                ->setCellValue('E' . ($key + 2), (is_object($brand = $eachOne->getBrand())) ? $brand->getName() : '')
                ->setCellValue('F' . ($key + 2), $eachOne->getOrgSn()) // 廠商型號
                ->setCellValue('G' . ($key + 2), $eachOne->getName()) 
                ->setCellValue('H' . ($key + 2), (is_object($pattern = $eachOne->getPattern())) ? $pattern->getName() : '')
                ->setCellValue('I' . ($key + 2), (is_object($color = $eachOne->getColor())) ? $color->getName() : '')
                ->setCellValue('J' . ($key + 2), (is_object($status = $eachOne->getStatus())) ? $status->getName() : '')
                ->setCellValue('K' . ($key + 2), $eachOne->getDpo()) // 系統內部編號
                ->setCellValue('L' . ($key + 2), $eachOne->getCost())
                ->setCellValue('M' . ($key + 2), $eachOne->getFakePrice()) // 市場價
                ->setCellValue('N' . ($key + 2), $eachOne->getPrice()) // 真實顯示價格為此
                ->setCellValue('O' . ($key + 2), $eachOne->getMemo())
                ->setCellValue('P' . ($key + 2), ($eachOne->getAllowDiscount()) ? '是' : '否')// {0: 否,1: 是}
                ->setCellValue('Q' . ($key + 2), ($eachOne->getIsWeb()) ? '是' : '否' )// {0: 否,1: 是}
                ->setCellValue('R' . ($key + 2), $this->getInTypeCellValue($eachOne)) // {0: 否,1: 是}
                ->setCellValue('S' . ($key + 2), $eachOne->getImgpath()) // 請將對應的圖片丟到 /img/yy-mm-dd/ 裡
                ->setCellValue('T' . ($key + 2), $eachOne->getStatus()->getName())
                ->setCellValue('U' . ($key + 2), $eachOne->getSn())
            ;
        }

        $phpExcelObject->getActiveSheet()->setTitle('報表');
        
        // Set active sheet index to the first sheet, so Excel opens this as the first sheet
        $phpExcelObject->setActiveSheetIndex(0);

        // create the writer
        $writer = $this->get('phpexcel')->createWriter($phpExcelObject, 'Excel2007');
        
        // create the response
        $response = $this->get('phpexcel')->createStreamedResponse($writer);
        
        // adding headers
        $response->headers->set('Content-Type', 'text/vnd.ms-excel; charset=utf-8');
        $response->headers->set('Content-Disposition', 'attachment;filename=goods_export.xlsx');
        $response->headers->set('Pragma', 'public');
        $response->headers->set('Cache-Control', 'maxage=1');

        return $response;        
    }

    /**
     * 匯入excel批次上傳商品
     *
     *  1. 取得上傳檔案
     *  2. 檔名加密移動到指定位置 /upload/userId/md5(date("Y-m-d")).xlsx
     *  3. 取得phpExcel service
     *  4. 透過service 讀取上傳的excel 檔案
     *  5. 根據row, cell兩層迭代上傳
     *  6. 不自動進行關連實體新增，避免後設資料混亂
     *  7. 序列化上傳的商品實體
     *  8. 回傳
     * 
     * @Route("/import", name="api_goodsPassport_import",options={"expose"=true})
     * @Method("POST")
     *
     * @ApiDoc(
     *  resource=true,
     *  description="批次上傳",
     *  statusCodes={
     *    200="Returned when successful",
     *    403="Returned when the ApiKey is not matched to say hello",
     *    404={
     *     "Returned when the ApiKey is not matched",
     *     "Returned when something else is not found"
     *    },
     *    500={
     *     "Please contact author to fix it"
     *    }
     *  }
     * )
     */
    public function importAction(Request $request)
    {
        /**
         * 這個工廠將會替我們創建新的商品實體
         * @var object
         */
        $GoodsFactory = $this->get('goods.factory');

        /**
         * 取得新增後的商品
         * 
         * @var array{object}
         */
        $newGoods = array();

        /**
         * 新增成功的商品實體陣列
         * 
         * @var array{object}
         */
        $goodsCollection = array();

        /**
         * serializer
         * @var object
         */
        $serializer = \JMS\Serializer\SerializerBuilder::create()->build();

        /**
         * $goodsCollection 序列化後的json字串
         * 
         * @var string
         */
        $jsonGoods = null;

        /**
         * 提供給工廠的參數陣列，在此Action裡該參數會隨著每行迭代重新刷新
         * 
         * @var array
         */
        $settings = array();

        /**
         * 取得檔案
         * 
         * @var object
         */
        $files = $request->files->get('file');

        // 無上傳檔案則不動作
        if (!$files->isValid()) {
            return new Response('');
        }

        /**
         * 取得目前使用者
         * 
         * @var object
         */
        $user = $this->get('security.context')->getToken()->getUser();

        /**
         * 資料夾絕對路徑
         * 
         * @var string
         */
        $filepath = $request->server->get('DOCUMENT_ROOT') . '/uploads/'. $user->getId();

        /**
         * 檔案名稱
         * 
         * @var string
         */
        $fileName = md5('import_file' . date('H:i:s')) . 'xlsx';

        // 移動檔案
        $files->move($filepath, $fileName);

        /**
         * phpExcel Service
         * 
         * @var object
         */
        $phpExcelObject = $this->get('phpexcel')->createPHPExcelObject();


        /**
         * 讀取前面上傳的excel檔案，並轉換成物件
         * 
         * @var object
         */
        $excelObj = \PHPExcel_IOFactory::load($filepath . '/' . $fileName);

        // 檔案讀取完立刻刪除
        @unlink($filepath . '/' . $fileName);

        /**
         * Worksheet
         * 
         * @var object
         */
        $workSheet = $excelObj->getActiveSheet();

        /**
         * 取得所有的 row 
         * 
         * @var array
         */
        $rows = $workSheet->getRowIterator();

        /**
         * Symfony 的屬性套件，透過它可以用物件方式讀寫陣列
         * @var object
         */
        $accessor = PropertyAccess::createPropertyAccessor();

        /**
         * 欄位順位-資料屬性對應陣列，
         * 會在迭代 $rows 的第一行時組成
         * 
         * @var array
         */
        $mapping = array();

        /**
         * 中文欄位名稱轉換為英文陣列鍵名之對應陣列
         * 
         * @var array
         */
        $translates = array(
            'setStore' => '部門*',
            'setPurchaseAt' => '進貨日*', 
            'setExpirateAt' => '到期日',
            'setSupplier' => '廠商*', 
            'setBrand' => '品牌*', 
            'setOrgSn' => 'SKU',
            'setName' => '商品描述*', 
            'setPattern' => '款式*', 
            'setMt' => '材質*',
            'setColor' => '花色*',
            'setStatus' => '商品狀況', 
            'setDpo' => 'DPO#', 
            'setCost' => '單價成本*(含稅)',
            'amount' => '數量*', 
            'setFakePrice' => '市價', 
            'setPrice' => '優惠價*',
            'setMemo' => '備註', 
            'setAllowDiscount' => '允許折扣', 
            'setIsWeb' => '允許網路販售',
            'setImgpath' => '對應圖片',
            'email' => '客戶信箱'
        );

        /**
         * 某列的所有格子
         * 
         * @var object
         */
        $cells = array();
        
        // 進行迭代，注意 $rowNum 是從 1 開始算，非一般我們習慣的0
        foreach ($rows as $rowNum => $row) {         
            // 取得本列的所有格子
            $cells = $row->getCellIterator();

            // true: 空的格子一樣要迭代
            $cells->setIterateOnlyExistingCells(false);

            // 第一行是欄位名稱，用來形成 key-attribute 的 mapping
            if ($rowNum === self::ROW_START) {
                // $cellNum 是從0開始，別和 $rowNum 搞混了
                foreach ($cells as $cellNum => $cell) {
                    // 如果沒有對應的動作，則直接進行下一次迭代
                    if (!$tmpAct = array_search(trim($cell), $translates)) {
                        continue;
                    }

                    // 根據欄位內容組成 mapping
                    $accessor->setValue($mapping, '[' . $cellNum . ']', $tmpAct);                         
                }   
               
                continue;
            }

            // 檢查mapping 有無 setAllowDiscount 方法, 若為沒有則添加
            if (!$allowDiscount = array_search('setAllowDiscount', $mapping)) {
                $accessor->setValue($mapping, '[' . count($mapping) . ']', 'setAllowDiscount'); 
            }

            // 檢查mapping 有無 setInType 方法, 若為沒有則添加
            if (!$allowDiscount = array_search('setInType', $mapping)) {
                $accessor->setValue($mapping, '[' . count($mapping) . ']', 'setInType'); 
            }

            // 檢查mapping 有無 setIsWeb 方法, 若為沒有則添加
            if (!$allowDiscount = array_search('setIsWeb', $mapping)) {
                $accessor->setValue($mapping, '[' . count($mapping) . ']', 'setIsWeb'); 
            }

            // 檢查mapping 有無 setStore 方法, 若為沒有則添加
            if (!$allowDiscount = array_search('setStore', $mapping)) {
                $accessor->setValue($mapping, '[' . count($mapping) . ']', 'setStore'); 
            }
            
            // 迭代並且進行新增資料實體的動作
            foreach ($cells as $cellNum => $cell) {
                // 如果是客戶信箱，要在 request 添加 email，這在 order 產生時會用到，
                // 又因為有客戶信箱表示為寄賣商品，所以要把 settings 的 setInType 設置為 1
                if (isset($mapping[$cellNum])) {
                    if ($mapping[$cellNum] === 'email') {
                        // request 參數 email 設置，訂單會用到此參數
                        $request->request->set('email', $cell);
                        
                        // 進貨類型設置為寄賣
                        $accessor->setValue($settings, '[setInType]', 1);
                    } else {
                        // 設置參數陣列
                        $accessor->setValue($settings, '[' . $mapping[$cellNum] . ']', $this->getSettingsVal($mapping[$cellNum], $cell));
                    }
                }
            }

            // 設置商品狀態為上架，強制規定，不開放批次上傳欄位擇定商品狀態，可能會造成系統業務邏輯混亂
            $accessor->setValue($settings, '[setStatus]', $this->getSettingsVal('setStatus', self::GS_ON_SALE));

            // 如果沒有設置圖片路徑，則給予404png 的路徑，null 不符合原本的邏輯
            if (!$accessor->getValue($settings, '[setImgpath]')) {
                $accessor->setValue($settings, '[setImgpath]', self::NO_IMG);
            }

            // 如果沒有設置允許折扣，預設給1表示允許
            if (!$accessor->getValue($settings, '[setAllowDiscount]')) {
                $accessor->setValue($settings, '[setAllowDiscount]', self::IS_ALLOW);
            }

            // 如果沒有設置允許折扣，預設給0表示允許
            if (!$accessor->getValue($settings, '[setInType]')) {
                $accessor->setValue($settings, '[setInType]', self::IS_NORMAL);
            }

            // 如果沒有設置允許折扣，預設給1表示允許
            if (!$accessor->getValue($settings, '[setIsWeb]')) {
                $accessor->setValue($settings, '[setIsWeb]', self::IS_ALLOW);
            }

            // 如果沒有設置商店，預設使用目前使用者的所屬店
            if (!$accessor->getValue($settings, '[setStore]')) {
                $accessor->setValue($settings, '[setStore]', $this->getSettingsVal('setStore', $user->getStore()->getSn()));
            }

            // 將參數丟給工廠產生商品實體
            $newGoods = $GoodsFactory->create($settings);

            // 迭代加入 goodsCollection
            foreach ($newGoods as $eachNew) {
                array_push($goodsCollection, $eachNew);
            }

            // 清空設定參數陣列
            $settings = array();
        }

        // 序列化 $goodsCollection 丟回給前端讓 angular 去 pharse
        $jsonGoods = $serializer->serialize($goodsCollection, 'json');

        return new Response($jsonGoods);
    }

    public function moveAction()
    {

    }

    public function backAction()
    {

    }

    public function sellActivityAction()
    {

    }

    /**
     * 取得 excel 進貨類型那一格的值
     * 
     * @param  object $goods
     * @return string
     */
    protected function getInTypeCellValue($goods)
    {
        /**
         * 進貨類型
         * 
         * @var string
         */
        $inType = null;
        
        /**
         * 搜尋條件
         * 
         * @var array
         */
        $condition = array('goods_passport' => $goods->getId(), 'orders_kind' => self::OK_CONSIGN_IN);

        // 判斷進貨類型為何, 若為true 則是寄賣貨物，需要找出關連的訂單和客戶
        if ($goods->getInType()) {
            /**
             * 寄賣進貨訂單實體
             * 
             * @var object
             */
            $order = $this->getDoctrine()->getRepository('WoojinOrderBundle:Orders')->findOneBy($condition);

            /**
             * 寄賣訂單的客戶實體
             * 
             * @var object
             */
            $custom = $order->getCustom();

            $inType = '寄賣  ' . $custom->getEmail() . '[' . $custom->getName() . $custom->getSex() . ']';
        }

        return (is_null($inType)) ? '一般' : $inType;
    }

    /**
     * 組成設定參數陣列
     *
     * @param   [string] $act 
     * @param   [string] $arg
     * @return  [string|integer|object]
     */
    protected function getSettingsVal($act, $arg)
    {
        /**
         * 回傳值
         * 
         * @var [string|integer|object]
         */
        $return = null;

        switch ($act)
        {
            case 'setPurchaseAt':
            case 'setExpirateAt':

                $return = new \DateTime(date('Y-m-d', strtotime($arg)));

                break;

            case 'setSupplier':

                $return = $this->getDoctrine()->getRepository('WoojinGoodsBundle:Supplier')->findOneBy((is_numeric($arg)) ? array('id' => $arg) : array('name' => $arg));

                break;

            case 'setBrand':

                $return = $this->getDoctrine()->getRepository('WoojinGoodsBundle:Brand')->findOneBy((is_numeric($arg)) ? array('id' => $arg) : array('name' => $arg));

                break;
            
            case 'setPattern':

                $return = $this->getDoctrine()->getRepository('WoojinGoodsBundle:Pattern')->findOneBy((is_numeric($arg)) ? array('id' => $arg) : array('name' => $arg));

                break;
            
            case 'setColor':

                $return = $this->getDoctrine()->getRepository('WoojinGoodsBundle:Color')->findOneBy((is_numeric($arg)) ? array('id' => $arg) : array('name' => $arg));

                break;
            
            case 'setStatus':

                $return = $this->getDoctrine()->getRepository('WoojinGoodsBundle:GoodsStatus')->findOneBy((is_numeric($arg)) ? array('id' => $arg) : array('name' => $arg));

                break;

            case 'setMT':

                $return = $this->getDoctrine()->getRepository('WoojinGoodsBundle:GoodsMT')->findOneBy((is_numeric($arg)) ? array('id' => $arg) : array('name' => $arg));

                break;
            
            
            case 'setSource':

                $return = $this->getDoctrine()->getRepository('WoojinGoodsBundle:GoodsSource')->findOneBy((is_numeric($arg)) ? array('id' => $arg) : array('name' => $arg));

                break;
            
            case 'setStore':

                $return = $this->getDoctrine()->getRepository('WoojinStoreBundle:Store')->findOneBy((is_numeric($arg)) ? array('id' => $arg) : array('sn' => $arg));

                break;

            case 'setOrgSn':    
            case 'setName':
            case 'setDpo':
            case 'setCost':
            case 'amount':
            case 'setFakePrice':
            case 'setPrice':
            case 'setBrandSn':            
            case 'setDes':
            case 'setMemo':
            case 'setAllowDiscount':
            case 'setIsWeb':
            case 'setInType':
            case 'setImgpath':

                $return = strval($arg);

                break;
            
            default:
                break;
        }

        return $return;
    }
}
