<?php

namespace Woojin\ApiBundle\Controller;

//Third Party
use Nelmio\ApiDocBundle\Annotation\ApiDoc;

//Component
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Exception\InvalidParameterException;
use Symfony\Component\Security\Core\Exception\InsufficientAuthenticationException;

//Default
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Cache;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;

// Entity
use Woojin\GoodsBundle\Entity\GoodsPassport;
use Woojin\OrderBundle\Entity\Move;
use Woojin\OrderBundle\Entity\MoveMemo;
use Woojin\OrderBundle\Entity\Orders;

/**
 * 關於 Move(調貨請求) CRUD 動作，
 *
 * @Route("/move")
 */
class MoveController extends Controller
{
    const GS_ON_SALE        = 1;
    const GS_MOVING         = 3;
    const GS_OFF_SALE       = 4;
    const GS_OTHER_STORE    = 5;
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
    const PT_CASH           = 1;

    /**
     * 取得調貨請求列表
     * 
     * @Route("", name="api_move_list",options={"expose"=true})
     * @Method("GET")
     *
     * @ApiDoc(
     *  resource=true,
     *  description="取得調貨請求列表",
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
         * Move entity array 
         * @var array{ object }
         */
        $moves = $this->getDoctrine()->getRepository('WoojinOrderBundle:Move')->findAll();

        $serializer = \JMS\Serializer\SerializerBuilder::create()->build();
        
        $jsonMoves = $serializer->serialize($moves, 'json');

