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
use Woojin\GoodsBundle\Entity\GoodsSource;

/**
 * 關於 GoodsSource(商品來源) CRUD 動作，
 *
 * @Route("/goodsSource")
 */
class GoodsSourceController extends Controller
{
    /**
     * 取得商品來源列表
     * 
     * @Route("", name="api_goodsSource_list",options={"expose"=true})
     * @Method("GET")
     *
     * @ApiDoc(
     *  resource=true,
     *  description="取得商品來源列表",
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
         * GoodsSource entity array 
         * @var array{ object }
         */
        $goodsSources = $this->getDoctrine()->getRepository('WoojinGoodsBundle:GoodsSource')->findAll();

        $serializer = \JMS\Serializer\SerializerBuilder::create()->build();
        
        $jsonGoodsSources = $serializer->serialize($goodsSources, 'json');

        return new Response($jsonGoodsSources);
    }

    /**
     * 取得單一商品來源實體
     * 
     * @Route("/{id}", requirements={"id" = "\d+"}, name="api_goodsSource_show",options={"expose"=true})
     * @ParamConverter("goodsSource", class="WoojinGoodsBundle:GoodsSource")
     * @Method("GET")
     *
     * @ApiDoc(
     *  resource=true,
     *  description="根據傳入的 id 取得單一指定商品來源(goodsSource)",
     *  requirements={{"name"="id", "dataType"="integer", "required"=true, "description"="商品來源的 id "}},
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
    public function showAction(GoodsSource $goodsSource)
    {
        $serializer = \JMS\Serializer\SerializerBuilder::create()->build();
        
        $jsonGoodsSource = $serializer->serialize($goodsSource, 'json');

        return new Response($jsonGoodsSource);
    }

    /**
     * 修改商品來源
     * 
     * 
     * @Route("/{id}", requirements={"id" = "\d+"}, name="api_goodsSource_update", options={"expose"=true})
     * @ParamConverter("goodsSource", class="WoojinGoodsBundle:GoodsSource")
     * @Method("PUT")
     * 
     * @ApiDoc(
     *  resource=true,
     *  description="更新指定(id)商品來源",
     *  requirements={{"name"="id", "dataType"="integer", "required"=true, "description"="商品來源的 id "}},
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
    public function updateAction(GoodsSource $goodsSource, Request $request)
    {
        $goodsSource->setName($request->request->get('name'));

        $em = $this->getDoctrine()->getManager();
        $em->persist($goodsSource);
        $em->flush();

        $serializer = \JMS\Serializer\SerializerBuilder::create()->build();
        
        $jsonGoodsSource = $serializer->serialize($goodsSource, 'json');

        return new Response($jsonGoodsSource);
    }

    /**
     * 新增商品來源
     * 
     * @Route("", name="api_goodsSource_create", options={"expose"=true})
     * @Method("POST")
     * 
     * @ApiDoc(
     *  resource=true,
     *  description="新增商品來源",
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
        $goodsSource = new GoodsSource;
        $goodsSource->setName($request->request->get('name'));
        
        $em = $this->getDoctrine()->getManager();
        $em->persist($goodsSource);
        $em->flush();

        $serializer = \JMS\Serializer\SerializerBuilder::create()->build();
        
        $jsonGoodsSource = $serializer->serialize($goodsSource, 'json');

        return new Response($jsonGoodsSource);
    }

    /**
     * 刪除商品來源
     * 
     * 
     * @Route("/{id}", requirements={"id" = "\d+"}, name="api_goodsSource_delete", options={"expose"=true})
     * @ParamConverter("goodsSource", class="WoojinGoodsBundle:GoodsSource")
     * @Method("DELETE")
     * 
     * @ApiDoc(
     *  resource=true,
     *  description="刪除指定(id)商品來源",
     *  requirements={{"name"="id", "dataType"="integer", "required"=true, "description"="商品來源的 id "}},
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
    public function destroyAction(GoodsSource $goodsSource)
    {
        try {
            $em = $this->getDoctrine()->getManager();
            $em->remove($goodsSource);
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
