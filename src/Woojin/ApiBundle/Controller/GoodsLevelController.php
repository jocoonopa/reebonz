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
use Woojin\GoodsBundle\Entity\GoodsLevel;

/**
 * 關於 GoodsLevel(新舊程度) CRUD 動作，
 *
 * @Route("/goodsLevel")
 */
class GoodsLevelController extends Controller
{
    /**
     * 取得新舊程度列表
     * 
     * @Route("", name="api_goodsLevel_list",options={"expose"=true})
     * @Method("GET")
     *
     * @ApiDoc(
     *  resource=true,
     *  description="取得新舊程度列表",
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
         * GoodsLevel entity array 
         * @var array{ object }
         */
        $goodsLevels = $this->getDoctrine()->getRepository('WoojinGoodsBundle:GoodsLevel')->findAll();

        $serializer = \JMS\Serializer\SerializerBuilder::create()->build();
        
        $jsonGoodsLevels = $serializer->serialize($goodsLevels, 'json');

        return new Response($jsonGoodsLevels);
    }

    /**
     * 取得單一新舊程度實體
     * 
     * @Route("/{id}", requirements={"id" = "\d+"}, name="api_goodsLevel_show",options={"expose"=true})
     * @ParamConverter("goodsLevel", class="WoojinGoodsBundle:GoodsLevel")
     * @Method("GET")
     *
     * @ApiDoc(
     *  resource=true,
     *  description="根據傳入的 id 取得單一指定新舊程度(goodsLevel)",
     *  requirements={{"name"="id", "dataType"="integer", "required"=true, "description"="新舊程度的 id "}},
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
    public function showAction(GoodsLevel $goodsLevel)
    {
        $serializer = \JMS\Serializer\SerializerBuilder::create()->build();
        
        $jsonGoodsLevel = $serializer->serialize($goodsLevel, 'json');

        return new Response($jsonGoodsLevel);
    }

    /**
     * 修改新舊程度
     * 
     * 
     * @Route("/{id}", requirements={"id" = "\d+"}, name="api_goodsLevel_update", options={"expose"=true})
     * @ParamConverter("goodsLevel", class="WoojinGoodsBundle:GoodsLevel")
     * @Method("PUT")
     * 
     * @ApiDoc(
     *  resource=true,
     *  description="更新指定(id)新舊程度",
     *  requirements={{"name"="id", "dataType"="integer", "required"=true, "description"="新舊程度的 id "}},
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
    public function updateAction(GoodsLevel $goodsLevel, Request $request)
    {
        $goodsLevel->setName($request->request->get('name'));

        $em = $this->getDoctrine()->getManager();
        $em->persist($goodsLevel);
        $em->flush();

        return new Response(json_encode($goodsLevel));
    }

    /**
     * 新增新舊程度
     * 
     * @Route("", name="api_goodsLevel_create", options={"expose"=true})
     * @Method("POST")
     * 
     * @ApiDoc(
     *  resource=true,
     *  description="新增新舊程度",
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
        $goodsLevel = new GoodsLevel;
        $goodsLevel->setName($request->request->get('name'));
        $em = $this->getDoctrine()->getManager();
        $em->persist($goodsLevel);
        $em->flush();

        return new Response(json_encode($goodsLevel));
    }

    /**
     * 刪除新舊程度
     * 
     * 
     * @Route("/{id}", requirements={"id" = "\d+"}, name="api_goodsLevel_delete", options={"expose"=true})
     * @ParamConverter("goodsLevel", class="WoojinGoodsBundle:GoodsLevel")
     * @Method("DELETE")
     * 
     * @ApiDoc(
     *  resource=true,
     *  description="刪除指定(id)新舊程度",
     *  requirements={{"name"="id", "dataType"="integer", "required"=true, "description"="新舊程度的 id "}},
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
    public function destroyAction(GoodsLevel $goodsLevel)
    {
        try {
            $em = $this->getDoctrine()->getManager();
            $em->remove($goodsLevel);
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
