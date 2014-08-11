<?php

namespace Woojin\BackendBundle\Controller;

use Woojin\UserBundle\Entity\UsersLog;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;

class IndexController extends Controller
{
  /**
   * backend 的layout
   * 
   * @Route("/", name="backend")
   * @Template()
   * @Method("GET")
   */
  public function indexAction()
  {
    return array();
  }

  /**
   * backend 的首頁內容，會透過 angular 的 xhr 被呼叫
   * 
   * @Route("/body", name="backend_index_body", options={"expose"=true})
   * @Template()
   * @Method("GET")
   */
  public function bodyAction()
  {
  	/**
  	 * 目前登入的使用者
  	 * @var object
  	 */
  	$user = $this->get('security.context')->getToken()->getUser();

  	/**
  	 * 該使用者登入記錄實體取得
  	 * @var array{object}
  	 */
  	$userLogs = $this->getDoctrine()->getRepository('WoojinUserBundle:UsersLog')->findBy(array('user' => $user->getId()), array('create_at' => 'DESC'));

  	/**
  	 * 最後登入記錄實體
  	 * @var object
  	 */
  	$lastLog = $this->get('user_service')->getLastLoginLog($userLogs);

  	return array('lastLog' => $lastLog);
  }

  /**
   * 使用者登入記錄，動作完成後轉向至 backend
   * 
   * @Route("/log")
   * @Method("GET")
   */
  public function logAction(Request $request)
  {
  	/**
  	 * 目前登入的使用者
  	 * @var object
  	 */
  	$user = $this->get('security.context')->getToken()->getUser();
		
		/**
		 * 使用者的ip
		 * @var string
		 */
		$userIp = $request->server->get('REMOTE_ADDR');
		
		/**
		 * Symfony 的 route 定義名稱,
		 * backend 相當於 http:/yourhost/backend/
		 * 
		 * @var string
		 */
		$routeName = 'backend';

		/**
		 * 新增使用者登入記錄實體
		 * @var object
		 */
		$usersLog = new UsersLog;
		$usersLog
			->setUser($user)
			->setIp($userIp)
		;

		$em = $this->getDoctrine()->getManager();
		$em->persist($usersLog);
		$em->flush();
		
		return $this->redirect($this->generateUrl($routeName), 301);
  }

  /**
   * @Route("/partials/msgFooter", name="backend_partials_msgFooter", options={"expose"=true})
   * @Template("WoojinBackendBundle:partials:_msgFooter.html.twig")
   * @Method("GET")
   */
  public function msgFooterAction()
  {
    return array();
  }
}
