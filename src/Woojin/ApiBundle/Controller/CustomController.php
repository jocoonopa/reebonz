<?php

namespace Woojin\ApiBundle\Controller;

//Third Party
use Nelmio\ApiDocBundle\Annotation\ApiDoc;

//Component
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Exception\InvalidParameterException;
use Symfony\Component\Security\Core\Exception\InsufficientAuthenticationException;
use Symfony\Component\PropertyAccess\PropertyAccess;

//Default
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Cache;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;

// Entity
use Woojin\OrderBundle\Entity\Custom;
use Woojin\OrderBundle;

/**
 * 關於 Custom(客戶) CRUD 動作
 *
 * @Route("/custom")
 */
class CustomController extends Controller
{
    /**
     * 客戶列表
     * 
     * @Route("", name="api_custom_list",options={"expose"=true})
     * @Method("GET")
     *
     * @ApiDoc(
     *  resource=true,
     *  description="取得客戶列表",
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
         * Custom entity array 
         * @var array{ object }
         */
        $customs = $this->getDoctrine()->getRepository('WoojinOrderBundle:Custom')->findAll();

        $serializer = \JMS\Serializer\SerializerBuilder::create()->build();
        
        $jsonCustoms = $serializer->serialize($customs, 'json');

        return new Response($jsonCustoms);
    }

    /**
     * 取得單一客戶實體
     * 
     * @Route("/{id}", requirements={"id" = "\d+"}, name="api_custom_show",options={"expose"=true})
     * @ParamConverter("custom", class="WoojinOrderBundle:Custom")
     * @Method("GET")
     *
     * @ApiDoc(
     *  resource=true,
     *  description="根據傳入的 id 取得單一指定客戶(custom)",
     *  requirements={{"name"="id", "dataType"="integer", "required"=true, "description"="客戶的 id "}},
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
    public function showAction(Custom $custom)
    {
        /**
         * serializer
         * @var object
         */
        $serializer = \JMS\Serializer\SerializerBuilder::create()->build();

        // 序列化使用者實體以利回傳給前端使用使用
        $jsonCustom = $serializer->serialize($custom, 'json');

        return new Response($jsonCustom);
    }

    /**
     * 修改客戶
     * 
     * 
     * @Route("/{id}", requirements={"id" = "\d+"}, name="api_custom_update", options={"expose"=true})
     * @ParamConverter("custom", class="WoojinOrderBundle:Custom")
     * @Method("PUT")
     * 
     * @ApiDoc(
     *  resource=true,
     *  description="更新指定(id)客戶",
     *  requirements={{"name"="id", "dataType"="integer", "required"=true, "description"="客戶的 id "}},
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
    public function updateAction(Custom $custom, Request $request)
    {
       /**
         * 這個工廠將會替我們創建新的商品實體
         * @var object
         */
        $CustomFactory = $this->get('custom.factory');

        /**
         * 提供給工廠的參數陣列
         * @var array
         */
        $settings = array();

        /**
         * Symfony 的屬性套件，透過它可以用物件方式讀寫陣列
         * @var object
         */
        $accessor = PropertyAccess::createPropertyAccessor();

        /**
         * serializer
         * @var object
         */
        $serializer = \JMS\Serializer\SerializerBuilder::create()->build();

        $accessor->setValue($settings, '[setName]', $request->request->get('name'));
        $accessor->setValue($settings, '[setEmail]', $request->request->get('email'));
        $accessor->setValue($settings, '[setMobil]', $request->request->get('mobil'));
        $accessor->setValue($settings, '[setSex]', $request->request->get('sex'));
        $accessor->setValue($settings, '[setAddress]', $request->request->get('password'));
        $accessor->setValue($settings, '[setBirthday]', new \Datetime($request->request->get('birthday')));
        $accessor->setValue($settings, '[setMemo]', $request->request->get('memo'));
       $accessor->setValue($settings, '[setStore]', $this->getDoctrine()->getRepository('WoojinStoreBundle:Store')->find($accessor->getValue($request->request->get('store'), '[id]')));
        
        // 透過工廠產生新的使用者
        $CustomFactory->update($settings, $custom);

        // 序列化使用者實體以利回傳給前端使用使用
        $jsonCustom = $serializer->serialize($custom, 'json');

        return new Response($jsonCustom);
    }

    /**
     * 新增客戶
     * 
     * @Route("", name="api_custom_create", options={"expose"=true})
     * @Method("POST")
     * 
     * @ApiDoc(
     *  resource=true,
     *  description="新增客戶",
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
        /**
         * 這個工廠將會替我們創建新的商品實體
         * @var object
         */
        $CustomFactory = $this->get('custom.factory');

        /**
         * 提供給工廠的參數陣列
         * @var array
         */
        $settings = array();

        /**
         * Symfony 的屬性套件，透過它可以用物件方式讀寫陣列
         * @var object
         */
        $accessor = PropertyAccess::createPropertyAccessor();

        /**
         * serializer
         * @var object
         */
        $serializer = \JMS\Serializer\SerializerBuilder::create()->build();

        $accessor->setValue($settings, '[setName]', $request->request->get('name'));
        $accessor->setValue($settings, '[setEmail]', $request->request->get('email'));
        $accessor->setValue($settings, '[setMobil]', $request->request->get('mobil'));
        $accessor->setValue($settings, '[setSex]', $request->request->get('sex'));
        $accessor->setValue($settings, '[setAddress]', $request->request->get('password'));
        $accessor->setValue($settings, '[setBirthday]', new \Datetime($request->request->get('birthday')));
        $accessor->setValue($settings, '[setMemo]', $request->request->get('memo'));
        $accessor->setValue($settings, '[setStore]', $this->getDoctrine()->getRepository('WoojinStoreBundle:Store')->find($accessor->getValue($request->request->get('store'), '[id]')));
        
        // 透過工廠產生新的使用者
        $custom = $CustomFactory->create($settings);

        // 序列化使用者實體以利回傳給前端使用
        $jsonCustom = $serializer->serialize($custom, 'json');

        return new Response($jsonCustom);
    }

    /**
     * 刪除客戶
     * 
     * 
     * @Route("/{id}", requirements={"id" = "\d+"}, name="api_custom_delete", options={"expose"=true})
     * @ParamConverter("custom", class="WoojinOrderBundle:Custom")
     * @Method("DELETE")
     * 
     * @ApiDoc(
     *  resource=true,
     *  description="刪除指定(id)客戶",
     *  requirements={{"name"="id", "dataType"="integer", "required"=true, "description"="客戶的 id "}},
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
    public function destroyAction(Custom $custom)
    {
        try {
            $em = $this->getDoctrine()->getManager();
            $em->remove($custom);
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
     * 根據搜尋條件取得客戶列表，將搜尋條件以json 格式傳入，其格式如下:
     *
     * {
     *     "Cname": {
     *         "in": (string),
     *         "notIn": (string),
     *         "like": (string)
     *     },
     *     "Cemail": {
     *         "in": (string),
     *         "notIn": (string),
     *         "like": (string)
     *     },
     *     "Cmobil": {
     *         "in": (string),
     *         "notIn": (string),
     *         "like": (string)
     *     },
     *     "Cbirthday": {
     *         "in": (string),
     *         "notIn": (string),
     *         "like": (string)
     *     },
     *     "Caddress": {
     *         "in": (string),
     *         "notIn": (string),
     *         "like": (string)
     *     }
     * }
     *
     * jsonOrderBy: {"attr": 屬性, "dir": "ASC|DESC"},
     * page: 頁數 ,
     * perPage: 每頁幾個,
     * _format: 格式
     *
     * 回傳的資料格式為 json 字串 或是 xlsx檔案，端看您的 _format決定。
     * 以下為json欄位介紹 [{
     *  "count": 總共多少筆資料,
     *  "page": 目前為第幾頁資料,
     *  "perPage": 每頁幾筆資料,
     *  "custom": [{
     *      "name": 客戶名,
     *      "email": 信箱,
     *      "mobil": 手機,
     *      "address": 地址,   
     *  }, ...,]
     * } 
     * 
     * 
     * @Route(
     *     "/filter/{jsonCondition}/{jsonOrderBy}/{page}/{perPage}/{_format}", 
     *     requirements={"page"="\d+", "perPage"="\d+"},
     *     defaults={
     *         "jsonCondition"="{}",
     *         "jsonOrderBy"="{}",
     *         "page"=1,
     *         "perPage"=100,
     *         "_format"="json"
     *     },
     *     name="api_custom_filter",
     *     options={"expose"=true}
     * )
     * @Method("GET")
     *
     * @ApiDoc(
     *  resource=true,
     *  description="根據搜尋條件取得客戶列表",
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
    public function filterAction($jsonCondition, $jsonOrderBy, $page, $perPage, $_format)
    {
        /**
         * 將搜尋條件的 json 字串轉換成搜尋陣列
         * @var array
         */
        $conditions = json_decode($jsonCondition, true);

        /**
         * 排序參數
         * @var array
         */
        $orderBy = json_decode($jsonOrderBy, true);

        /**
         * serializer
         * @var object
         */
        $serializer = \JMS\Serializer\SerializerBuilder::create()->build();

        /**
         * 客戶repo
         * @var object
         */
        $customRepo = $this->getDoctrine()->getRepository('WoojinOrderBundle:Custom');

        /**
         * 回傳資料
         * @var 
         */
        $response = $customRepo->findByFilter($conditions, $orderBy, $page, $perPage);

        $jsonResponse = $serializer->serialize($response, 'json');

        return new Response($jsonResponse);
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
