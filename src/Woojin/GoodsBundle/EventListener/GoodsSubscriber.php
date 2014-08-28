<?php

namespace Woojin\GoodsBundle\EventListener;

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
 * 此類別主要處理商品新增和更動時連帶的動作，舊版的系統是直接把動作寫死在controller裡，東西大了以後相當難維護擴充，
 * 現在改成寫在 listener 並且將其他關聯實體的產生都透過 service container 實作工廠模式來產生，
 * 讓程式碼複雜度大幅減低且容易修改。
 *
 * 有和 request 相依賴!!!。 所以 angular 如果對goods 的屬性有修正這邊也要跟著改
 *
 * 商品新增的動作有:
 * 1. 產生對應的進貨訂單 ( 產生進貨訂單會連動觸發訂單的listener，進而產生操作記錄(Ope)實體
 * 2. 產生對應的Meta更動記錄( 嚴格來講每個實體都要有記錄，不過時間有限目前只做商品, 訂單的紀錄即可)
 */
class GoodsSubscriber implements EventSubscriber
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

  protected $container;

  public function __construct(ContainerInterface $container)
  {
    $this->container = $container;
  }

  public function getSubscribedEvents()
  {
    return array('postPersist');
  }

  /**
   * 商品新增觸發動作。
   *
   * 商品新增後，會連動新增一筆關連的進貨訂單，若為寄賣訂單則該訂單會關連一個客戶實體，
   * 而進貨訂單成立後系統會觸發 OrderSubscriber 去新增操作記錄。
   */
  public function postPersist(LifecycleEventArgs $args)
  {
    /**
     * 商品實體
     * @var object
     */
    $goods = $args->getEntity();

    // 若不是 GoodsPassport 的實體就不要動作了
    if (!($goods instanceof GoodsPassport)) {   
    	return;
    }

    /**
     * 取得目前的 request 實體，這邊千萬不能用 Request::createFromGlobals，
     * 因為這樣做會使得在 tmp 的上傳檔案被消除，而新的 request 根據 path 去問檔案會問不到因此而報錯
     * 
     * @var object
     */
    $request = $this->container->get('request');

    /**
     * 訂單工廠
     * @var object
     */
    $OrderFactory = $this->container->get('order.factory');

    /**
     * 提供給工廠的參數陣列
     * @var array
     */
    $settings = array();

    /**
     * 取得Doctrine
     * @var object
     */
    $dc = $this->container->get('doctrine');

    /**
     * Symfony 的屬性套件，透過它可以用物件方式讀寫陣列
     * @var object
     */
    $accessor = PropertyAccess::createPropertyAccessor();

    // 若是調貨(進)則不進行以下訂單動作
    if ($goods->getStatus()->getName() === self::GS_MOVING) {
      return;
    }

    // 若是取消訂單動作，相關商品變更會在 ApiBundle/OrdersController.php 完成，
    // 這邊不做任何處理
    if ($request->request->get('cancel')) {
      return;
    }

    // 如果是刷入刷出活動，不做任何動作
    if ($request->request->get('activity_in_out')) {
      return;
    }

    if (!$goods->getInType()) {
      // 新增一般進貨訂單及操作動作記錄

      $accessor->setValue($settings, '[setGoodsPassport]', $goods);
      $accessor->setValue($settings, '[setStatus]', $dc->getRepository('WoojinOrderBundle:OrdersStatus')->find(self::OS_COMPLETE));
      $accessor->setValue($settings, '[setKind]', $dc->getRepository('WoojinOrderBundle:OrdersKind')->find(self::OK_IN));
      $accessor->setValue($settings, '[setPayType]', $dc->getRepository('WoojinOrderBundle:PayType')->find(self::PT_CASH));
      $accessor->setValue($settings, '[setRequired]', $goods->getCost());
      $accessor->setValue($settings, '[setPaid]', $goods->getCost());

      $OrderFactory->create($settings);

    } else {
      // 新增寄賣訂單及操作動作記錄
      $accessor->setValue($settings, '[setGoodsPassport]', $goods);
      $accessor->setValue($settings, '[setStatus]', $dc->getRepository('WoojinOrderBundle:OrdersStatus')->find(self::OS_COMPLETE));
      $accessor->setValue($settings, '[setKind]', $dc->getRepository('WoojinOrderBundle:OrdersKind')->find(self::OK_CONSIGN_IN));
      $accessor->setValue($settings, '[setPayType]', $dc->getRepository('WoojinOrderBundle:PayType')->find(self::PT_CASH));
      $accessor->setValue($settings, '[setCustom]', $goods->getConsigner());
      $accessor->setValue($settings, '[setRequired]', $goods->getCost());
      $accessor->setValue($settings, '[setPaid]', $goods->getCost());

      $orderConsignIn = $OrderFactory->create($settings);

      $settings = array();

      $accessor->setValue($settings, '[setGoodsPassport]', $goods);
      $accessor->setValue($settings, '[setStatus]', $dc->getRepository('WoojinOrderBundle:OrdersStatus')->find(self::OS_HANDLING));
      $accessor->setValue($settings, '[setKind]', $dc->getRepository('WoojinOrderBundle:OrdersKind')->find(self::OK_FEEDBACK));
      $accessor->setValue($settings, '[setPayType]', $dc->getRepository('WoojinOrderBundle:PayType')->find(self::PT_CASH));
      $accessor->setValue($settings, '[setCustom]', $goods->getConsigner());
      $accessor->setValue($settings, '[setRequired]', $goods->getFeedback());
      $accessor->setValue($settings, '[setPaid]', 0);
      $accessor->setValue($settings, '[setParent]', $orderConsignIn);

      $orderFeedBack = $OrderFactory->create($settings);

      $settings = array();

      $accessor->setValue($settings, '[setParent]', $orderFeedBack);
      $OrderFactory->update($settings, $orderConsignIn);
    }
  }
}