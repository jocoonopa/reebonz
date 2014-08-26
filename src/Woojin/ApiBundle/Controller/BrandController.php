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
use Woojin\GoodsBundle\Entity\Brand;

/**
 * 關於 Brand(品牌) CRUD 動作，
 *
 * @Route("/brand")
 */
class BrandController extends Controller
{
    /**
     * 取得品牌列表
     * 
     * @Route("", name="api_brand_list",options={"expose"=true})
     * @Method("GET")
     *
     * @ApiDoc(
     *  resource=true,
     *  description="取得品牌列表",
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
         * Brand entity array 
         * @var array{ object }
         */
        $brands = $this->getDoctrine()->getRepository('WoojinGoodsBundle:Brand')->findAll();

        $serializer = \JMS\Serializer\SerializerBuilder::create()->build();
        
        $jsonBrands = $serializer->serialize($brands, 'json');

        return new Response($jsonBrands);
    }

    /**
     * 取得單一品牌實體
     * 
     * @Route("/{id}", requirements={"id" = "\d+"}, name="api_brand_show",options={"expose"=true})
     * @ParamConverter("brand", class="WoojinGoodsBundle:Brand")
     * @Method("GET")
     *
     * @ApiDoc(
     *  resource=true,
     *  description="根據傳入的 id 取得單一指定品牌(brand)",
     *  requirements={{"name"="id", "dataType"="integer", "required"=true, "description"="品牌的 id "}},
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
    public function showAction(Brand $brand)
    {
        $serializer = \JMS\Serializer\SerializerBuilder::create()->build();
        
        $jsonBrand = $serializer->serialize($brand, 'json');
        
        return new Response($jsonBrand);
    }

    /**
     * 修改品牌
     * 
     * 
     * @Route("/{id}", requirements={"id" = "\d+"}, name="api_brand_update", options={"expose"=true})
     * @ParamConverter("brand", class="WoojinGoodsBundle:Brand")
     * @Method("PUT")
     * 
     * @ApiDoc(
     *  resource=true,
     *  description="更新指定(id)品牌",
     *  requirements={{"name"="id", "dataType"="integer", "required"=true, "description"="品牌的 id "}},
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
    public function updateAction(Brand $brand, Request $request)
    {
        $brand->setName($request->request->get('name'));

        $em = $this->getDoctrine()->getManager();
        $em->persist($brand);
        $em->flush();

        $serializer = \JMS\Serializer\SerializerBuilder::create()->build();
        
        $jsonBrand = $serializer->serialize($brand, 'json');
        
        return new Response($jsonBrand);
    }

    /**
     * 新增品牌
     * 
     * @Route("", name="api_brand_create", options={"expose"=true})
     * @Method("POST")
     * 
     * @ApiDoc(
     *  resource=true,
     *  description="新增品牌",
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
        $brand = new Brand;
        $brand->setName($request->request->get('name'));
        
        $em = $this->getDoctrine()->getManager();
        $em->persist($brand);
        $em->flush();

        $serializer = \JMS\Serializer\SerializerBuilder::create()->build();
        
        $jsonBrand = $serializer->serialize($brand, 'json');
        
        return new Response($jsonBrand);
    }

    /**
     * 刪除品牌
     * 
     * 
     * @Route("/{id}", requirements={"id" = "\d+"}, name="api_brand_delete", options={"expose"=true})
     * @ParamConverter("brand", class="WoojinGoodsBundle:Brand")
     * @Method("DELETE")
     * 
     * @ApiDoc(
     *  resource=true,
     *  description="刪除指定(id)品牌",
     *  requirements={{"name"="id", "dataType"="integer", "required"=true, "description"="品牌的 id "}},
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
    public function destroyAction(Brand $brand)
    {
        try {
            $em = $this->getDoctrine()->getManager();
            $em->remove($brand);
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
