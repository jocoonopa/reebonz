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
use Woojin\StoreBundle\Entity\Store;

/**
 * 關於 Store(店家) CRUD 動作，
 *
 * @Route("/store")
 */
class StoreController extends Controller
{
    /**
     * 取得店家列表
     * 
     * @Route("", name="api_store_list",options={"expose"=true})
     * @Method("GET")
     *
     * @ApiDoc(
     *  resource=true,
     *  description="取得店家列表",
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
         * Store entity array 
         * @var array{ object }
         */
        $stores = $this->getDoctrine()->getRepository('WoojinStoreBundle:Store')->findAll();

        $serializer = \JMS\Serializer\SerializerBuilder::create()->build();
        
        $jsonStores = $serializer->serialize($stores, 'json');

        return new Response($jsonStores);
    }

    /**
     * 取得單一店家實體
     * 
     * @Route("/{id}", requirements={"id" = "\d+"}, name="api_store_show",options={"expose"=true})
     * @ParamConverter("store", class="WoojinStoreBundle:Store")
     * @Method("GET")
     *
     * @ApiDoc(
     *  resource=true,
     *  description="根據傳入的 id 取得單一指定店家(store)",
     *  requirements={{"name"="id", "dataType"="integer", "required"=true, "description"="店家的 id "}},
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
    public function showAction(Store $store)
    {
        $serializer = \JMS\Serializer\SerializerBuilder::create()->build();
        
        $jsonStore = $serializer->serialize($store, 'json');

        return new Response($jsonStore);
    }

    /**
     * 修改店家
     * 
     * 
     * @Route("/{id}", requirements={"id" = "\d+"}, name="api_store_update", options={"expose"=true})
     * @ParamConverter("store", class="WoojinStoreBundle:Store")
     * @Method("PUT")
     * 
     * @ApiDoc(
     *  resource=true,
     *  description="更新指定(id)店家",
     *  requirements={{"name"="id", "dataType"="integer", "required"=true, "description"="店家的 id "}},
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
    public function updateAction(Store $store, Request $request)
    {
        $store
            ->setName($request->request->get('name'))
            ->setSn($request->request->get('sn'))
            ->setExchangeRate($request->request->get('exchange_rate_id'))
        ;

        $em = $this->getDoctrine()->getManager();
        $em->persist($store);
        $em->flush();

        return new Response(json_encode($store));
    }

    /**
     * 新增店家
     * 
     * @Route("", name="api_store_create", options={"expose"=true})
     * @Method("POST")
     * 
     * @ApiDoc(
     *  resource=true,
     *  description="新增店家",
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
        $store = new Store;
        $store
            ->setName($request->request->get('name'))
            ->setSn($request->request->get('sn'))
            ->setExchangeRate($request->request->get('exchange_rate_id'))
        ;
        
        $em = $this->getDoctrine()->getManager();
        $em->persist($store);
        $em->flush();

        return new Response(json_encode($store));
    }

    /**
     * 刪除店家
     * 
     * 
     * @Route("/{id}", requirements={"id" = "\d+"}, name="api_store_delete", options={"expose"=true})
     * @ParamConverter("store", class="WoojinStoreBundle:Store")
     * @Method("DELETE")
     * 
     * @ApiDoc(
     *  resource=true,
     *  description="刪除指定(id)店家",
     *  requirements={{"name"="id", "dataType"="integer", "required"=true, "description"="店家的 id "}},
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
    public function destroyAction(Store $store)
    {
        try {
            $em = $this->getDoctrine()->getManager();
            $em->remove($store);
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
