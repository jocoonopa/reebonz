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
use Woojin\UserBundle\Entity\Role;

/**
 * 關於 Role(權限) CRUD 動作，
 *
 * @Route("/role")
 */
class RoleController extends Controller
{
    /**
     * 權限列表
     * 
     * @Route("", name="api_role_list",options={"expose"=true})
     * @Method("GET")
     *
     * @ApiDoc(
     *  resource=true,
     *  description="取得權限列表",
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
         * Role entity array 
         * @var array{ object }
         */
        $roles = $this->getDoctrine()->getRepository('WoojinUserBundle:Role')->findAll();

        $serializer = \JMS\Serializer\SerializerBuilder::create()->build();
        
        $jsonRoles = $serializer->serialize($roles, 'json');

        return new Response($jsonRoles);
    }

    /**
     * 取得單一權限實體
     * 
     * @Route("/{id}", requirements={"id" = "\d+"}, name="api_role_show",options={"expose"=true})
     * @ParamConverter("role", class="WoojinUserBundle:Role")
     * @Method("GET")
     *
     * @ApiDoc(
     *  resource=true,
     *  description="根據傳入的 id 取得單一指定權限(role)",
     *  requirements={{"name"="id", "dataType"="integer", "required"=true, "description"="權限的 id "}},
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
    public function showAction(Role $role)
    {
        /**
         * serializer
         * @var object
         */
        $serializer = \JMS\Serializer\SerializerBuilder::create()->build();

        // 序列化使用者實體以利回傳給前端使用使用
        $jsonRole = $serializer->serialize($role, 'json');

        return new Response($jsonRole);
    }

    /**
     * 修改權限
     * 
     * 
     * @Route("/{id}", requirements={"id" = "\d+"}, name="api_role_update", options={"expose"=true})
     * @ParamConverter("role", class="WoojinUserBundle:Role")
     * @Method("PUT")
     * 
     * @ApiDoc(
     *  resource=true,
     *  description="更新指定(id)權限",
     *  requirements={{"name"="id", "dataType"="integer", "required"=true, "description"="權限的 id "}},
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
    public function updateAction(Role $role, Request $request)
    {
        $role
            ->setName($request->request->get('name'))
            ->setRole($request->request->get('role'))
        ;

        $em = $this->getDoctrine()->getManager();
        $em->persist($role);
        $em->flush();

        return new Response(json_encode($role));
    }

    /**
     * 新增權限
     * 
     * @Route("", name="api_role_create", options={"expose"=true})
     * @Method("POST")
     * 
     * @ApiDoc(
     *  resource=true,
     *  description="新增權限",
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
        $role = new Role;
        $role
            ->setName($request->request->get('name'))
            ->setRole($request->request->get('role'))
        ;
        
        $em = $this->getDoctrine()->getManager();
        $em->persist($role);
        $em->flush();

        return new Response(json_encode($role));
    }

    /**
     * 刪除權限
     * 
     * 
     * @Route("/{id}", requirements={"id" = "\d+"}, name="api_role_delete", options={"expose"=true})
     * @ParamConverter("role", class="WoojinUserBundle:Role")
     * @Method("DELETE")
     * 
     * @ApiDoc(
     *  resource=true,
     *  description="刪除指定(id)權限",
     *  requirements={{"name"="id", "dataType"="integer", "required"=true, "description"="權限的 id "}},
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
    public function destroyAction(Role $role)
    {
        try {
            $em = $this->getDoctrine()->getManager();
            $em->remove($role);
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
