<?php

namespace Woojin\StoreBundle\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Woojin\StoreBundle\Entity\Activity;

/**
 * Activity controller.
 *
 * @Route("/activity")
 */
class ActivityController extends Controller
{
  /**
   * @Route("", name="activity_index", options={"expose"=true})
   * @Method("GET")
   * @Template()
   */
  public function indexAction()
  {
    return array();
  }
}
