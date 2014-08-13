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
            ->setDiscount($request->request->get('discount'))
            ->setExceed($request->request->get('exceed'))
            ->setMinus($request->request->get('minus'))
            ->setStartAt(new \DateTime($request->request->get('start_at')))
            ->setEndAt(new \DateTime($request->request->get('end_at')))
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
            ->setDiscount($request->request->get('discount'))
            ->setExceed($request->request->get('exceed'))
            ->setMinus($request->request->get('minus'))
            ->setStartAt(new \DateTime($request->request->get('start_at')))
            ->setEndAt(new \DateTime($request->request->get('end_at')))
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
        } catch (Exception $e) {
            throw $e;
        }
    }
}
