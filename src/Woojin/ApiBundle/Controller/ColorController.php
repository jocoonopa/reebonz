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
use Woojin\GoodsBundle\Entity\Color;

/**
 * 關於 Color(顏色) CRUD 動作，
 *
 * @Route("/color")
 */
class ColorController extends Controller
{
    /**
     * 取得顏色列表
     * 
     * @Route("", name="api_color_list",options={"expose"=true})
     * @Method("GET")
     *
     * @ApiDoc(
     *  resource=true,
     *  description="取得顏色列表",
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
         * Color entity array 
         * @var array{ object }
         */
        $colors = $this->getDoctrine()->getRepository('WoojinGoodsBundle:Color')->findAll();

        $serializer = \JMS\Serializer\SerializerBuilder::create()->build();
        
        $jsonColors = $serializer->serialize($colors, 'json');

        return new Response($jsonColors);
    }

    /**
     * 取得單一顏色實體
     * 
     * @Route("/{id}", requirements={"id" = "\d+"}, name="api_color_show",options={"expose"=true})
     * @ParamConverter("color", class="WoojinGoodsBundle:Color")
     * @Method("GET")
     *
     * @ApiDoc(
     *  resource=true,
     *  description="根據傳入的 id 取得單一指定顏色(color)",
     *  requirements={{"name"="id", "dataType"="integer", "required"=true, "description"="顏色的 id "}},
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
    public function showAction(Color $color)
    {
        $serializer = \JMS\Serializer\SerializerBuilder::create()->build();
        
        $jsonColor = $serializer->serialize($color, 'json');

        return new Response($jsonColor);
    }

    /**
     * 修改顏色
     * 
     * 
     * @Route("/{id}", requirements={"id" = "\d+"}, name="api_color_update", options={"expose"=true})
     * @ParamConverter("color", class="WoojinGoodsBundle:Color")
     * @Method("PUT")
     * 
     * @ApiDoc(
     *  resource=true,
     *  description="更新指定(id)顏色",
     *  requirements={{"name"="id", "dataType"="integer", "required"=true, "description"="顏色的 id "}},
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
    public function updateAction(Color $color, Request $request)
    {
        $color->setName($request->request->get('name'));

        $em = $this->getDoctrine()->getManager();
        $em->persist($color);
        $em->flush();

        $serializer = \JMS\Serializer\SerializerBuilder::create()->build();
        
        $jsonColor = $serializer->serialize($color, 'json');

        return new Response($jsonColor);
    }

    /**
     * 新增顏色
     * 
     * @Route("", name="api_color_create", options={"expose"=true})
     * @Method("POST")
     * 
     * @ApiDoc(
     *  resource=true,
     *  description="新增顏色",
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
        $color = new Color;
        $color->setName($request->request->get('name'));
        $em = $this->getDoctrine()->getManager();
        $em->persist($color);
        $em->flush();

        $serializer = \JMS\Serializer\SerializerBuilder::create()->build();
        
        $jsonColor = $serializer->serialize($color, 'json');

        return new Response($jsonColor);
    }

    /**
     * 刪除顏色
     * 
     * 
     * @Route("/{id}", requirements={"id" = "\d+"}, name="api_color_delete", options={"expose"=true})
     * @ParamConverter("color", class="WoojinGoodsBundle:Color")
     * @Method("DELETE")
     * 
     * @ApiDoc(
     *  resource=true,
     *  description="刪除指定(id)顏色",
     *  requirements={{"name"="id", "dataType"="integer", "required"=true, "description"="顏色的 id "}},
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
    public function destroyAction(Color $color)
    {
        try {
            $em = $this->getDoctrine()->getManager();
            $em->remove($color);
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
