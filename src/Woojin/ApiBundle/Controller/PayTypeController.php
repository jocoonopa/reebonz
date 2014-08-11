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
use Woojin\OrderBundle\Entity\PayType;

/**
 * 關於 PayType(付費方式) CRUD 動作，
 *
 * @Route("/payType")
 */
class PayTypeController extends Controller
{
    /**
     * 取得付費方式列表
     * 
     * @Route("", name="api_payType_list",options={"expose"=true})
     * @Method("GET")
     *
     * @ApiDoc(
     *  resource=true,
     *  description="取得付費方式列表",
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
         * PayType entity array 
         * @var array{ object }
         */
        $payTypes = $this->getDoctrine()->getRepository('WoojinOrderBundle:PayType')->findAll();

        $serializer = \JMS\Serializer\SerializerBuilder::create()->build();
        
        $jsonPayTypes = $serializer->serialize($payTypes, 'json');

        return new Response($jsonPayTypes);
    }

    /**
     * 取得單一付費方式實體
     * 
     * @Route("/{id}", requirements={"id" = "\d+"}, name="api_payType_show",options={"expose"=true})
     * @ParamConverter("payType", class="WoojinOrderBundle:PayType")
     * @Method("GET")
     *
     * @ApiDoc(
     *  resource=true,
     *  description="根據傳入的 id 取得單一指定付費方式(payType)",
     *  requirements={{"name"="id", "dataType"="integer", "required"=true, "description"="付費方式的 id "}},
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
    public function showAction(PayType $payType)
    {
        $serializer = \JMS\Serializer\SerializerBuilder::create()->build();
        
        $jsonPayType = $serializer->serialize($payType, 'json');

        return new Response($jsonPayType);
    }

    /**
     * 修改付費方式
     * 
     * 
     * @Route("/{id}", requirements={"id" = "\d+"}, name="api_payType_update", options={"expose"=true})
     * @ParamConverter("payType", class="WoojinOrderBundle:PayType")
     * @Method("PUT")
     * 
     * @ApiDoc(
     *  resource=true,
     *  description="更新指定(id)付費方式",
     *  requirements={{"name"="id", "dataType"="integer", "required"=true, "description"="付費方式的 id "}},
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
    public function updateAction(PayType $payType, Request $request)
    {
        $payType
            ->setName($request->request->get('name'))
            ->setDiscount($request->request->get('discount'))
        ;

        $em = $this->getDoctrine()->getManager();
        $em->persist($payType);
        $em->flush();

        return new Response(json_encode($payType));
    }

    /**
     * 新增付費方式
     * 
     * @Route("", name="api_payType_create", options={"expose"=true})
     * @Method("POST")
     * 
     * @ApiDoc(
     *  resource=true,
     *  description="新增付費方式",
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
        $payType = new PayType;
        $payType
            ->setName($request->request->get('name'))
            ->setDiscount($request->request->get('discount'))
        ;
        
        $em = $this->getDoctrine()->getManager();
        $em->persist($payType);
        $em->flush();

        return new Response(json_encode($payType));
    }

    /**
     * 刪除付費方式
     * 
     * 
     * @Route("/{id}", requirements={"id" = "\d+"}, name="api_payType_delete", options={"expose"=true})
     * @ParamConverter("payType", class="WoojinOrderBundle:PayType")
     * @Method("DELETE")
     * 
     * @ApiDoc(
     *  resource=true,
     *  description="刪除指定(id)付費方式",
     *  requirements={{"name"="id", "dataType"="integer", "required"=true, "description"="付費方式的 id "}},
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
    public function destroyAction(PayType $payType)
    {
        try {
            $em = $this->getDoctrine()->getManager();
            $em->remove($payType);
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
