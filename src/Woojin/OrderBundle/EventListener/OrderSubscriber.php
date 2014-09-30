<?php

namespace Woojin\OrderBundle\EventListener;

use Doctrine\ORM\Event\LifecycleEventArgs;
use Woojin\GoodsBundle\Entity\GoodsPassport;
use Woojin\OrderBundle\Entity\Orders;

use Symfony\Component\Serializer\Exception\Exception;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Security\Core\SecurityContext;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\PropertyAccess\PropertyAccess;

use Doctrine\Common\EventSubscriber;

/**
*/
class OrderSubscriber implements EventSubscriber
{
	const GS_ONSALE         = 1;
	const GS_SOLDOUT        = 2;
	const GS_MOVING         = 3;
	const GS_OFFSALE        = 4;
	const GS_OTHERSTORE     = 5;
	const GS_ACTIVITY       = 6;
	const OS_HANDLING       = 1;
	const OS_COMPLETE       = 2;
	const OS_CANCEL         = 3;
	const OK_IN             = 1;
	const OK_EXCHANGE_IN    = 2;
	const OK_TURN_IN        = 3;
	const OK_MOVE_IN        = 4;
	const OK_CONSIGN_IN     = 5;
	const OK_OUT            = 6;
	const OK_EXCHANGE_OUT   = 7;
	const OK_TURN_OUT       = 8;
	const OK_MOVE_OUT       = 9;
	const OK_FEEDBACK       = 10;
	const OK_WEB_OUT        = 11;
	const OK_SPECIAL_SELL   = 12;
	const OK_SAME_BS        = 13;
	const PT_CASH           = 1;
	const OK_TYPE_INSTORE   = 0;
	const OK_TYPE_OUTSTORE  = 2;
	const OP_TYPE_CREATE    = 1;
	const OP_TYPE_UPDATE    = 2;
	const OP_TYPE_DELETE    = 3;

	protected $container;

	public function __construct(ContainerInterface $container)
	{
		$this->container = $container;
	}

	public function getSubscribedEvents()
	{
		return array(
			'postPersist',
			'postUpdate',
		);
	}

	/**
	 * 訂單新增觸發動作
	 */
	public function postPersist(LifecycleEventArgs $args)
	{
		/**
		 * 抓取的訂單實體
		 * @var object
		 */
		$order = $args->getEntity();

		/**
		 * Entity Manager
		 * @var object
		 */
		$em = $args->getEntityManager();

		/**
		 * 取得目前的 request 實體，這邊千萬不能用 Request::createFromGlobals，
		 * 因為這樣做會使得在 tmp 的上傳檔案被消除，而新的 request 根據 path 去問檔案會問不到因此而報錯
		 * 
		 * @var object
		 */
		$request = $this->container->get('request');

		/**
		 * 信用卡號
		 * 
		 * @var [string]
		 */
		$cardSn = $request->request->get('card_sn');

		/**
		 * 付費方式實體
		 * 
		 * @var \Woojin\OrderBundle\Entity\payType
		 */
		$payType = $em->getRepository('WoojinOrderBundle:PayType')->find($request->request->get('pay_type', self::PT_CASH));

		/**
		 * 操作記錄工廠
		 * @var object
		 */
		$OpeFactory = $this->container->get('ope.factory');

		/**
		* 目前的使用者
		* @var object
		*/
		$user = $this->container->get('security.context')->getToken()->getUser();

		/**
		 * Serializer
		 * @var object
		 */
		$serializer = \JMS\Serializer\SerializerBuilder::create()->build();

		// 若不是 Orders 得實體則不動作
		if (!($order instanceof Orders)) {   
			return;
		}

		/**
		* 操作記錄工廠參數設置
		* @var array
		*/
		$settings = array(
			'setOrders' => $order,
			'setPayType' => $payType,
			'setType' => 'C',
			'setCardSn' => $cardSn,
			'setMoney' => $order->getPaid(),
			'setContent' => $request->request->get('content', '無特別記錄'),
			'setUser' => $user
		);

		$OpeFactory->create($settings);

		return $this;
	}

	public function postUpdate(LifecycleEventArgs $args)
	{
		/**
		* 抓取的訂單實體
		* @var object
		*/
		$order = $args->getEntity();

		/**
		* Entity Manager
		* @var object
		*/
		$em = $args->getEntityManager();

		/**
		* 取得訂單關連的所有ope
		* 
		* @var \Woojin\OrderBundle\Entity\Ope
		*/
		$opes = $em->getRepository('WoojinOrderBundle:Ope')->findBy(array('orders' => $order), array('id' => 'DESC'));

		if (isset($ope[0])) {
			$content = json_decode($ope[0]->getContent(), true);

			$opeLastPaid = $content['paid'];
		} else {
			$opeLastPaid = 0;
		}

		/**
		 * 取得目前的 request 實體，這邊千萬不能用 Request::createFromGlobals，
		 * 因為這樣做會使得在 tmp 的上傳檔案被消除，而新的 request 根據 path 去問檔案會問不到因此而報錯
		 * 
		 * @var object
		 */
		$request = $this->container->get('request');

		/**
		 * 信用卡號
		 * 
		 * @var [string]
		 */
		$cardSn = $request->request->get('card_sn');

		/**
		 * 付費方式實體
		 * 
		 * @var \Woojin\OrderBundle\Entity\payType
		 */
		$payType = $em->find('WoojinOrderBundle:PayType', $request->request->get('pay_type', 0));

		/**
		 * 操作記錄工廠
		 * 
		 * @var object
		 */
		$OpeFactory = $this->container->get('ope.factory');

		/**
		 * 目前的使用者
		 * 
		 * @var object
		 */
		$user = $this->container->get('security.context')->getToken()->getUser();

		/**
		 * 提供給工廠的參數陣列
		 * 
		 * @var array
		 */
		$settings = array();

		// 若不是 Orders 得實體則不動作
		if (!($order instanceof Orders)) {   
			return;
		}

		$type = ($order->getStatus()->getId() === self::OS_CANCEL) ? self::OP_TYPE_DELETE : self::OP_TYPE_UPDATE; 

		/**
		 * 操作記錄工廠參數設置
		 * @var array
		 */
		$settings = array(
			'setOrders' => $order,
			'setPayType' => $payType,
			'setType' => $type,
			'setCardSn' => $cardSn,
			'setMoney' => $request->request->get('diff', 0),
			'setContent' => $request->request->get('content', '無特別記錄'),
			'setUser' => $user, 
		);

		$OpeFactory->create($settings);
	}
}