<?php

namespace Woojin\OrderBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;

/**
 * @Route("/orders")
 */
class OrderController extends Controller
{
	/**
	 * @Route("", name="orders_index", options={"expose"=true})
	 * @Template()
	 * @Method("GET")
	 */
	public function indexAction()
	{
		return array();
	}

	/**
	 * @Route("/normal", name="orders_normal", options={"expose"=true})
	 * @Template()
	 * @Method("GET")
	 */
	public function normalAction() 
	{
		return array();
	}

	/**
	 * @Route("/special", name="orders_special")
	 * @Template()
	 * @Method("GET")
	 */
	public function specialAction () 
	{
		return array();
	}

	/**
	 * @Route("/back", name="orders_back")
	 * @Template()
	 * @Method("GET")
	 */
	public function backAction () 
	{
		return array();
	}
}