<?php

namespace Woojin\SecurityBundle\Controller;

use Woojin\UserBundle\Entity\User;
use Woojin\StoreBundle\Entity\Store;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Security\Core\SecurityContext;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use JMS\SecurityExtraBundle\Annotation\Secure;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;

class SecurityController extends Controller
{
  /**
   * @Route("/login", name="login")
   * @Template("WoojinSecurityBundle:Security:login.html.twig")
   */
  public function loginAction()
  {
	$request = $this->getRequest();
	$session = $request->getSession();

	//$test = new User();
	// get the login error if there is one
	if ($request->attributes->has(SecurityContext::AUTHENTICATION_ERROR)) {
	  $error = $request->attributes->get(
		SecurityContext::AUTHENTICATION_ERROR
	  );
	} else {
	  $error = $session->get(SecurityContext::AUTHENTICATION_ERROR);
	  $session->remove(SecurityContext::AUTHENTICATION_ERROR);
	}

	return  array(
	  // last username entered by the user
	  'last_username' => $session->get(SecurityContext::LAST_USERNAME),
	  'error'         => $error,
	);
  }

   /**
	* @Route("/forgotten", name="forgotten")
	* @Template()
	*/
	public function forgotAction(Request $request)
	{
		$error = false;
		$success = false;
		$email = $request->request->get('email');

		$em = $this->getDoctrine()->getManager();

		$user = $em->getRepository('WoojinUserBundle:User')->findOneBy(array('email' => $email, 'isActive' => true));

		if ($user) {
			$csrf = uniqid();

			$message = \Swift_Message::newInstance()
				->setSubject('Reebonz密碼設定')
				->setFrom('reebonz.tw.marketing@gmail.com')
				->setTo($request->request->get('email'))
				->setBody(
					$this->renderView(
						'WoojinSecurityBundle:Security:mail.html.twig',
						array('user' => $user, 'csrf' => $csrf)
					), 
					'text/html'
				)
			;

			$user->setCsrf($csrf);

			$this->get('mailer')->send($message);
			$em->persist($user);
			$em->flush();

			$success = '您的密碼已經寄出囉，請前往信箱查看';

			$email = false;
		} else {
			$error = (empty($email)) ?  false : '輸入的email不存在';
		}
		
		return array(
			'error' => $error,
			'success' => $success,
			'last_email' => $email
		);
	}

	/**
	 * @Route("/password/{id}/edit/{csrf}", name="password_edit")
	 * @ParamConverter("user", class="WoojinUserBundle:User")
	 * @Template("WoojinSecurityBundle:Security:password.html.twig")
	 */
	public function passwordEditAction(User $user, $csrf, Request $request)
	{
		if ($csrf !== $user->getCsrf()) {
			return new Response('驗證過期，請重新申請密碼更改');
		}

		if ($request->request->get('password', false) && ($request->request->get('password') === $request->request->get('confirm-password'))) {
			/**
			 * Encoder
			 * @var object
			 */
			$encoder = $this->container->get('security.encoder_factory')->getEncoder($user);

			 /**
			 * 加密後的密碼
			 * 
			 * @var string
			 */
			$password = $encoder->encodePassword($request->request->get('password'), $user->getSalt());

			$user
				->setPassword($password)
				->setCsrf(uniqid())
			;

			$em = $this->getDoctrine()->getManager();

			$em->persist($user);
			$em->flush();

			return new Response($this->render('WoojinSecurityBundle:Security:psdSuccess.html.twig'));
		}

		return array('user' => $user);
	}

	/**
	* @Route("/login_check", name="login_check")
	*/
	public function loginCheckAction()
	{

	}

	/**
	* @Route("/logout", name="logout")
	*/
	public function logoutAction()
	{

	}
}