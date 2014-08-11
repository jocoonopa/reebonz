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
use Woojin\GoodsBundle\Entity\Pattern;

/**
 * 關於 Pattern(款式) CRUD 動作，
 *
 * @Route("/pattern")
 */
class PatternController extends Controller
{
    /**
     * 取得款式列表
     * 
     * @Route("", name="api_pattern_list",options={"expose"=true})
     * @Method("GET")
     *
     * @ApiDoc(
     *  resource=true,
     *  description="取得款式列表",
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
         * Pattern entity array 
         * @var array{ object }
         */
        $patterns = $this->getDoctrine()->getRepository('WoojinGoodsBundle:Pattern')->findAll();

        $serializer = \JMS\Serializer\SerializerBuilder::create()->build();
        
        $jsonPatterns = $serializer->serialize($patterns, 'json');

        return new Response($jsonPatterns);
    }

    /**
     * 取得單一款式實體
     * 
     * @Route("/{id}", requirements={"id" = "\d+"}, name="api_pattern_show",options={"expose"=true})
     * @ParamConverter("pattern", class="WoojinGoodsBundle:Pattern")
     * @Method("GET")
     *
     * @ApiDoc(
     *  resource=true,
     *  description="根據傳入的 id 取得單一指定款式(pattern)",
     *  requirements={{"name"="id", "dataType"="integer", "required"=true, "description"="款式的 id "}},
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
    public function showAction(Pattern $pattern)
    {
        $serializer = \JMS\Serializer\SerializerBuilder::create()->build();
        
        $jsonPattern = $serializer->serialize($pattern, 'json');

        return new Response($jsonPattern);
    }

    /**
     * 修改款式
     * 
     * 
     * @Route("/{id}", requirements={"id" = "\d+"}, name="api_pattern_update", options={"expose"=true})
     * @ParamConverter("pattern", class="WoojinGoodsBundle:Pattern")
     * @Method("PUT")
     * 
     * @ApiDoc(
     *  resource=true,
     *  description="更新指定(id)款式",
     *  requirements={{"name"="id", "dataType"="integer", "required"=true, "description"="款式的 id "}},
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
    public function updateAction(Pattern $pattern, Request $request)
    {
        $pattern->setName($request->request->get('name'));

        $em = $this->getDoctrine()->getManager();
        $em->persist($pattern);
        $em->flush();

        return new Response(json_encode($pattern));
    }

    /**
     * 新增款式
     * 
     * @Route("", name="api_pattern_create", options={"expose"=true})
     * @Method("POST")
     * 
     * @ApiDoc(
     *  resource=true,
     *  description="新增款式",
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
        $pattern = new Pattern;
        $pattern->setName($request->request->get('name'));
        $em = $this->getDoctrine()->getManager();
        $em->persist($pattern);
        $em->flush();

        return new Response(json_encode($pattern));
    }

    /**
     * 刪除款式
     * 
     * 
     * @Route("/{id}", requirements={"id" = "\d+"}, name="api_pattern_delete", options={"expose"=true})
     * @ParamConverter("pattern", class="WoojinGoodsBundle:Pattern")
     * @Method("DELETE")
     * 
     * @ApiDoc(
     *  resource=true,
     *  description="刪除指定(id)款式",
     *  requirements={{"name"="id", "dataType"="integer", "required"=true, "description"="款式的 id "}},
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
    public function destroyAction(Pattern $pattern)
    {
        try {
            $em = $this->getDoctrine()->getManager();
            $em->remove($pattern);
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
