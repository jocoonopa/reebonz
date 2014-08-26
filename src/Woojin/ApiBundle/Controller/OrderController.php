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
use Woojin\OrderBundle\Entity\Orders;
use Woojin\OrderBundle\Entity\Invoice;

/**
 * 關於 Orders(訂單) 操作
 *
 * @Route("/orders")
 */
class OrderController extends Controller
{
    const GS_ON_SALE        = 1;
    const GS_SOLDOUT        = 2;
    const GS_MOVING         = 3;
    const GS_OFF_SALE       = 4;
    const GS_ACTIVITY       = 6;
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
     * 販售一般商品
     * 
     * @Route("/normal", name="api_orders_normal", options={"expose"=true})
     * @Method("POST")
     *
     * @ApiDoc(
     *  resource=true,
     *  description="一般販售",
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
    public function createNormalAction(Request $request)
    {
        /**
         * Post 過來的商品實體
         * 
         * @var array{object}
         */
        $goodsGroup = $request->request->get('goods');

        /**
         * Entity Manager
         * 
         * @var object
         */
        $em = $this->getDoctrine()->getManager();

        /**
         * Symfony 的屬性套件，透過它可以用物件方式讀寫陣列
         * 
         * @var object
         */
        $accessor = PropertyAccess::createPropertyAccessor();

        /**
         * Post 過來的客戶
         * 
         * @var array
         */
        $postCustom = $request->request->get('custom');

        /**
         * 使用者
         * 
         * @var Woojin\UserBundle\Entity\User
         */
        $user = $this->get('security.context')->getToken()->getUser();

        /**
         * 販售訂單關連的客戶實體
         * 
         * @var object || null
         */
        $custom = $em->getRepository('WoojinOrderBundle:Custom')->findOneBy(array('email'=> $accessor->getValue($postCustom, '[email]')));
        
        /**
         * 發票
         * 
         * @var \Woojin\OrderBundle\Entity\Invoice
         */
        $invoice = $this->genInvoice($user, $custom);

        /**
         * serializer
         * 
         * @var object
         */
        $serializer = \JMS\Serializer\SerializerBuilder::create()->build();

        /**
         * 訂單的實體陣列
         * 
         * @var array{object}
         */
        $ordersRepo = array();

        /**
         * 回傳的銷貨訂單索引陣列
         * 
         * @var array
         */
        $returnOrdersIds = array();

        /**
         * 商品狀態:售出
         * 
         * @var object
         */
        $status = $em->find('WoojinGoodsBundle:GoodsStatus', self::GS_SOLDOUT);

        foreach ($goodsGroup as $eachGoods) {
            /**
             * 逐一透過 post 的商品id取得商品實體
             * 
             * @var \Woojin\GoodsBundle\Entity\GoodsPassport
             */
            $goods = $this->getDoctrine()->getRepository('WoojinGoodsBundle:GoodsPassport')->find($accessor->getValue($eachGoods, '[id]'));
            
            /**
             * 訂單狀態 id ，從 required 和 paid 是否相等判斷，
             * 若相等則為完成狀態 OS_COMPLETE ，不相等則為處理中 OS_HANDLING
             * 
             * @var integer
             */
            $ordersStatusId = ($accessor->getValue($eachGoods, '[orders][required]') === $accessor->getValue($eachGoods, '[orders][paid]')) ? self::OS_COMPLETE : self::OS_HANDLING;

            /**
             * 產生的銷貨訂單
             * 
             * @var \Woojin\OrderBundle\Entity\Orders
             */
            $orders = new Orders;

            // 設置狀態屬性為下架, 折扣為post 過來的值
            $goods
                ->setStatus($status)
                ->setDiscount(is_null($discount = $accessor->getValue($eachGoods, '[discount]')) ? 10 : $discount)
            ;

            $em->persist($goods);

            // 設置訂單屬性
            $orders
                ->setGoodsPassport($goods)
                ->setStatus($em->find('WoojinOrderBundle:OrdersStatus', $ordersStatusId))
                ->setKind($em->find('WoojinOrderBundle:OrdersKind', self::OK_OUT))
                ->setPayType($em->find('WoojinOrderBundle:PayType', $accessor->getValue($eachGoods, '[orders][pay_type]')))
                ->setCustom($custom)
                ->setRequired($accessor->getValue($eachGoods, '[orders][required]'))
                ->setPaid($accessor->getValue($eachGoods, '[orders][paid]'))
                ->setMemo($accessor->getValue($eachGoods, '[orders][memo]'))
                ->setInvoice($invoice)
            ;

            // 加入回傳商品陣列
            array_push($ordersRepo, $orders);

            $em->persist($orders);
        }  

        $em->flush();

        // 設置發票編號
        $invoice->setSn('TW' . str_pad($invoice->getId(), 10, 0, STR_PAD_LEFT));
        $em->persist($invoice);
        $em->flush();

        // 這邊只回傳id的用意是，讓前端的angular 再一次透過這些id 重新取得 orders 資料，
        // 因為這邊如果直接回傳orders 資料，會無法取得ope操作記錄，
        // 因為ope 根本還沒產生，目前沒有想到怎們解決這個狀況，只好先用蠢方法了。
        foreach ($ordersRepo as $orders) {
            array_push($returnOrdersIds, $orders->getId());
        }

        return new Response($serializer->serialize($returnOrdersIds, 'json'));
    }

    /**
     * 販售活動商品
     * 
     * @Route("/special", name="api_orders_special", options={"expose"=true})
     * @Method("POST")
     *
     * @ApiDoc(
     *  resource=true,
     *  description="活動販售",
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
    public function createSpecialAction (Request $request)
    {
        /**
         * Post 過來的商品實體
         * 
         * @var array{object}
         */
        $goodsGroup = $request->request->get('goods');

        /**
         * Entity Manager
         * 
         * @var object
         */
        $em = $this->getDoctrine()->getManager();

        /**
         * Symfony 的屬性套件，透過它可以用物件方式讀寫陣列
         * 
         * @var object
         */
        $accessor = PropertyAccess::createPropertyAccessor();

        /**
         * Post 過來的客戶
         * 
         * @var array
         */
        $postCustom = $request->request->get('custom');

        /**
         * 使用者
         * 
         * @var Woojin\UserBundle\Entity\User
         */
        $user = $this->get('security.context')->getToken()->getUser();

        /**
         * 販售訂單關連的客戶實體
         * 
         * @var object || null
         */
        $custom = $em->getRepository('WoojinOrderBundle:Custom')->findOneBy(array('email'=> $accessor->getValue($postCustom, '[email]')));
        
        /**
         * 發票
         * 
         * @var \Woojin\OrderBundle\Entity\Invoice
         */
        $invoice = $this->genInvoice($user, $custom);

        /**
         * serializer
         * @var object
         */
        $serializer = \JMS\Serializer\SerializerBuilder::create()->build();

        /**
         * 訂單的實體陣列
         * 
         * @var array{object}
         */
        $ordersRepo = array();

        /**
         * 回傳的銷貨訂單索引陣列
         * 
         * @var array
         */
        $returnOrdersIds = array();

        /**
         * 商品狀態:售出
         * 
         * @var object
         */
        $status = $em->find('WoojinGoodsBundle:GoodsStatus', self::GS_SOLDOUT);

        foreach ($goodsGroup as $eachGoods) {
            /**
             * 逐一透過 post 的商品id取得商品實體
             * 
             * @var object
             */
            $goods = $em->find('WoojinGoodsBundle:GoodsPassport', $accessor->getValue($eachGoods, '[id]'));
            
            /**
             * 訂單狀態 id ，從 required 和 paid 是否相等判斷，
             * 若相等則為完成狀態 OS_COMPLETE ，不相等則為處理中 OS_HANDLING
             * 
             * @var integer
             */
            $ordersStatusId = ($accessor->getValue($eachGoods, '[orders][required]') === $accessor->getValue($eachGoods, '[orders][paid]')) ? self::OS_COMPLETE : self::OS_HANDLING;

            /**
             * 產生的銷貨訂單
             * 
             * @var object
             */
            $orders = new Orders;

            // 設置狀態屬性為下架, 折扣為post 過來的值
            $goods
                ->setStatus($status)
                ->setDiscount(is_null($discount = $accessor->getValue($eachGoods, '[discount]')) ? 10 : $discount)
            ;

            $em->persist($goods);

            // 設置訂單屬性
            $orders
                ->setInvoice($invoice)
                ->setGoodsPassport($goods)
                ->setStatus($em->find('WoojinOrderBundle:OrdersStatus', $ordersStatusId))
                ->setKind($em->find('WoojinOrderBundle:OrdersKind', self::OK_SPECIAL_SELL))
                ->setPayType($em->find('WoojinOrderBundle:PayType', $accessor->getValue($eachGoods, '[orders][pay_type]')))
                ->setCustom($custom)
                ->setRequired($accessor->getValue($eachGoods, '[orders][required]'))
                ->setPaid($accessor->getValue($eachGoods, '[orders][paid]'))
                ->setMemo($accessor->getValue($eachGoods, '[orders][memo]'))
            ;

            // 加入回傳商品陣列
            array_push($ordersRepo, $orders);

            $em->persist($orders);
        }  

        $em->flush();

        // 設置發票編號
        $invoice->setSn('TW' . str_pad($invoice->getId(), 10, 0, STR_PAD_LEFT));
        $em->persist($invoice);
        $em->flush();

        // 這邊只回傳id的用意是，讓前端的angular 再一次透過這些id 重新取得 orders 資料，
        // 因為這邊如果直接回傳orders 資料，會無法取得ope操作記錄，
        // 因為ope 根本還沒產生，目前沒有想到怎們解決這個狀況，只好先用蠢方法了。
        // 當然可以直接回傳整沱orders, 但...就覺得資料量好像有點太大
        foreach ($ordersRepo as $orders) {
            array_push($returnOrdersIds, $orders->getId());
        }

        return new Response($serializer->serialize($returnOrdersIds, 'json'));
    }

