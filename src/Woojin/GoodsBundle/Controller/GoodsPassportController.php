<?php

namespace Woojin\GoodsBundle\Controller;

use Woojin\GoodsBundle\Entity\GoodsPassport;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Cache;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;

/**
 * @Route("/goodsPassport")
 */
class GoodsPassportController extends Controller 
{
  /**
   * @Route("", name="goodsPassport_index", options={"expose"=true})
   * @Template()
   * @Method("GET")
   */
  public function indexAction ()
  {        
    return array();
  }

  /**
   * @Route("/partials/listTitle", name="goods_partials_listTitle", options={"expose"=true})
   * @Template("WoojinGoodsBundle:GoodsPassport/partials/res:_listTitle.html.twig")
   * @Method("GET")
   */
  public function listTitleAction()
  {
    return array();
  }

  /**
   * type 有以下幾個可選值，代入後分別對應符合之模版
   * 
   * 1. input
   * 2. select
   * 3. radio
   * 4. date (日期專用)
   * 5. textarea
   * 6. span
   * 7. number
   * 8. status
   *
   * example: $type = input, 則輸出 _input.html.twig 
   * 
   * @Route("/partials/form/{type}", name="goods_partials_form", options={"expose"=true})
   * @Method("GET")
   */
  public function partialsFormAction($type)
  {
    return $this->render('WoojinGoodsBundle:GoodsPassport/partials/form:_' . $type . '.html.twig');
  }
}