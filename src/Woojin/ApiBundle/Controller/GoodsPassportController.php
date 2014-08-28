<?php

namespace Woojin\ApiBundle\Controller;

//Third Party
use Nelmio\ApiDocBundle\Annotation\ApiDoc;

//Component
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PropertyAccess\PropertyAccess;

//Default
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
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
    const API_KEY = '17201810cc';

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
     * 根據搜尋條件取得商品列表，將搜尋條件以json 格式傳入，其格式為 
     * {
     *     attr: {
     *         type: array
     *     }
     * }
     * 
     * 如下:
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
         * 
         * @var array
         */
        $conditions = json_decode($jsonCondition, true);

        /**
         * 排序參數
         *
         * #Example: 
         * 
         * array('attr' => xxx, 'dir' => 'ASC');
         * 
         * @var array
         */
        $orderBy = json_decode($jsonOrderBy, true);

        /**
         * serializer
         * 
         * @var object
         */
        $serializer = \JMS\Serializer\SerializerBuilder::create()->build();

        /**
         * 商品repo
         * 
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
         * Entity Manager
         * 
         * @var object
         */
        $em = $this->getDoctrine()->getManager();

        /**
         * Symfony 的屬性套件，透過它可以用物件方式讀寫陣列
         * @var object
         */
        $accessor = PropertyAccess::createPropertyAccessor();

        /**
         * serializer
         * @var object
         */
        $serializer = \JMS\Serializer\SerializerBuilder::create()->build();

        /**
         * 商品屬性設定者，專責處理傳入工廠的 $settings
         * 
         * @var \Woojin\GoodsBundle\GoodsSetter
         */
        $GoodsSetter = $this->get('goods.setter');

        /**
         * 這個工廠將會替我們創建新的商品實體
         * @var object
         */
        $GoodsFactory = $this->get('goods.factory');

        /**
         * 提供給工廠的參數陣列
         * 
         * @var array
         */
        $settings = $GoodsSetter->setCreateSettings($accessor, $request, $em)->getSettings();

        /**
         * 商品實體
         * 
         * @var [\Woojin\GoodsBundle\Entity\GoodsPassport]
         */
        $goods = $GoodsFactory->create($settings);

        /**
         * $jsonGoodsPassports
         * @var [string] (json)
         */
        $jsonGoodsPassports = $serializer->serialize($goods, 'json');

        return new Response($jsonGoodsPassports);
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
         * Entity Manager
         * 
         * @var object
         */
        $em = $this->getDoctrine()->getManager();

        /**
         * Symfony 的屬性套件，透過它可以用物件方式讀寫陣列
         * @var object
         */
        $accessor = PropertyAccess::createPropertyAccessor();

        /**
         * serializer
         * @var object
         */
        $serializer = \JMS\Serializer\SerializerBuilder::create()->build();

        /**
         * 商品屬性設定者，專責處理傳入工廠的 $settings
         * 
         * @var \Woojin\GoodsBundle\GoodsSetter
         */
        $GoodsSetter = $this->get('goods.setter');

        /**
         * 這個工廠將會替我們創建新的商品實體
         * @var object
         */
        $GoodsFactory = $this->get('goods.factory');

        /**
         * 提供給工廠的參數陣列
         * 
         * @var array
         */
        $settings = $GoodsSetter->setUpdateSettings($accessor, $request, $em)->getSettings();
        
        $jsonGoodsPassports = $serializer->serialize($GoodsFactory->update($settings, $goodsPassport), 'json');

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
        } catch (\Exception $e) {
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
        } catch (\Exception $e) {
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
        set_time_limit(0);
        ini_set('memory_limit','512M');

        /**
         * 將搜尋條件的 json 字串轉換成搜尋陣列
         * 
         * @var array
         */
        $conditions = json_decode($jsonCondition, true);

        /**
         * 商品匯出報表物件
         * 
         * @var \Woojin\GoodsBundle\GoodsExporter
         */
        $GoodsExporter = $this->get('goods.exporter');

        /**
         * 取得的商品資料
         * 
         * @var array(object)
         */
        $goodsGroup = $this->getDoctrine()->getRepository('WoojinGoodsBundle:GoodsPassport')->findByFilter($conditions);

        /**
         * Response to Client
         * 
         * @var [object]
         */
        $response = $GoodsExporter->run($goodsGroup); 

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
     *  8. 回傳 json 格式字串
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
        // 接下來的動作是非常吃資源的!!
        set_time_limit(0);
        ini_set('memory_limit','512M');

        /**
         * serializer
         * @var object
         */
        $serializer = \JMS\Serializer\SerializerBuilder::create()->build();

        /**
         * 商品資料上傳用物件
         * 
         * @var \Woojin\GoodsBundle\Importer\GoodsImporter
         */
        $importer = $this->get('goods.importer');
        
        /**
         * 新增成功的商品實體陣列
         * 
         * @var array{object}
         */
        $goodsCollection = $importer->run($request)->getGoodsCollection();

        /**
         * $goodsCollection 序列化後的json字串
         * 
         * @var string
         */
        $jsonGoods = $serializer->serialize($goodsCollection, 'json');

        return new Response($jsonGoods);
    }

    /**
     * 我的懶人刪除
     * 
     * @Route("/batch/remove/{apiKey}/{id}/jocoonopa", name="api_goodsPassport_batch_remove", options={"expose"=true})
     * @Method("DELETE")
     * @ApiDoc(
     *  resource=true,
     *  description="懶人刪除, ~~~~",
     *  requirements={{"name"="apiKey", "dataType"="string", "required"=true, "description"="懶人刪除, ~~~~"}},
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
     * 
     */
    public function batchRemove($apiKey, $id)
    {
        // 檢查 apiKey 是否正確
        if ($apiKey !== self::API_KEY) {
            throw new \Exception ('Wrong Api Key');
        }

        try {
            /**
             * Entity Manager
             * 
             * @var object
             */
            $em = $this->getDoctrine()->getManager();
            
             /**
             * 商品物件陣列
             * 
             * @var array[\Woojin\GoodsBundle\Entity\GoodsPassport]
             */
            $goodsGroup = $em->getRepository('WoojinGoodsBundle:GoodsPassport')->findBy(array('supplier' => $id));

            // 移除屬於指定所屬店的所有商品
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
        } catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * 我的懶人修復產編
     * 
     * @Route("/batch/repair/{apiKey}/jocoonopa", name="api_goodsPassport_batch_repair", options={"expose"=true})
     * @Method("PUT")
     * @ApiDoc(
     *  resource=true,
     *  description="懶人修復產編",
     *  requirements={{"name"="apiKey", "dataType"="string", "required"=true, "description"="懶人修復產編"}},
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
     * 
     */
    public function batchRepair($apiKey)
    {
        // 檢查 apiKey 是否正確
        if ($apiKey !== self::API_KEY) {
            throw new \Exception ('Wrong Api Key');
        }

        set_time_limit(0);
        ini_set('memory_limit','512M');

        try {
            /**
             * Entity Manager
             * 
             * @var object
             */
            $em = $this->getDoctrine()->getManager();
            
            /**
             * 商品物件陣列
             * 
             * @var array[\Woojin\GoodsBundle\Entity\GoodsPassport]
             */
            $goodsGroup = $em->getRepository('WoojinGoodsBundle:GoodsPassport')->findAll();

            // 對商品們做一個假更新動作，觸發 Prepesist && PreUpdate 事件達到更新產編的效果
            array_walk($goodsGroup, function ($goods) use (&$em) {
                $goods->setBrandSn(' ');
                $em->persist($goods);
            });
            
            $em->flush();

            /**
             * 回傳訊息
             * 
             * @var array
             */
            $returnMsg = array('status' => 'OK');

            return new Response(json_encode($returnMsg));
        } catch (\Exception $e) {
            throw $e;
        }
    }
}
