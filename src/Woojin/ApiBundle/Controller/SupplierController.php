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
use Woojin\GoodsBundle\Entity\Supplier;

/**
 * 關於 Supplier(供貨商) CRUD 動作，
 *
 * @Route("/supplier")
 */
class SupplierController extends Controller
{
    /**
     * 取得供貨商列表
     * 
     * @Route("", name="api_supplier_list",options={"expose"=true})
     * @Method("GET")
     *
     * @ApiDoc(
     *  resource=true,
     *  description="取得供貨商列表",
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
         * Supplier entity array 
         * @var array{ object }
         */
        $suppliers = $this->getDoctrine()->getRepository('WoojinGoodsBundle:Supplier')->findAll();

        $serializer = \JMS\Serializer\SerializerBuilder::create()->build();
        
        $jsonSuppliers = $serializer->serialize($suppliers, 'json');

        return new Response($jsonSuppliers);
    }

    /**
     * 取得單一供貨商實體
     * 
     * @Route("/{id}", requirements={"id" = "\d+"}, name="api_supplier_show",options={"expose"=true})
     * @ParamConverter("supplier", class="WoojinGoodsBundle:Supplier")
     * @Method("GET")
     *
     * @ApiDoc(
     *  resource=true,
     *  description="根據傳入的 id 取得單一指定供貨商(supplier)",
     *  requirements={{"name"="id", "dataType"="integer", "required"=true, "description"="供貨商的 id "}},
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
    public function showAction(Supplier $supplier)
    {
        $serializer = \JMS\Serializer\SerializerBuilder::create()->build();
        
        $jsonSupplier = $serializer->serialize($supplier, 'json');

        return new Response($jsonSupplier);
    }

    /**
     * 修改供貨商
     * 
     * 
     * @Route("/{id}", requirements={"id" = "\d+"}, name="api_supplier_update", options={"expose"=true})
     * @ParamConverter("supplier", class="WoojinGoodsBundle:Supplier")
     * @Method("PUT")
     * 
     * @ApiDoc(
     *  resource=true,
     *  description="更新指定(id)供貨商",
     *  requirements={{"name"="id", "dataType"="integer", "required"=true, "description"="供貨商的 id "}},
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
    public function updateAction(Supplier $supplier, Request $request)
    {
        $supplier->setName($request->request->get('name'));

        $em = $this->getDoctrine()->getManager();
        $em->persist($supplier);
        $em->flush();

        return new Response(json_encode($supplier));
    }

    /**
     * 新增供貨商
     * 
     * @Route("", name="api_supplier_create", options={"expose"=true})
     * @Method("POST")
     * 
     * @ApiDoc(
     *  resource=true,
     *  description="新增供貨商",
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
        $supplier = new Supplier;
        $supplier->setName($request->request->get('name'));
        $em = $this->getDoctrine()->getManager();
        $em->persist($supplier);
        $em->flush();

        return new Response(json_encode($supplier));
    }

    /**
     * 刪除供貨商
     * 
     * 
     * @Route("/{id}", requirements={"id" = "\d+"}, name="api_supplier_delete", options={"expose"=true})
     * @ParamConverter("supplier", class="WoojinGoodsBundle:Supplier")
     * @Method("DELETE")
     * 
     * @ApiDoc(
     *  resource=true,
     *  description="刪除指定(id)供貨商",
     *  requirements={{"name"="id", "dataType"="integer", "required"=true, "description"="供貨商的 id "}},
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
    public function destroyAction(Supplier $supplier)
    {
        try {
            $em = $this->getDoctrine()->getManager();
            $em->remove($supplier);
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
