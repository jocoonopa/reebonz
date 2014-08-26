<?php

namespace Woojin\StoreBundle\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
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

  /**
   * @Route("/{id}/platform", name="activity_platform", options={"expose"=true})
   * @ParamConverter("Activity", class="WoojinStoreBundle:Activity")
   * @Method("GET")
   * @Template()
   */
  public function platformAction(Activity $activity)
  {
    return array('activity' => $activity);
  }
}
