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
use Woojin\GoodsBundle\Entity\GoodsStatus;

/**
 * 關於 GoodsStatus(商品狀態) CRUD 動作，
 *
 * @Route("/goodsStatus")
 */
class GoodsStatusController extends Controller
{
    /**
     * 取得商品狀態列表
     * 
     * @Route("", name="api_goodsStatus_list",options={"expose"=true})
     * @Method("GET")
     *
     * @ApiDoc(
     *  resource=true,
     *  description="取得商品狀態列表",
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
         * GoodsStatus entity array 
         * @var array{ object }
         */
        $goodsStatuss = $this->getDoctrine()->getRepository('WoojinGoodsBundle:GoodsStatus')->findAll();

        $serializer = \JMS\Serializer\SerializerBuilder::create()->build();
        
        $jsonGoodsStatuss = $serializer->serialize($goodsStatuss, 'json');

        return new Response($jsonGoodsStatuss);
    }

    /**
     * 取得單一商品狀態實體
     * 
     * @Route("/{id}", requirements={"id" = "\d+"}, name="api_goodsStatus_show",options={"expose"=true})
     * @ParamConverter("goodsStatus", class="WoojinGoodsBundle:GoodsStatus")
     * @Method("GET")
     *
     * @ApiDoc(
     *  resource=true,
     *  description="根據傳入的 id 取得單一指定商品狀態(goodsStatus)",
     *  requirements={{"name"="id", "dataType"="integer", "required"=true, "description"="商品狀態的 id "}},
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
    public function showAction(GoodsStatus $goodsStatus)
    {
        $serializer = \JMS\Serializer\SerializerBuilder::create()->build();
        
        $jsonGoodsStatus = $serializer->serialize($goodsStatus, 'json');

        return new Response($jsonGoodsStatus);
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