    /**
     * --------------根據搜尋條件取得訂單列表----------------
     * 根據搜尋條件取得訂單列表，將搜尋條件以json 格式傳入，其格式如下:
     * {
     *     "Gid": {
     *         "in": [(integer)]
     *     },
     *     "Ggoods_passport": {
     *         "in": [(integer)],
     *         "notIn": [(string)]
     *     },
     *     "Gcustom": {
     *         "in": [custom_id(string)], 
     *         "notIn": [custom_id(string)]
     *     },
     *     "Gstatus": {
     *         "in": [狀態id(int)],
     *         "notIn": [狀態id(int)]
     *     },
     *     "Gmemo": {
     *         "like": [備註包含的字串](string)
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
     *      "ope": 操作記錄,
     *      "required": 應付,
     *      "paid": 已付,
     *      "kind": 類型,
     *      "custom": 客戶,
     *      "memo": 備註,
     *      "status": 狀態, 
     *  }, ...,]
     * } 
     *  
     * @Route(
     *     "/filter/{jsonCondition}/{jsonOrderBy}/{page}/{perPage}", 
     *     requirements={"page"="\d+", "perPage"="\d+"},
     *     defaults={
     *         "jsonCondition"="{}",
     *         "jsonOrderBy"="{}",
     *         "page"=1,
     *         "perPage"=100
     *     },
     *     name="api_orders_filter",
     *     options={"expose"=true}
     * )
     * @Method("GET")
     *
     * @ApiDoc(
     *  resource=true,
     *  description="更新訂單",
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
    public function filterAction($jsonCondition, $jsonOrderBy, $page, $perPage)
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
        $ordersRepo = $this->getDoctrine()->getRepository('WoojinOrderBundle:Orders');

        /**
         * 回傳資料
         * 
         * @var 
         */
        $response = $ordersRepo->findByFilter($conditions, $orderBy, $page, $perPage);

