<?php

namespace Woojin\OrderBundle;

use Woojin\OrderBundle\Entity\Orders;
use Woojin\OrderBundle\Entity\Ope;

use Symfony\Component\Security\Core\SecurityContext;
use Doctrine\Common\Persistence\ManagerRegistry;

class OrderFactory implements \Woojin\BackendBundle\EntityFactory
{
	protected $registry;
	protected $context;

	public function __construct(ManagerRegistry $registry, SecurityContext $context)
	{
		$this->registry = $registry;
		$this->context = $context;
	}

	/**
	 * 訂單製造
	 * @param  [array] $settings [訂單屬性對應陣列, 完整格式為
	 * array(
	 *   'setGoods' => 關聯商品實體,
	 *   'setStatus' => 狀態實體, 
	 *   'setKind' => 種類實體,
	 *   'setPayType' => 付費方式實體,
	 *   'setCustom' => 客戶實體,
	 *   // 'invoice' => 發票的實體(!!! 由系統自動分配，不會透過工廠作業)
	 *   'setRequired' => 訂單應付金額, 
	 *   'setPaid' => 實收金額,
	 *   'setMemo' => 備註,
	 *   'setRelatedId' => 關聯的訂單
	 * )
	 * @return [object] 生成的訂單
	 */
	public function create($settings)
	{
		$order = new Orders;
		
		return $this->save($settings, $order);
	}

	/**
	 * 訂單修改
	 * @param [array] $settings [訂單屬性對應陣列, 完整格式為
	 * array(
	 *   'setGoods' => 關聯商品實體,
	 *   'setStatus' => 狀態實體, 
	 *   'setKind' => 種類實體,
	 *   'setPayType' => 付費方式實體,
	 *   'setCustom' => 客戶實體,
	 *   // 'invoice' => 發票的實體(!!! 由系統自動分配，不會透過工廠作業)
	 *   'setRequired' => 訂單應付金額, 
	 *   'setPaid' => 實收金額,
	 *   'setMemo' => 備註,
	 *   'setRelatedId' => 關聯的訂單
	 * )
	 * @param [object] 要修改的訂單實體
	 * @return [object] 生成的訂單
	 */
	public function update($settings, $order)
	{
		return $this->save($settings, $order);
	}

	public function copy($orders, $amount){}

	protected function save($order)
	{
		$em = $this->registry->getManager();

		// 使用交易機制以防萬一
		$em->getConnection()->beginTransaction();

		try {
			// 根據傳入的設定進行屬性設置
			$this->iterateSetting($settings, &$order);

			// 將結果保存，迴圈結束後再一次執行
			$em->persist($order);

			$em->flush();

			$em->getConnection()->commit();
		} catch (\Exception $e) {
			$em->getConnection()->rollback();
			
			throw $e;
		}    

		return $order;
	}

	protected function iterateSetting($settings, &$order)
	{
		// 根據傳入的設定進行屬性設置
		foreach ($settings as $key => $val) {
			$order->$key($val);
		}

		return $this;
	}
}
