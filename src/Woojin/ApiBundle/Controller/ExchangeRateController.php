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
use Woojin\OrderBundle\Entity\ExchangeRate;

/**
 * 關於 ExchangeRate(匯率) CRUD 動作，
 *
 * @Route("/exchangeRate")
 */
class ExchangeRateController extends Controller
{
    /**
     * 取得匯率列表
     * 
     * @Route("", name="api_exchangeRate_list",options={"expose"=true})
     * @Method("GET")
     *
     * @ApiDoc(
     *  resource=true,
     *  description="取得匯率列表",
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
         * ExchangeRate entity array 
         * @var array{ object }
         */
        $exchangeRates = $this->getDoctrine()->getRepository('WoojinOrderBundle:ExchangeRate')->findAll();

        $serializer = \JMS\Serializer\SerializerBuilder::create()->build();
        
        $jsonExchangeRates = $serializer->serialize($exchangeRates, 'json');

        return new Response($jsonExchangeRates);
    }

    /**
     * 取得單一匯率實體
     * 
     * @Route("/{id}", requirements={"id" = "\d+"}, name="api_exchangeRate_show",options={"expose"=true})
     * @ParamConverter("exchangeRate", class="WoojinOrderBundle:ExchangeRate")
     * @Method("GET")
     *
     * @ApiDoc(
     *  resource=true,
     *  description="根據傳入的 id 取得單一指定匯率(exchangeRate)",
     *  requirements={{"name"="id", "dataType"="integer", "required"=true, "description"="匯率的 id "}},
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
    public function showAction(ExchangeRate $exchangeRate)
    {
        $serializer = \JMS\Serializer\SerializerBuilder::create()->build();
        
        $jsonExchangeRate = $serializer->serialize($exchangeRate, 'json');

        return new Response(json_encode($exchangeRate));
    }

    /**
     * 修改匯率
     * 
     * 
     * @Route("/{id}", requirements={"id" = "\d+"}, name="api_exchangeRate_update", options={"expose"=true})
     * @ParamConverter("exchangeRate", class="WoojinOrderBundle:ExchangeRate")
     * @Method("PUT")
     * 
     * @ApiDoc(
     *  resource=true,
     *  description="更新指定(id)匯率",
     *  requirements={{"name"="id", "dataType"="integer", "required"=true, "description"="匯率的 id "}},
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
    public function updateAction(ExchangeRate $exchangeRate, Request $request)
    {
        $exchangeRate
            ->setName($request->request->get('name'))
            ->setRate($request->request->get('rate'))
        ;

        $em = $this->getDoctrine()->getManager();
        $em->persist($exchangeRate);
        $em->flush();

        return new Response(json_encode($exchangeRate));
    }

    /**
     * 新增匯率
     * 
     * @Route("", name="api_exchangeRate_create", options={"expose"=true})
     * @Method("POST")
     * 
     * @ApiDoc(
     *  resource=true,
     *  description="新增匯率",
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
        $exchangeRate = new ExchangeRate;
        $exchangeRate
            ->setName($request->request->get('name'))
            ->setRate($request->request->get('rate'))
        ;
        
        $em = $this->getDoctrine()->getManager();
        $em->persist($exchangeRate);
        $em->flush();

        return new Response(json_encode($exchangeRate));
    }

    /**
     * 刪除匯率
     * 
     * 
     * @Route("/{id}", requirements={"id" = "\d+"}, name="api_exchangeRate_delete", options={"expose"=true})
     * @ParamConverter("exchangeRate", class="WoojinOrderBundle:ExchangeRate")
     * @Method("DELETE")
     * 
     * @ApiDoc(
     *  resource=true,
     *  description="刪除指定(id)匯率",
     *  requirements={{"name"="id", "dataType"="integer", "required"=true, "description"="匯率的 id "}},
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
    public function destroyAction(ExchangeRate $exchangeRate)
    {
        try {
            $em = $this->getDoctrine()->getManager();
            $em->remove($exchangeRate);
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
}
