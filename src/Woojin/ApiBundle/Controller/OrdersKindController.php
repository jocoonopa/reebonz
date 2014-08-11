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
use Woojin\OrderBundle\Entity\OrdersKind;

/**
 * 關於 OrdersKind(訂單種類) CRUD 動作，
 *
 * @Route("/ordersKind")
 */
class OrdersKindController extends Controller
{
    /**
     * 取得訂單種類列表
     * 
     * @Route("", name="api_ordersKind_list",options={"expose"=true})
     * @Method("GET")
     *
     * @ApiDoc(
     *  resource=true,
     *  description="取得訂單種類列表",
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
         * OrdersKind entity array 
         * @var array{ object }
         */
        $ordersKinds = $this->getDoctrine()->getRepository('WoojinOrderBundle:OrdersKind')->findAll();

        $serializer = \JMS\Serializer\SerializerBuilder::create()->build();
        
        $jsonOrdersKinds = $serializer->serialize($ordersKinds, 'json');

        return new Response($jsonOrdersKinds);
    }

    /**
     * 取得單一訂單種類實體
     * 
     * @Route("/{id}", requirements={"id" = "\d+"}, name="api_ordersKind_show",options={"expose"=true})
     * @ParamConverter("ordersKind", class="WoojinOrderBundle:OrdersKind")
     * @Method("GET")
     *
     * @ApiDoc(
     *  resource=true,
     *  description="根據傳入的 id 取得單一指定訂單種類",
     *  requirements={{"name"="id", "dataType"="integer", "required"=true, "description"="訂單種類的 id "}},
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
    public function showAction(OrdersKind $ordersKind)
    {
        $serializer = \JMS\Serializer\SerializerBuilder::create()->build();
        
        $jsonOrdersKind = $serializer->serialize($ordersKind, 'json');

        return new Response($jsonOrdersKind);
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
