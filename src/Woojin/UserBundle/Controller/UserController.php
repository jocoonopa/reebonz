<?php

namespace Woojin\UserBundle\Controller;

use Woojin\UserBundle\Entity\User;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Cache;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;

/**
 * @Route("/user")
 */
class UserController extends Controller
{
	/**
	 * @Route("/", name="user_index", options={"expose"=true})
	 * @Method("GET")
	 * @Template()
	 */
	public function indexAction()
	{
		return array();
	}

    /**
     * 更改目前使用者的密碼
     * 
     * @Route("/password_edit", name="user_password_edit", options={"expose"=true})
     * @Method("GET")
     * @Template()
     */
    public function editPasswordAction()
    {
        return array();
    }
}
