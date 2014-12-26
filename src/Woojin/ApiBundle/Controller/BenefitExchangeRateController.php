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
use Woojin\StoreBundle\Entity\BenefitExchangeRate;

/**
 * 關於 BenefitExchangeRate(銷貨匯率) CRUD 動作，
 *
 * @Route("/benefitExchangeRate")
 */
class BenefitExchangeRateController extends Controller
{
    /**
     * 取得匯率列表
     * 
     * @Route("", name="api_benefitExchangeRate_list",options={"expose"=true})
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
        $benefitExchangeRates = $this->getDoctrine()->getRepository('WoojinStoreBundle:BenefitExchangeRate')->findAll();

        $serializer = \JMS\Serializer\SerializerBuilder::create()->build();
        
        $jsonExchangeRates = $serializer->serialize($benefitExchangeRates, 'json');

        return new Response($jsonExchangeRates);
    }

    /**
     * 取得單一匯率實體
     * 
     * @Route("/{id}", requirements={"id" = "\d+"}, name="api_benefitExchangeRate_show",options={"expose"=true})
     * @ParamConverter("benefitExchangeRate", class="WoojinStoreBundle:BenefitExchangeRate")
     * @Method("GET")
     *
     * @ApiDoc(
     *  resource=true,
     *  description="根據傳入的 id 取得單一指定匯率(benefitExchangeRate)",
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
    public function showAction(ExchangeRate $benefitExchangeRate)
    {
        $serializer = \JMS\Serializer\SerializerBuilder::create()->build();
        
        $jsonExchangeRate = $serializer->serialize($benefitExchangeRate, 'json');

        return new Response(json_encode($benefitExchangeRate));
    }

    /**
     * 修改匯率
     * 
     * 
     * @Route("/{id}", requirements={"id" = "\d+"}, name="api_benefitExchangeRate_update", options={"expose"=true})
     * @ParamConverter("benefitExchangeRate", class="WoojinStoreBundle:BenefitExchangeRate")
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
    public function updateAction(BenefitExchangeRate $benefitExchangeRate, Request $request)
    {
        $benefitExchangeRate
            ->setName('新加坡幣')
            ->setRate($request->request->get('rate'))
            ->setMonth(trim($request->request->get('month')))
        ;

        $em = $this->getDoctrine()->getManager();
        $em->persist($benefitExchangeRate);
        $em->flush();

        return new Response(json_encode($benefitExchangeRate));
    }

    /**
     * 新增匯率
     * 
     * @Route("", name="api_benefitExchangeRate_create", options={"expose"=true})
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
        $benefitExchangeRate = new BenefitExchangeRate;
        $benefitExchangeRate
            ->setName('新加坡幣')
            ->setRate($request->request->get('rate'))
            ->setMonth(trim($request->request->get('month')))
        ;
        
        $em = $this->getDoctrine()->getManager();
        $em->persist($benefitExchangeRate);
        $em->flush();

        return new Response(json_encode($benefitExchangeRate));
    }

    /**
     * 刪除匯率
     * 
     * 
     * @Route("/{id}", requirements={"id" = "\d+"}, name="api_benefitExchangeRate_delete", options={"expose"=true})
     * @ParamConverter("benefitExchangeRate", class="WoojinStoreBundle:BenefitExchangeRate")
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
    public function destroyAction(BenefitExchangeRate $benefitExchangeRate)
    {
        try {
            $em = $this->getDoctrine()->getManager();
            $em->remove($benefitExchangeRate);
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
}