        $jsonResponse = $serializer->serialize($response, 'json');

        return new Response($jsonResponse);
    }

    /**
     * 回傳該條件下(jsonCondition)查詢會得到的結果數量，用來形成頁籤
     * 
     * @Route(
     *     "/filterCount/{jsonCondition}", 
     *     defaults={"jsonCondition"="{}"},
     *     name="api_orders_filter_count",
     *     options={"expose"=true}
     * )
     * @Method("GET")
     *
     * @ApiDoc(
     *  resource=true,
     *  description="根據搜尋條件取得訂單列表",
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
         * 
         * @var array
         */
        $conditions = json_decode($jsonCondition, true);

        /**
         * 訂單repo
         * 
         * @var object
         */
        $ordersRepo = $this->getDoctrine()->getRepository('WoojinOrderBundle:Orders');

        /**
         * 該條件下會得到的商品數
         * 
         * @var integer
         */
        $count = $ordersRepo->findCountByFilter($conditions);

        return new Response($count);
    }

    /**
     * @Route("/{id}", requirements={"id" = "\d+"}, name="api_orders_show",options={"expose"=true})
     * @ParamConverter("orders", class="WoojinOrderBundle:Orders")
     * @Method("GET")
     *
     * @ApiDoc(
     *  resource=true,
     *  description="根據 id 取得對應訂單",
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
    public function showAction(Orders $orders)
    {
        $serializer = \JMS\Serializer\SerializerBuilder::create()->build();
        
        $jsonOrders = $serializer->serialize($orders, 'json');

        return new Response($jsonOrders);
    }

    /**
     * @Route("/{id}", requirements={"id" = "\d+"}, name="api_orders_update",options={"expose"=true})
     * @ParamConverter(class="orders", class="WoojinOrderBundle:Orders")
     * @Method("PUT")
     *
     * @ApiDoc(
     *  resource=true,
     *  description="更新訂單",
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
    public function updateAction(Orders $orders, Request $request)
    {
        /**
         * Entity Manager
         * 
         * @var object
         */
        $em = $this->getDoctrine()->getManager();

        /**
         * 此次更新輸入金額
         * 
         * @var integer
         */
        $diff = $request->request->get('diff', 0);

        /**
         * 訂單狀態id
         * 
         * @var integer
         */
        $statusId = ($orders->getRequired() <= ($orders->getPaid() + $diff) ) ? self::OS_COMPLETE: self::OS_HANDLING;

        /**
         * 訂單實體
         * 
         * @var \Woojin\OrderBundle\Entity\Orders
         */
        $status = $em->find('WoojinOrderBundle:OrdersStatus', $statusId); 

        $orders
            ->setPaid($orders->getPaid() + $diff)
            ->setMemo($request->request->get('memo'))
            ->setStatus($status)
        ;

        $em->persist($orders);
        $em->flush();

        return new Response($orders->getId());
    }

    /**
     * @Route("/{id}", requirements={"id" = "\d+"}, name="api_orders_cancel",options={"expose"=true})
     * @ParamConverter(class="orders", class="WoojinOrderBundle:Orders")
     * @Method("DELETE")
     *
     * @ApiDoc(
     *  resource=true,
     *  description="
     *  ---------------訂單取消--------------     
     *  
     *  1. 訂單狀態改為取消, 應付和已付都改為0 ( 訂單必須要是該商品所屬訂單的最末單才可以取消! )
     *  2. 對應的商品也要同步響應處理，狀況如下:
     *  a. 進貨訂單取消(1)-> 商品直接刪除
     *  b. 寄賣訂單取消(5)-> 商品直接刪除, 寄賣回扣訂單也變更為取消
     *  c. 售出類訂單取消(type === 2) -> 商品恢復上架，若有所屬活動則變更為活動中 
     * 
     * ps: 調貨訂單取消無，請在調貨請求那兒處理 ; 寄賣回扣取消無，直接在寄賣訂單取消。
     * -------------------------------------
     * ",
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
    public function cancelAction (Orders $orders)
    {
        /**
         * Entity Manager
         * 
         * @var object
         */
        $em = $this->getDoctrine()->getManager();

        /**
         * 訂單種類索引
         * 
         * @var integer
         */
        $kindId = $orders->getKind()->getId();

        /**
         * 訂單取消狀態之實體
         * 
         * @var \Woojin\OrderBundle\Entity\OrdersStatus
         */
        $cancelStatus = $em->find('WoojinOrderBundle:OrdersStatus', self::OS_CANCEL);

        /**
         * 商品實體
         * 
         * @var \Woojin\GoodsBundle\Entity\GoodsPassport
         */
        $goods = $orders->getGoodsPassport();

        /**
         * 商品擁有之最新訂單
         * 
         * @var \Woojin\OrderBundle\Entity\Orders
         */
        $lastOrders = $em->getRepository('WoojinOrderBundle:Orders')->getLastOrdersOfGoods($goods);

        // 檢查是否為該商品之最末訂單
        if ($lastOrders !== $orders) {
            // 若最末訂單之父單非傳入之訂單 或 最末訂單並非寄賣回扣訂單，表示這不是寄賣的情況，返回錯誤訊息
            if ($lastOrders->getParent() !== $orders || $lastOrders->getKind()->getId() !== self::OK_FEEDBACK) {
                return new Response(json_encode(array('error' => '此訂單非最末訂單不可取消!')));
            }
        }

        // 依據不同的訂單種類進行不同的處理
        switch ($kindId)
        {
            case self::OK_IN:
            case self::OK_CONSIGN_IN:

                $em->remove($goods);
                $em->flush();

                return new Response(json_encode(array('success' => '刪除完成，相關商品同時刪除!')));

            case self::OK_OUT:
            case self::OK_WEB_OUT:
            case self::OK_SAME_BS:
                /**
                 * 商品上架狀態實體
                 * 
                 * @var \Woojin\GoodsBundle\Entity\GoodsStatus
                 */
                $onSaleStatus = $em->find('WoojinGoodsBundle:GoodsStatus', self::GS_ON_SALE);

                $orders->setStatus($cancelStatus)->setPaid(0);

                $goods->setStatus($onSaleStatus);

                $em->persist($orders);
                $em->persist($goods);
                $em->flush();

                return new Response($orders->getId());

            case self::OK_SPECIAL_SELL:
                /**
                 * 商品活動狀態實體
                 * 
                 * @var \Woojin\GoodsBundle\Entity\GoodsStatus
                 */
                $activityStatus = $em->find('WoojinGoodsBundle:GoodsStatus', self::GS_ACTIVITY);

                $orders
                    ->setStatus($cancelStatus)
                    ->setPaid(0) // 已付金額改為0, 應該是沒啥差別，但這樣做事情會顯得比較合理
                ;

                $goods->setStatus($activityStatus);

                $em->persist($orders);
                $em->persist($goods);
                $em->flush();

                return new Response($orders->getId());

            default:
                return new Response(json_encode(array('error' => '訂單種類不在白名單內')));
        }
    }

    /**
     * 產生一個新的發票實體
     * 
     * @param  [\Woojin\UserBundle\Entity\User] $user   
     * @param  [\Woojin\OrderBundle\Entity\Custom] $custom 
     * @return [\Woojin\OrderBundle\Entity\Invoice] $invoice   
     */
    protected function genInvoice($user, $custom) 
    {
        $invoice = new Invoice;

        $invoice
            ->setUser($user)
            ->setStore($user->getStore())
            ->setCustom($custom)
        ;

        return $invoice;
    }
}

