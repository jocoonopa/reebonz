<?php

namespace Woojin\OrderBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;

/**
 * @Route("/move")
 */
class MoveController extends Controller
{
	/**
	 * @Route("", name="move_index", options={"expose"=true})
	 * @Template()
	 * @Method("GET")
	 */
	public function indexAction()
	{
		return array();
	}
}