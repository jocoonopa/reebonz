<?php

namespace Woojin\GoodsBundle;

use Woojin\GoodsBundle\Entity\GoodsPassport;
use Symfony\Component\Security\Core\SecurityContext;

class GoodsFactory implements \Woojin\BackendBundle\EntityFactory
{
	protected $em;
	protected $context;

	public function __construct(\Doctrine\ORM\EntityManager $em, SecurityContext $context)
  {
    $this->em = $em;
    $this->context = $context;
  }

	/**
	 * 商品製造
	 *
	 * @param [array] $setting [商品實體對應陣列，格式如下
	 * array(
	 * 	'setName' => 商品名稱,
	 * 	'setPrice' => 市場價格,
	 * 	'setFakePrice' => 優惠價格,
	 * 	'setCost' => 成本,
	 * 	'setOrgSn' => SKU原廠型號,
	 * 	'setDpo' => 系統內部編號,
	 * 	'setBrandSn' => 品牌型號,
	 * 	'setMemo' => 備註,
	 * 	'setPurchaseAt' => 進貨時間,
	 * 	'setExpirateAt' => 過期時間,
	 * 	'setAllowDiscount' => 允許打折,
	 * 	'setImgpath' => 圖片路徑,
	 * 	'setBrand' => 品牌實體,
	 * 	'setGoodsLevel' => 產品新舊程度,
	 * 	'setPattern' => 款式,
	 * 	'setColor' => 顏色,
	 * 	'setGoodsMT' => 材質,
	 * 	'setGoodsSource' => 來源,
	 * 	'setSupplier' => 供貨商,
	 * 	'amount' => 要產生多少個這種格式的商品(default = 1)
	 * )]
	 * @return array(object) 產生出來的商品實體陣列
	 */
	public function create($settings)
	{
		/**
		 * 商品實體陣列
		 * @var array(object)
		 */
		$goodsCollection = array();

		/**
		 * 製造實體個數
		 * @var integer
		 */
		$amount = (isset($settings['amount'])) ? $settings['amount'] : 1;

		// 先除去數量元素，以防接下來的迴圈處理發生錯誤
		unset($settings['amount']);

		// 使用交易機制以防萬一
		$this->em->getConnection()->beginTransaction();

		try {
			// 根據數量執行等次數的迴圈，每次產生一個新的商品實體，
			// 所以此迴圈正常來說會產生 $amount 個新的商品，
			// 並且分別綁定上不同的訂單和操作記錄
			for ($i = 0; $i < $amount; $i ++) {
				// 每次迴圈開始就new 一個商品實體並放入商品實體陣列
				array_push($goodsCollection, new GoodsPassport);

				// 根據傳入的設定進行屬性設置
				foreach ($settings as $key => $val) {
					$goodsCollection[$i]->$key($val);
				}

				// 將結果保存，迴圈結束後再一次執行
				$this->em->persist($goodsCollection[$i]);
			}

			$this->em->flush();

			// 迭代陣列元素，為每個剛新增的商品實體設置產編和繼承id
			foreach ($goodsCollection as $goods) {
				// 產編根據 店碼+廠商代碼三碼+年末碼+月二碼+日二碼+流水號五碼 組成
				$goods->setSn(
					$this->genSn(
						$goods->getStore()->getSn(), 
						$goods->getId(), 
						$goods->getPurchaseAt(),
						$goods->getSupplier()->getName()
					)
				)
				->setInheritId($goods->getId());

				// 將結果保存，迭代結束後一次執行
				$this->em->persist($goods);
			}

			$this->em->flush();

			$this->em->getConnection()->commit();
		} catch (Exception $e) {
			$this->em->getConnection()->rollback();
			throw $e;
		}    

		return $goodsCollection;
	}

	/**
	 * 商品修改
	 *
	 * @param [array] $setting [商品實體對應陣列，格式如下
	 * array(
	 * 	'setName' => 商品名稱,
	 * 	'setPrice' => 市場價格,
	 * 	'setFakePrice' => 優惠價格,
	 * 	'setCost' => 成本,
	 * 	'setOrgSn' => SKU原廠型號,
	 * 	'setDpo' => 系統內部編號,
	 * 	'setBrandSn' => 品牌型號,
	 * 	'setMemo' => 備註,
	 * 	'setPurchaseAt' => 進貨時間,
	 * 	'setExpirateAt' => 過期時間,
	 * 	'setAllowDiscount' => 允許打折,
	 * 	'setImgpath' => 圖片路徑,
	 * 	'setBrand' => 品牌實體,
	 * 	'setGoodsLevel' => 產品新舊程度,
	 * 	'setPattern' => 款式,
	 * 	'setColor' => 顏色,
	 * 	'setGoodsMT' => 材質,
	 * 	'setGoodsSource' => 來源,
	 * 	'setSupplier' => 供貨商,
	 * 	'setIsWeb' => '是否在網站上販售'
	 * )]
	 * @return array(object) 產生出來的商品實體陣列
	 */
	public function update($settings, $goods)
	{
		/**
		 * 商店代碼
		 * @var string
		 */
		$storeSn = $this->context->getToken()->getUser()->getStore()->getSn();

		// 使用交易機制以防萬一
    $this->em->getConnection()->beginTransaction();

    try {
      // 根據傳入的設定進行屬性設置
      foreach ($settings as $key => $val) {
        $goods->$key($val);
      }

      $goods
      	->setSn( 
    			$this->genSn(
						$storeSn, 
						$goods->getId(), 
						$goods->getCreateAt(),
						$goods->getSupplier()->getName()
					)
				)
      	->setInheritId($goods->getId())
      ;

			// 將結果保存，迭代結束後一次執行
			$this->em->persist($goods);
			$this->em->flush();
      $this->em->getConnection()->commit();
    } catch (Exception $e) {
      $this->em->getConnection()->rollback();
      throw $e;
    }    

    return $goods;
	}

	public function copy($goods, $amount)
	{

	}

	/**
	 * 產編根據 店碼+廠商代碼三碼+年末碼+月二碼+日二碼+流水號五碼 組成
	 * 
	 * @param  [string] $storeSn [店碼]
	 * @param  [integer] $id [流水號]
	 * @param  [datetime] $createAt [建立時間]
	 * @param  [string] $supName [供貨商代碼]
	 * @return [string]        
	 */
	protected function genSn($storeSn, $id, $createAt, $supName)
	{
		return $storeSn . substr($createAt->format('Ymd'), 3) . $supName . str_pad($id, 5, 0, STR_PAD_LEFT);
	}
}
