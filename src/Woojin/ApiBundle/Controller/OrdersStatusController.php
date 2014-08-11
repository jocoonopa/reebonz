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
use Woojin\OrderBundle\Entity\OrdersStatus;

/**
 * 關於 OrdersStatus(訂單狀態) CRUD 動作，
 *
 * @Route("/ordersStatus")
 */
class OrdersStatusController extends Controller
{
    /**
     * 取得訂單狀態列表
     * 
     * @Route("", name="api_ordersStatus_list",options={"expose"=true})
     * @Method("GET")
     *
     * @ApiDoc(
     *  resource=true,
     *  description="取得訂單狀態列表",
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
         * OrdersStatus entity array 
         * @var array{ object }
         */
        $ordersStatus = $this->getDoctrine()->getRepository('WoojinOrderBundle:OrdersStatus')->findAll();

        $serializer = \JMS\Serializer\SerializerBuilder::create()->build();
        
        $jsonOrdersStatuss = $serializer->serialize($ordersStatus, 'json');

        return new Response($jsonOrdersStatuss);
    }

    /**
     * 取得單一訂單狀態實體
     * 
     * @Route("/{id}", requirements={"id" = "\d+"}, name="api_ordersStatus_show",options={"expose"=true})
     * @ParamConverter("ordersStatus", class="WoojinOrderBundle:OrdersStatus")
     * @Method("GET")
     *
     * @ApiDoc(
     *  resource=true,
     *  description="根據傳入的 id 取得單一指定訂單狀態(order)Status",
     *  requirements={{"name"="id", "dataType"="integer", "required"=true, "description"="訂單狀態的 id "}},
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
    public function showAction(OrdersStatus $ordersStatus)
    {
        $serializer = \JMS\Serializer\SerializerBuilder::create()->build();
        
        $jsonOrdersStatus = $serializer->serialize($ordersStatus, 'json');

        return new Response($jsonOrdersStatus);
    }

    /**
     * 取得外部api key
     *
     * @param  [string] $apiKey [客戶端送來的api key]
     * @param  [string] $name [客戶名稱]
     * @return [boolean]
     */
    protected function isValid($apiKey, $name)
    {
        return ($apiKey === md5(date('Y-m-d') . $name));
    }
}
