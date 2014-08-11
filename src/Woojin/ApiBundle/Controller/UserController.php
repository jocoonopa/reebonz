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

use Lsw\SecureControllerBundle\Annotation\Secure;

// Entity
use Woojin\UserBundle\Entity\User;

/**
 * 關於 User(後台使用者) CRUD 動作，
 *
 * @Route("/user")
 */
class UserController extends Controller
{
    /**
     * 後台使用者列表
     * 
     * @Route("", name="api_user_list",options={"expose"=true})
     * @Method("GET")
     *
     * @ApiDoc(
     *  resource=true,
     *  description="取得後台使用者列表",
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
         * User entity array 
         * @var array{ object }
         */
        $users = $this->getDoctrine()->getRepository('WoojinUserBundle:User')->findAll();

        $serializer = \JMS\Serializer\SerializerBuilder::create()->build();
        
        $jsonUsers = $serializer->serialize($users, 'json');

        return new Response($jsonUsers);
    }

    /**
     * 取得單一後台使用者實體
     * 
     * @Route("/{id}", requirements={"id" = "\d+"}, name="api_user_show",options={"expose"=true})
     * @ParamConverter("user", class="WoojinUserBundle:User")
     * @Method("GET")
     *
     * @ApiDoc(
     *  resource=true,
     *  description="根據傳入的 id 取得單一指定後台使用者(user)",
     *  requirements={{"name"="id", "dataType"="integer", "required"=true, "description"="後台使用者的 id "}},
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
    public function showAction(User $user)
    {
        /**
         * serializer
         * @var object
         */
        $serializer = \JMS\Serializer\SerializerBuilder::create()->build();

        // 序列化使用者實體以利回傳給前端使用使用
        $jsonUser = $serializer->serialize($user, 'json');

        return new Response($jsonUser);
    }

    /**
     * 修改後台使用者
     * 
     * 
     * @Route("/{id}", requirements={"id" = "\d+"}, name="api_user_update", options={"expose"=true})
     * @ParamConverter("user", class="WoojinUserBundle:User")
     * @Method("PUT")
     * 
     * @ApiDoc(
     *  resource=true,
     *  description="更新指定(id)後台使用者",
     *  requirements={{"name"="id", "dataType"="integer", "required"=true, "description"="後台使用者的 id "}},
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
    public function updateAction(User $user, Request $request)
    {
       /**
         * 這個工廠將會替我們創建新的商品實體
         * @var object
         */
        $UserFactory = $this->get('user.factory');

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

        /**
         * Angular 布林值傳過來會變成字串，目前不曉得怎們解決，只好很蠢的先自己硬轉乘布林了
         * @var boolean
         */
        $isActive = ($request->request->get('is_active', 'true') === 'true') ? true : false;

        $accessor->setValue($settings, '[addRole]', $this->getDoctrine()->getRepository('WoojinUserBundle:Role')->find($accessor->getValue($request->request->get('roles'), '[0][id]')));
        $accessor->setValue($settings, '[setRealname]', $request->request->get('realname'));
        $accessor->setValue($settings, '[setUsername]', $request->request->get('username'));
        $accessor->setValue($settings, '[setEmail]', $request->request->get('email'));
        $accessor->setValue($settings, '[setMobil]', $request->request->get('mobil'));
        $accessor->setValue($settings, '[setChmod]', $request->request->get('chmod', 555));
        $accessor->setValue($settings, '[setIsActive]', $isActive);
        $accessor->setValue($settings, '[setCsrf]', uniqid());
        $accessor->setValue($settings, '[setStore]', $this->getDoctrine()->getRepository('WoojinStoreBundle:Store')->find($accessor->getValue($request->request->get('store'), '[id]')));
        
        // 透過工廠產生新的使用者
        $UserFactory->update($settings, $user);

        // 序列化使用者實體以利回傳給前端使用使用
        $jsonUser = $serializer->serialize($user, 'json');

        return new Response($jsonUser);
    }

    /**
     * 新增後台使用者
     * 
     * @Route("", name="api_user_create", options={"expose"=true})
     * @Method("POST")
     * 
     * @ApiDoc(
     *  resource=true,
     *  description="新增後台使用者",
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
        $UserFactory = $this->get('user.factory');

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

        /**
         * Angular 布林值傳過來會變成字串，目前不曉得怎們解決，只好很蠢的先自己硬轉乘布林了
         * @var boolean
         */
        $isActive = ($request->request->get('is_active', 'true') === 'true') ? true : false;

        $accessor->setValue($settings, '[addRole]', $this->getDoctrine()->getRepository('WoojinUserBundle:Role')->find($accessor->getValue($request->request->get('role'), '[id]')));
        $accessor->setValue($settings, '[setRealname]', $request->request->get('realname'));
        $accessor->setValue($settings, '[setUsername]', $request->request->get('username'));
        $accessor->setValue($settings, '[setEmail]', $request->request->get('email'));
        $accessor->setValue($settings, '[setMobil]', $request->request->get('mobil'));
        $accessor->setValue($settings, '[setChmod]', $request->request->get('chmod', 555));
        $accessor->setValue($settings, '[setPassword]', $request->request->get('password'));
        $accessor->setValue($settings, '[setIsActive]', $isActive);
        $accessor->setValue($settings, '[setCsrf]', uniqid());
        $accessor->setValue($settings, '[setStore]', $this->getDoctrine()->getRepository('WoojinStoreBundle:Store')->find($accessor->getValue($request->request->get('store'), '[id]')));
        
        // 透過工廠產生新的使用者
        $user = $UserFactory->create($settings);

        // 序列化使用者實體以利回傳給前端使用
        $jsonUser = $serializer->serialize($user, 'json');

        return new Response($jsonUser);
    }

    /**
     * 刪除後台使用者
     * 
     * @Secure(roles="ROLE_CHIEF_ADMIN")
     * @Route("/{id}", requirements={"id" = "\d+"}, name="api_user_delete", options={"expose"=true})
     * @ParamConverter("user", class="WoojinUserBundle:User")
     * @Method("DELETE")
     * 
     * @ApiDoc(
     *  resource=true,
     *  description="刪除指定(id)後台使用者，店長以上等級才可使用",
     *  requirements={{"name"="id", "dataType"="integer", "required"=true, "description"="後台使用者的 id "}},
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
    public function destroyAction(User $user)
    {
        /**
         * 目前的使用者
         * @var object
         */
        $userNow = $this->container->get('security.context')->getToken()->getUser();

        if ($userNow === $user) {
            return new Exception('Suicide is now allow');
        }

        try {
            /**
             * Entity Manager
             * @var object
             */
            $em = $this->getDoctrine()->getManager();

            $em->remove($user);
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
     * 取得目前使用者的資訊
     * 
     * @Route("/current", name="api_user_current", options={"expose"=true})
     * @Method("GET")
     * 
     * @ApiDoc(
     *  resource=true,
     *  description="取得目前使用者資料",
     *  requirements={{"name"="id", "dataType"="integer", "required"=true, "description"="後台使用者的 id "}},
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
    public function currentAction()
    {
        /**
         * 使用者
         * 
         * @var Woojin\UserBundle\Entity\User
         */
        $user = $this->get('security.context')->getToken()->getUser();

        /**
         * serializer
         * 
         * @var \JMS\Serializer\SerializerBuilder
         */
        $serializer = \JMS\Serializer\SerializerBuilder::create()->build();

        // 序列化使用者實體以利回傳給前端使用
        $jsonUser = $serializer->serialize($user, 'json');

        return new Response($jsonUser);
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
