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
use Woojin\OrderBundle\Entity\Ope;

/**
 * 關於 Ope(訂單) 操作
 *
 * @Route("/ope")
 */
class OpeController extends Controller
{
    /**
     * @Route("/{id}", requirements={"id" = "\d+"}, name="api_ope_show",options={"expose"=true})
     * @ParamConverter("ope", class="WoojinOrderBundle:Ope")
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
    public function showAction(Ope $ope)
    {
        $serializer = \JMS\Serializer\SerializerBuilder::create()->build();
        
        $jsonOpe = $serializer->serialize($ope, 'json');

        return new Response($jsonOpe);
    }

    /**
     * @Route("/{id}", requirements={"id" = "\d+"}, name="api_ope_update",options={"expose"=true})
     * @ParamConverter(class="ope", class="WoojinOrderBundle:Ope")
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
    public function updateAction(Ope $ope, Request $request)
    {
        /**
         * Entity Manager
         * 
         * @var object
         */
        $em = $this->getDoctrine()->getManager();

        $ope
            ->setCardSn($request->request->get('card_sn'))
            ->setPayType($em->find('WoojinOrderBundle:PayType', $request->request->get('pay_type')))
        ;

        $em->persist($ope);
        $em->flush();

        return new Response($ope->getId());
    }
}

