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
use Woojin\GoodsBundle\Entity\GoodsMT;

/**
 * 關於 GoodsMT(材質) CRUD 動作，
 *
 * @Route("/goodsMT")
 */
class GoodsMTController extends Controller
{
    /**
     * 取得材質列表
     * 
     * @Route("", name="api_goodsMT_list",options={"expose"=true})
     * @Method("GET")
     *
     * @ApiDoc(
     *  resource=true,
     *  description="取得材質列表",
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
         * GoodsMT entity array 
         * @var array{ object }
         */
        $goodsMTs = $this->getDoctrine()->getRepository('WoojinGoodsBundle:GoodsMT')->findAll();

        $serializer = \JMS\Serializer\SerializerBuilder::create()->build();
        
        $jsonGoodsMTs = $serializer->serialize($goodsMTs, 'json');

        return new Response($jsonGoodsMTs);
    }

    /**
     * 取得單一材質實體
     * 
     * @Route("/{id}", requirements={"id" = "\d+"}, name="api_goodsMT_show",options={"expose"=true})
     * @ParamConverter("goodsMT", class="WoojinGoodsBundle:GoodsMT")
     * @Method("GET")
     *
     * @ApiDoc(
     *  resource=true,
     *  description="根據傳入的 id 取得單一指定材質(goodsMT)",
     *  requirements={{"name"="id", "dataType"="integer", "required"=true, "description"="材質的 id "}},
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
    public function showAction(GoodsMT $goodsMT)
    {
        $serializer = \JMS\Serializer\SerializerBuilder::create()->build();
        
        $jsonGoodsMT = $serializer->serialize($goodsMT, 'json');

        return new Response($jsonGoodsMT);
    }

    /**
     * 修改材質
     * 
     * 
     * @Route("/{id}", requirements={"id" = "\d+"}, name="api_goodsMT_update", options={"expose"=true})
     * @ParamConverter("goodsMT", class="WoojinGoodsBundle:GoodsMT")
     * @Method("PUT")
     * 
     * @ApiDoc(
     *  resource=true,
     *  description="更新指定(id)材質",
     *  requirements={{"name"="id", "dataType"="integer", "required"=true, "description"="材質的 id "}},
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
    public function updateAction(GoodsMT $goodsMT, Request $request)
    {
        $goodsMT->setName($request->request->get('name'));

        $em = $this->getDoctrine()->getManager();
        $em->persist($goodsMT);
        $em->flush();

        $serializer = \JMS\Serializer\SerializerBuilder::create()->build();
        
        $jsonGoodsMT = $serializer->serialize($goodsMT, 'json');

        return new Response($jsonGoodsMT);
    }

    /**
     * 新增材質
     * 
     * @Route("", name="api_goodsMT_create", options={"expose"=true})
     * @Method("POST")
     * 
     * @ApiDoc(
     *  resource=true,
     *  description="新增材質",
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
        $goodsMT = new GoodsMT;
        $goodsMT->setName($request->request->get('name'));
        $em = $this->getDoctrine()->getManager();
        $em->persist($goodsMT);
        $em->flush();

        return new Response(json_encode($goodsMT));
    }

    /**
     * 刪除材質
     * 
     * 
     * @Route("/{id}", requirements={"id" = "\d+"}, name="api_goodsMT_delete", options={"expose"=true})
     * @ParamConverter("goodsMT", class="WoojinGoodsBundle:GoodsMT")
     * @Method("DELETE")
     * 
     * @ApiDoc(
     *  resource=true,
     *  description="刪除指定(id)材質",
     *  requirements={{"name"="id", "dataType"="integer", "required"=true, "description"="材質的 id "}},
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
    public function destroyAction(GoodsMT $goodsMT)
    {
        try {
            $em = $this->getDoctrine()->getManager();
            $em->remove($goodsMT);
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
