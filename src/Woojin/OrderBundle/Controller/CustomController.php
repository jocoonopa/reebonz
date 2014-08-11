<?php

namespace Woojin\OrderBundle\Controller;

use Woojin\OrderBundle\Entity\Custom;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;

/**
 * @Route("/custom")
 */
class CustomController extends Controller
{
	/**
	 * @Route("/", name="custom_index", options={"expose"=true})
	 * @Method("GET")
	 * @Template()
	 */
	public function indexAction()
	{
		/**
		 * 所有的商店實體，會用來形成商店選項
		 * @var object
		 */
		$stores = $this->getDoctrine()->getRepository('WoojinStoreBundle:Store')->findAll();

		/**
		 * Serializer
		 * @var object
		 */
		$serializer = \JMS\Serializer\SerializerBuilder::create()->build();
        
    $jsonStores = $serializer->serialize($stores, 'json');

		return array('stores' => $jsonStores);
	}
}
