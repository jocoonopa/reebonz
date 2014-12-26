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
use Woojin\StoreBundle\Entity\Activity;

/**
 * 關於 Activity(活動) CRUD 動作，
 *
 * @Route("/activity")
 */
class ActivityController extends Controller
{
    const GS_ONSALE         = 1;
    const GS_SOLDOUT        = 2;
    const GS_MOVING         = 3;
    const GS_OFFSALE        = 4;
    const GS_OTHERSTORE     = 5;
    const GS_ACTIVITY       = 6;

    /**
     * 取得活動列表
     * 
     * @Route("", name="api_activity_list",options={"expose"=true})
     * @Method("GET")
     *
     * @ApiDoc(
     *  resource=true,
     *  description="取得活動列表",
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
         * Activity entity array 
         * 
         * @var array{ object }
         */
        $activitys = $this->getDoctrine()->getRepository('WoojinStoreBundle:Activity')->findAll();

        $serializer = \JMS\Serializer\SerializerBuilder::create()->build();
        
        $jsonActivitys = $serializer->serialize($activitys, 'json');

        return new Response($jsonActivitys);
    }

    /**
     * 取得單一活動實體
     * 
     * @Route("/{id}", requirements={"id" = "\d+"}, name="api_activity_show",options={"expose"=true})
     * @ParamConverter("activity", class="WoojinStoreBundle:Activity")
     * @Method("GET")
     *
     * @ApiDoc(
     *  resource=true,
     *  description="根據傳入的 id 取得單一指定活動(activity)",
     *  requirements={{"name"="id", "dataType"="integer", "required"=true, "description"="活動的 id "}},
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
    public function showAction(Activity $activity)
    {
        $serializer = \JMS\Serializer\SerializerBuilder::create()->build();
        
        $jsonActivity = $serializer->serialize($activity, 'json');
        
        return new Response($jsonActivity);
    }

    /**
     * 修改活動
     * 
     * 
     * @Route("/{id}", requirements={"id" = "\d+"}, name="api_activity_update", options={"expose"=true})
     * @ParamConverter("activity", class="WoojinStoreBundle:Activity")
     * @Method("PUT")
     * 
     * @ApiDoc(
     *  resource=true,
     *  description="更新指定(id)活動",
     *  requirements={{"name"="id", "dataType"="integer", "required"=true, "description"="活動的 id "}},
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
    public function updateAction(Activity $activity, Request $request)
    {
        $activity
            ->setName($request->request->get('name'))
            ->setDescription($request->request->get('description'))
            ->setStartAt(new \DateTime($request->request->get('start_at')))
            ->setEndAt(new \DateTime($request->request->get('end_at')))
            ->setDiscount($request->request->get('discount', 0))
            ->setExceed($request->request->get('exceed', 0))
            ->setMinus($request->request->get('minus', 0))
        ;

        $em = $this->getDoctrine()->getManager();
        $em->persist($activity);
        $em->flush();

        $serializer = \JMS\Serializer\SerializerBuilder::create()->build();
        
        $jsonActivity = $serializer->serialize($activity, 'json');

        return new Response($jsonActivity);
    }

    /**
     * 新增活動
     * 
     * @Route("", name="api_activity_create", options={"expose"=true})
     * @Method("POST")
     * 
     * @ApiDoc(
     *  resource=true,
     *  description="新增活動",
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
        $activity = new Activity;
        $activity
            ->setName($request->request->get('name'))
            ->setDescription($request->request->get('description'))
            ->setStartAt(new \DateTime($request->request->get('start_at')))
            ->setEndAt(new \DateTime($request->request->get('end_at')))
            ->setDiscount($request->request->get('discount', 0))
            ->setExceed($request->request->get('exceed', 0))
            ->setMinus($request->request->get('minus', 0))
        ;
        
        $em = $this->getDoctrine()->getManager();
        $em->persist($activity);
        $em->flush();

        $serializer = \JMS\Serializer\SerializerBuilder::create()->build();
        
        $jsonActivity = $serializer->serialize($activity, 'json');

        return new Response($jsonActivity);
    }

    /**
     * 刪除活動
     * 
     * @Route("/{id}", requirements={"id" = "\d+"}, name="api_activity_delete", options={"expose"=true})
     * @ParamConverter("activity", class="WoojinStoreBundle:Activity")
     * @Method("DELETE")
     * 
     * @ApiDoc(
     *  resource=true,
     *  description="刪除指定(id)活動",
     *  requirements={{"name"="id", "dataType"="integer", "required"=true, "description"="活動的 id "}},
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
    public function destroyAction(Activity $activity)
    {
        try {
            $em = $this->getDoctrine()->getManager();
            $em->remove($activity);
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

    /**
     * 刷入活動
     * 
     * @Route("/{id}/platform/pull", requirements={"id" = "\d+"}, name="api_activity_pull", options={"expose"=true})
     * @ParamConverter("activity", class="WoojinStoreBundle:Activity")
     * @Method("PUT")
     * 
     * @ApiDoc(
     *  resource=true,
     *  description="指定商品(id)刷入活動",
     *  requirements={{"name"="id", "dataType"="integer", "required"=true, "description"="商品的 id"}},
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
    public function pullAction(Activity $activity, Request $request)
    {
        /**
         * Entity Manager
         * 
         * @var object
         */
        $em = $this->getDoctrine()->getManager();

        /**
         * 活動狀態
         * 
         * @var \Woojin\GoodsBundle\Entity\GoodsStatus
         */
        $activityStatus = $em->find('WoojinGoodsBundle:GoodsStatus', self::GS_ACTIVITY);

        /**
         * 商品產編陣列
         * 
         * @var array
         */
        $goodsIds = array();

        array_map(function ($row) use (&$goodsIds) {
            array_push($goodsIds, $row['id']);
        }, $request->request->get('goodsPost'));

        /**
         * QueryBuilder
         * 
         * @var object
         */
        $qb = $em->createQueryBuilder();

        // 將商品選擇出來
        $qb
            ->select('g')
            ->from('WoojinGoodsBundle:GoodsPassport', 'g')
            ->where(
                $qb->expr()->andX(
                    $qb->expr()->in('g.id', $goodsIds),
                    $qb->expr()->in('g.status', array(self::GS_ONSALE, self::GS_ACTIVITY))
                )
            )
        ;

        /**
         * 商品實體陣列
         * 
         * @var array{object}
         */
        $goodses = $qb->getQuery()->getResult();

        foreach ($goodses as $goods) {
            if ($goods->getStatus()->getId() !== self::GS_OFFSALE) {
                // update 商品屬性
                $goods
                    ->setActivity($activity)
                    ->setStatus($activityStatus)
                ;

                $em->persist($goods);
            }
        }

        $em->flush();

        return new Response(json_encode(array('status' => 'ok')));
    }

    /**
     * 刷出活動返回店中
     * 
     * @Route("/{id}/platform/push", requirements={"id" = "\d+"}, name="api_activity_push", options={"expose"=true})
     * @ParamConverter("activity", class="WoojinStoreBundle:Activity")
     * @Method("PUT")
     * 
     * @ApiDoc(
     *  resource=true,
     *  description="指定(id)商品刷出活動",
     *  requirements={{"name"="id", "dataType"="integer", "required"=true, "description"="活動的 id"}},
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
    public function pushAction(Activity $activity, Request $request)
    {
        /**
         * Entity Manager
         * 
         * @var object
         */
        $em = $this->getDoctrine()->getManager();

        /**
         * 商品上架狀態
         * 
         * @var \Woojin\GoodsBundle\Entity\GoodsStatus
         */
        $onSaleStatus = $em->find('WoojinGoodsBundle:GoodsStatus', self::GS_ONSALE);

        /**
         * 商品產編陣列
         * 
         * @var array
         */
        $goodsIds = array();
        
        array_map(function ($row) use (&$goodsIds) {
            array_push($goodsIds, $row['id']);
        }, $request->request->get('goodsPost'));

        /**
         * QueryBuilder
         * 
         * @var object
         */
        $qb = $em->createQueryBuilder();

        // 將商品選擇出來
        $qb
            ->select('g')
            ->from('WoojinGoodsBundle:GoodsPassport', 'g')
            ->where(
                $qb->expr()->andX(
                    $qb->expr()->eq('g.status', self::GS_ACTIVITY),
                    $qb->expr()->in('g.id', $goodsIds),
                    $qb->expr()->eq('g.activity', $activity->getId())
                )
            )
        ;

        /**
         * 商品實體陣列
         * 
         * @var array{object}
         */
        $goodses = $qb->getQuery()->getResult();

        // 逐一update活動為目標活動
        foreach ($goodses as $goods) {
            if ($goods->getStatus()->getId() !== self::GS_OFFSALE) {
                // update 商品屬性
                $goods
                    ->setActivity(null)
                    ->setStatus($onSaleStatus)
                ;

                $em->persist($goods);
            }
        }

        $em->flush();

        return new Response(json_encode(array('status' => 'ok')));
    }
}
