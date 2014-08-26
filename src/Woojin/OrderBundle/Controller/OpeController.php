<?php

namespace Woojin\OrderBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;

/**
 * @Route("/ope")
 */
class OpeController extends Controller
{
	/**
	 * @Route("/list", name="ope_partials_list", options={"expose"=true})
	 * @Method("GET")
	 * @Template()
	 */
	public function listAction()
	{
		return array();
	}
}