        return new Response($jsonMoves);
    }

    /**
     * 取得單一調貨請求實體
     * 
     * @Route("/{id}", requirements={"id" = "\d+"}, name="api_move_show",options={"expose"=true})
     * @ParamConverter("move", class="WoojinOrderBundle:Move")
     * @Method("GET")
     *
     * @ApiDoc(
     *  resource=true,
     *  description="根據傳入的 id 取得單一指定調貨請求(move)",
     *  requirements={{"name"="id", "dataType"="integer", "required"=true, "description"="調貨請求的 id "}},
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
    public function showAction(Move $move)
    {
        $serializer = \JMS\Serializer\SerializerBuilder::create()->build();
        
        $jsonMove = $serializer->serialize($move, 'json');

        return new Response($jsonMove);
    }

    /**
     * 根據搜尋條件取得調貨請求列表，將搜尋條件以json 格式傳入
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
     *  "move": [{
     *   // ...族繁不及被載   
     *  }, ...,]
     * } 
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
     *     name="api_move_filter",
     *     options={"expose"=true}
     * )
     * @Method("GET")
     *
     * @ApiDoc(
     *  resource=true,
     *  description="根據搜尋條件取得調貨請求列表",
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
         * 調貨請求repo
         * @var object
         */
        $moveRepo = $this->getDoctrine()->getRepository('WoojinOrderBundle:Move');

        /**
         * 回傳資料
         * @var 
         */
        $response = $moveRepo->findByFilter($conditions, $orderBy, $page, $perPage);

        $jsonResponse = $serializer->serialize($response, 'json');

        return new Response($jsonResponse);
    }

    /**
     * 回傳該條件下(jsonCondition)查詢會得到的結果數量，用來形成頁籤
     * 
     * @Route(
     *     "/filterCount/{jsonCondition}", 
     *     defaults={"jsonCondition"="{}"},
     *     name="api_move_filter_count",
     *     options={"expose"=true}
     * )
     * @Method("GET")
     *
     * @ApiDoc(
     *  resource=true,
     *  description="根據搜尋條件取得調貨請求列表個數",
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
        $moveRepo = $this->getDoctrine()->getRepository('WoojinOrderBundle:Move');

        /**
         * 該條件下會得到的商品數
         * @var integer
         */
        $count = $moveRepo->findCountByFilter($conditions);

        return new Response($count);
    }

    /**
     * 修改調貨請求
     * 
     * 
     * @Route("/{id}", requirements={"id" = "\d+"}, name="api_move_update", options={"expose"=true})
     * @ParamConverter("move", class="WoojinOrderBundle:Move")
     * @Method("PUT")
     * 
     * @ApiDoc(
     *  resource=true,
     *  description="更新指定(id)調貨請求",
     *  requirements={{"name"="id", "dataType"="integer", "required"=true, "description"="調貨請求的 id "}},
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
    public function updateAction(Move $move, Request $request)
    {
        /**
         * 目前使用者的所屬商店
         * 
         * @var object
         */
        $user = $this->get('security.context')->getToken()->getUser();

        /**
         * Entity Manager
         * 
         * @var object
         */
        $em = $this->getDoctrine()->getManager();

        /**
         * 動作類型 [send, recieve, cancel]
         * 
         * @var string
         */
        $act = $request->request->get('act');

        // 分別針對動作進行處理
        switch ($act)
        {
            /**
             * 出貨確認
             *
             * 1. 產生相關調進出貨訂單, 接收店的商品護照狀態為調貨中
             * 2. 調貨請求狀態綁訂關連訂單商品以及回應人員
             * 3. 調貨請求狀態備註更新
             */
            case 'send':              
                if (!$this->isOkToSend($move, $user)) {
                    return new Response(json_encode(array('error' => '不符合出貨條件!')));
                }

                /**
                 * 商品狀態: 上架
                 * 
                 * @var \Woojin\GoodsBundle\Entity\GoodsStatus
                 */
                $onSaleStatus = $em->getDoctrine()->getRepository('WoojinGoodsBundle:GoodsStatus')->find(self::GS_ON_SALE);

                /**
                 * 出貨商品
                 * 
                 * @var \Woojin\GoodsBundle\Entity\GoodsPassport
                 */
                $sendGoods = $move->getOutGoodsPassport();
                $sendGoods->setStatus($onSaleStatus);

                /**
                 * 接收商品
                 * 
                 * @var \Woojin\GoodsBundle\Entity\GoodsPassport
                 */
                $recieveGoods = clone $sendGoods;
                $recieveGoods
                    ->setSn($move->getReqStore()->getSn() . substr($sendGoods->getSn(), 1))
                    ->setStatus($onSaleStatus);
                ; 
               
                /**
                 * 訂單狀態：處理中
                 * 
                 * @var \Woojin\OrderBundle\Entity\OrdersStatus
                 */
                $handlingStatus = $em->getRepository('WoojinOrderBundle:OrdersStatus')->find(self::OS_HANDLING);
                
                /**
                 * 付費方式: 現金
                 * 
                 * @var \Woojin\OrderBundle\Entity\PayType
                 */
                $cashPayType = $em->getRepository('WoojinOrderBundle:PayType')->find(self::PT_CASH);
                
                /**
                 * 訂單種類: 調出貨 
                 * 
                 * @var \Woojin\OrderBundle\Entity\OrdersKind
                 */
                $sendKind = $em->getRepository('WoojinOrderBundle:OrdersKind')->find(self::OK_MOVE_OUT);
                
                /**
                 * 訂單種類: 調進貨
                 * 
                 * @var \Woojin\OrderBundle\Entity\OrdersKind
                 */
                $recieveKind = $em->getRepository('WoojinOrderBundle:OrdersKind')->find(self::OK_MOVE_IN);
        
                /**
                 * 調出貨訂單
                 * 
                 * @var \Woojin\OrderBundle\Entity\Orders
                 */
                $sendOrder = new Orders;
                $sendOrder
                    ->setGoodsPassport($sendGoods)
                    ->setStatus($handlingStatus)
                    ->setPayType($cashPayType)
                    ->setKind($sendKind)
                    ->setRequired(0)
                    ->setPaid(0)
                ;

                /**
                 * 調進貨訂單
                 * 
                 * @var \Woojin\OrderBundle\Entity\Orders
                 */
                $recieveOrder = new Orders;
                $recieveOrder
                    ->setGoodsPassport($recieveGoods)
                    ->setStatus($handlingStatus)
                    ->setPayType($cashPayType)
                    ->setKind($recieveKind)
                    ->setRequired(0)
                    ->setPaid(0)
                ;

                // 請求狀態綁訂關連訂單, 商品以及回應者
                $move
                    ->setInOrders($recieveOrder)
                    ->setOutOrders($sendOrder)
                    ->setInGoodsPassport($recieveGoods)
                    ->setResUser($user)
                ;

                // 如果有備註，則新增一個備註實體並且關連該調貨請求
                if ($content = $request->request->get('memo', false)) {
                    /**
                     * 調貨備註
                     * 
                     * @var \Woojin\OrderBundle\Entity\MoveMemo
                     */
                    $moveMemo = new MoveMemo;
                    $moveMemo
                        ->setContent($content)
                        ->setMove($move)
                    ;

                    $em->persist($moveMemo);
                }

                $em->persist($sendGoods);
                $em->persist($sendOrder);
                $em->persist($recieveGoods);
                $em->persist($recieveOrder);

                $em->flush();

                break;

            /**
             * 到貨確認:
             * 
             * 1. 相關訂單狀態改為完成，接收店的商品護照狀態改為上架
             * 2. 調貨請求狀態改為完成
             * 3. 自動取消其他關連此商品的調貨請求
             */
            case 'recieve':            
                if (!$this->isOkToRecieve($move, $user)) {
                    return new Response(json_encode(array('error' => '不符合收貨條件!')));
                }

                /**
                 * 訂單狀態：取消
                 * 
                 * @var \Woojin\OrderBundle\Entity\OrdersStatus
                 */
                $cancelStatus = $em->getRepository('WoojinOrderBundle:OrdersStatus')->find(self::OS_CANCEL);

                /**
                 * 訂單狀態：處理中
                 * 
                 * @var \Woojin\OrderBundle\Entity\OrdersStatus
                 */
                $completeStatus = $em->getRepository('WoojinOrderBundle:OrdersStatus')->find(self::OS_COMPLETE);

                /**
                 * 商品狀態: 上架
                 * 
                 * @var \Woojin\GoodsBundle\Entity\GoodsStatus
                 */
                $onSaleStatus = $em->getRepository('WoojinGoodsBundle:GoodsStatus')->find(self::GS_ON_SALE);

                /**
                 * 商品狀態: 它店
                 * 
                 * @var \Woojin\GoodsBundle\Entity\GoodsStatus
                 */
                $otherStoreStatus = $em->getRepository('WoojinGoodsBundle:GoodsStatus')->find(self::GS_OTHER_STORE);

                // 相關訂單狀態改為完成，接收店的商品護照狀態改為上架
                $move->getInOrders()->setStatus($completeStatus);
                $move->getOutOrders()->setStatus($completeStatus);
                $move->getInGoodsPassport()->setStatus($onSaleStatus);
                $move->getOutGoodsPassport()->setStatus($otherStoreStatus);

                // 訂單狀態改為完成
                $move->getStatus()->setStatus($completeStatus);

                $em->persist($move);

                // 自動取消其他關連此商品的調貨請求
                $qb = $em->createQueryBuilder();
                $qb
                    ->select('m')
                    ->from('WoojinOrderBundle:Move', 'm')
                    ->where(
                        $qb->expr()->andX(
                            $qb->expr()->eq('m.out_goods_passport', $move->getOutGoodsPassport()),
                            $qb->expr()->neq('m.id', $move) 
                        ) 
                    )
                ;

                // 如果有備註，則新增一個備註實體並且關連該調貨請求
                if ($content = $request->request->get('memo', false)) {
                    /**
                     * 調貨備註
                     * 
                     * @var \Woojin\OrderBundle\Entity\MoveMemo
                     */
                    $moveMemo = new MoveMemo;
                    $moveMemo
                        ->setContent($content)
                        ->setMove($move)
                    ;

                    $em->persist($moveMemo);
                }

                $cancelMoves = $qb->getQuery()->getResult();
                foreach ($cancelMoves as $cancelMove) {
                    $cancelMove->setStatus($cancelStatus);

                    $em->persist($cancelMove);
                }

                $em->flush();

                break;

            /**
             * 取消請求:
             *
             * 1. 調貨請求狀態改為取消
             * 2. 關連訂單或是商品若存在全部刪除
             */
            case 'cancel':
                if (!$this->isOkToCancel($move, $user)) {
                    return new Response(json_encode(array('error' => '不符合取消條件!')));
                }

                /**
                 * 商品狀態: 上架
                 * 
                 * @var \Woojin\GoodsBundle\Entity\GoodsStatus
                 */
                $onSaleStatus = $em->getRepository('WoojinGoodsBundle:GoodsStatus')->find(self::GS_ON_SALE);

                /**
                 * 商品狀態: 它店
                 * 
                 * @var \Woojin\GoodsBundle\Entity\GoodsStatus
                 */
                $otherStoreStatus = $em->getRepository('WoojinGoodsBundle:GoodsStatus')->find(self::GS_OTHER_STORE);

                /**
                 * 訂單狀態：取消
                 * 
                 * @var \Woojin\OrderBundle\Entity\OrdersStatus
                 */
                $cancelStatus = $em->getRepository('WoojinOrderBundle:OrdersStatus')->find(self::OS_CANCEL);

                $move->setStatus($cancelStatus);

                $em->persist($move);

                if (!is_null($move->getInOrders())) {
                    $move->getInOrders()->setStatus($cancelStatus);
                }

                if (!is_null($move->getOutOrders())) {
                    $move->getOutOrders()->setStatus($cancelStatus);
                }

                if (!is_null($move->getInGoodsPassport())) {
                    $move->getInGoodsPassport()->setStatus($otherStoreStatus);
                }

                if (!is_null($move->getOutGoodsPassport())) {
                    $move->getOutGoodsPassport()->setStatus($onSaleStatus);
                }

                // 如果有備註，則新增一個備註實體並且關連該調貨請求
                if ($content = $request->request->get('memo', false)) {
                    /**
                     * 調貨備註
                     * 
                     * @var \Woojin\OrderBundle\Entity\MoveMemo
                     */
                    $moveMemo = new MoveMemo;
                    $moveMemo
                        ->setContent($content)
                        ->setMove($move)
                    ;

                    $em->persist($moveMemo);
                }

                $em->flush();

                break;

            default:
                break;
        }

        /**
         * serializer
         * 
         * @var \JMS\Serializer\SerializerBuilder
         */
        $serializer = \JMS\Serializer\SerializerBuilder::create()->build();

        $jsonMove = $serializer->serialize($move, 'json');

        return new Response($jsonMove);
    }

    /**
     * 新增調貨請求
     * 
     * @Route("", name="api_move_create", options={"expose"=true})
     * @Method("POST")
     * 
     * @ApiDoc(
     *  resource=true,
     *  description="新增調貨請求",
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
         * POST 過來的商品實體
         * 
         * @var Woojin\GoodsBundle\Entity\GoodsPassport
         */
        $goods = $em->getRepository('WoojinGoodsBundle:GoodsPassport')->find($request->request->get('id'));

        // 若不是上架商品，禁止發送調貨請求
        if ($goods->getStatus()->getId() !== self::GS_ON_SALE) {
            return new Response('非上架商品!');
        }

        /**
         * 狀態實體
         * 
         * @var Woojin\OrderBundle\Entity\Status
         */
        $status = $em->getRepository('WoojinOrderBundle:OrdersStatus')->find(self::OS_HANDLING);

        /**
         * 目前使用者的所屬商店
         * 
         * @var object
         */
        $user = $this->get('security.context')->getToken()->getUser();

        // 若為本店商品，不進行調貨，返回訊息
        if ($goods->getStore()->getId() === $user->getStore()->getId()) {
            return new Response(json_encode(array('msg' => '本店商品無需調貨!')));
        }

        /**
         * 調貨請求實體
         *
         * @var Woojin\OrderBundle\Entity\Move
         */
        $existMove = $em->getRepository('WoojinOrderBundle:Move')->findBy(array('out_goods_passport' => $goods, 'status' => $status, 'req_store' => $user->getStore()));

        // 檢查該店是否已經對該商品有一個尚未處理的請求，
        // 若大於1表示有，回傳請求已存在的訊息
        if ($existMove) {
            return new Response(json_encode(array('msg' => '已存在請求!')) );
        }

        /**
         * 新產生的調貨請求實體
         * 
         * @var Woojin\OrderBundle\Entity\Move
         */
        $move = new Move;
        $move
            ->setStatus($status)
            ->setReqStore($user->getStore())
            ->setReqUser($user)
            ->setOutGoodsPassport($goods)
            ->setResStore($goods->getStore())
        ;

        $em->persist($move);
        $em->flush();

        $serializer = \JMS\Serializer\SerializerBuilder::create()->build();
        
        $jsonMove = $serializer->serialize($move, 'json');

        return new Response($jsonMove);
    }

    /**
     * 刪除調貨請求
     * 
     * 
     * @Route("/{id}", requirements={"id" = "\d+"}, name="api_move_delete", options={"expose"=true})
     * @ParamConverter("move", class="WoojinOrderBundle:Move")
     * @Method("DELETE")
     * 
     * @ApiDoc(
     *  resource=true,
     *  description="刪除指定(id)調貨請求",
     *  requirements={{"name"="id", "dataType"="integer", "required"=true, "description"="調貨請求的 id "}},
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
    public function destroyAction(Move $move)
    {
        try {
            $em = $this->getDoctrine()->getManager();
            $em->remove($move);
            $em->flush();

            /**
             * 回傳訊息
             * @var array
             */
            $returnMsg = array('status' => 'OK', 'method' => 'delete');

            return new Response(json_encode($returnMsg));
        } catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * 是否可以出貨
     * 
     * 1. 商品狀態必須為上架
     * 2. 商品所屬店必須為本店
     * 3. 調貨請求狀態為處理中
     * 
     * @param  [instance \Woojin\OrderBundle\Entity\Move]  $move 
     * @param  [instance \Woojin\UserBundle\Entity\User]  $user
     * @return boolean 
     */
    protected function isOkToSend($move, $user)
    {
        return (
            $move->getOutGoodsPassport()->getStatus()->getId() === self::GS_ON_SALE &&
            $move->getOutGoodsPassport()->getStore() === $user->getStore() &&
            $move->getStatus()->getId() === self::OS_HANDLING
        );
    }

    /**
     * 是否可以收貨
     * 
     * 1. 商品狀態必須為調貨中
     * 2. 商品所屬店非本店
     * 3. 調貨請求發起店為本店
     * 4. 調貨請求狀態為處理中
     * 
     * @param  [instance \Woojin\OrderBundle\Entity\Move]  $move 
     * @param  [instance \Woojin\UserBundle\Entity\User]  $user
     * @return boolean 
     */
    protected function isOkToRecieve($move, $user)
    {
        return (
            $move->getOutGoodsPassport()->getStatus()->getId() === self::GS_MOVING &&
            $move->getOutGoodsPassport()->getStore !== $user->getStore() &&
            $move->getReqStore() === $user->getStore() &&
            $move->getStatus()->getId() === self::OS_HANDLING
        );
    }

    /**
     * 是否可以取消
     * 
     * 1. 調貨請求狀態為處理中
     * 
     * @param  [instance \Woojin\OrderBundle\Entity\Move]  $move 
     * @param  [instance \Woojin\UserBundle\Entity\User]  $user
     * @return boolean 
     */
    protected function isOkToCancel($move, $user)
    {
        return ($move->getStatus()->getId() === self::OS_HANDLING);
    }
}
