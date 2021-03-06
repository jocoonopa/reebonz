<?php

namespace Woojin\StoreBundle\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

/**
 * Board controller.
 *
 * @Route("/board")
 */
class BoardController extends Controller
{
  /**
   * @Route("/", name="store_board")
   * @Template()
   * @Method("GET")
   */
  public function indexAction()
  {
    return array();
  }
}