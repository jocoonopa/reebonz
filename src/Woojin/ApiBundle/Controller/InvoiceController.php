<?php

namespace Woojin\ApiBundle\Controller;

//Third Party
use Nelmio\ApiDocBundle\Annotation\ApiDoc;

//Component
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

//Default
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;

/**
 * 關於 Invoice CRUD 動作，這個實體直接依賴 Order 的狀態，
 * 因此不具備新增和刪除的動作，由於和客戶端的發票機直接溝通，難以透過 session 驗證，
 * 因此採用API Key 的方式驗證權限。
 * 
 *
 * @Route("/invoice")
 */
class InvoiceController extends Controller
{
    /**
     * @Route("", name="api_invoice_list", options={"expose"=true})
     * @Method("GET")
     *
     * @ApiDoc(
     *  resource=true,
     *  https=true,
     *  description="取得發票列表",
     *  requirements={},
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
        $invoices = $this->getDoctrine()->getRepository('WoojinOrderBundle:Invoice')->findAll();

        $serializer = \JMS\Serializer\SerializerBuilder::create()->build();
        
        $jsonInvoices = $serializer->serialize($invoices, 'json');

        return new Response($jsonInvoices);
    }

    /**
     * @Route("/{id}", name="api_invoice_show", options={"expose"=true})
     * @ParamConverter("invoice", class="WoojinOrderBundle:Invoice")
     * @Method("GET")
     *
     * @ApiDoc(
     *  resource=true,
     *  description="根據傳入的 id 取得單一指定發票(invoice)",
     *  requirements={
     *      {"name"="id", "dataType"="integer", "required"=true, "description"="發票的 id "},
     *      {"name"="apiKey", "dataType"="string", "required"=true, "description"="Used to validate permission"},
     *      {"name"="_format", "dataType"="string", "required"=true, "description"="Decide type of return MIME, default is json"}
     *  },
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
    public function showAction(Invoice $invoice, $apiKey)
    {
    }

    /**
     * @Route("/{id}", name="api_invoice_update", options={"expose"=true})
     * @ParamConverter("invoice", class="WoojinOrderBundle:Invoice")
     * @Method("PUT")
     * 
     * @ApiDoc(
     *  resource=true,
     *  description="更新指定(id)發票(invoice)",
     *  requirements={
     *      {"name"="id", "dataType"="integer", "required"=true, "description"="發票的 id "} 
     *  },
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
    public function updateAction(Invoice $invoice, $apiKey, $sn)
    {
        /**
         * Entity Manager
         * 
         * @var object
         */
        $em = $this->getDoctrine()->getManager();

        // 設定發票屬性
        $invoice
            ->setHasPrint(true)
            ->setSn($sn)
        ;

        $em->persist($invoice);
        $em->flush();


        return new Response(json_encode($tmp));
    }

    public function createAction(){}

    public function destroyAction(){}

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
