<?php

namespace Woojin\GoodsBundle;

use Woojin\GoodsBundle\Entity\GoodsPassport;
use Symfony\Component\Security\Core\SecurityContext;
use Symfony\Component\PropertyAccess\PropertyAccess;
use Doctrine\Common\Persistence\ManagerRegistry;

class GoodsFactory implements \Woojin\BackendBundle\EntityFactory
{
    protected $registry;
    protected $context;

    public function __construct(ManagerRegistry $registry, SecurityContext $context)
    {
        $this->registry = $registry;
        $this->context = $context;
    }

    /**
     * 商品製造
     *
     * @param [array] $setting [商品實體對應陣列，格式如下
     * array(
     *  'setName' => 商品名稱,
     *  'setPrice' => 市場價格,
     *  'setFakePrice' => 優惠價格,
     *  'setCost' => 成本,
     *  'setOrgSn' => SKU原廠型號,
     *  'setDpo' => 系統內部編號,
     *  'setBrandSn' => 品牌型號,
     *  'setMemo' => 備註,
     *  'setPurchaseAt' => 進貨時間,
     *  'setExpirateAt' => 過期時間,
     *  'setAllowDiscount' => 允許打折,
     *  'setImgpath' => 圖片路徑,
     *  'setBrand' => 品牌實體,
     *  'setGoodsLevel' => 產品新舊程度,
     *  'setPattern' => 款式,
     *  'setColor' => 顏色,
     *  'setGoodsMT' => 材質,
     *  'setGoodsSource' => 來源,
     *  'setSupplier' => 供貨商,
     *  'amount' => 要產生多少個這種格式的商品(default = 1)
     * )]
     * @return array(object) 產生出來的商品實體陣列
     */
    public function create($settings)
    {
        $em = $this->registry->getManager();

        /**
         * 商品實體陣列
         * 
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

        // 使用交易機制
        $em->getConnection()->beginTransaction();

        try {
            $goodsCollection = $this->setDefualtName($settings)->genGoodsVialoopWithAmount($amount, $settings, $em);

            $em->flush();

            // commit
            $em->getConnection()->commit();
        } catch (\Exception $e) {
            $em->getConnection()->rollback();

            throw $e;
        }    

        return $goodsCollection;
    }

    /**
     * 商品修改
     *
     * @param [array] $setting [商品實體對應陣列，格式如下
     * array(
     *  'setName' => 商品名稱,
     *  'setPrice' => 市場價格,
     *  'setFakePrice' => 優惠價格,
     *  'setCost' => 成本,
     *  'setOrgSn' => SKU原廠型號,
     *  'setDpo' => 系統內部編號,
     *  'setBrandSn' => 品牌型號,
     *  'setMemo' => 備註,
     *  'setPurchaseAt' => 進貨時間,
     *  'setExpirateAt' => 過期時間,
     *  'setAllowDiscount' => 允許打折,
     *  'setImgpath' => 圖片路徑,
     *  'setBrand' => 品牌實體,
     *  'setGoodsLevel' => 產品新舊程度,
     *  'setPattern' => 款式,
     *  'setColor' => 顏色,
     *  'setGoodsMT' => 材質,
     *  'setGoodsSource' => 來源,
     *  'setSupplier' => 供貨商,
     *  'setIsWeb' => '是否在網站上販售'
     * )]
     * 
     * @return array(object) 產生出來的商品實體陣列
     */
    public function update($settings, $goods)
    {
        $em = $this->registry->getManager(); 

        // 使用交易機制
        $em->getConnection()->beginTransaction();

        $this->setDefualtName($settings);

        try {
            // 根據傳入的設定進行屬性設置
            foreach ($settings as $key => $val) {
                $goods->$key($val);
            }

            // 將結果保存，迭代結束後一次執行
            $em->persist($goods);
            $em->flush();

            // commit
            $em->getConnection()->commit();
        } catch (\Exception $e) {
          $em->getConnection()->rollback();

          throw $e;
        }    

        return $goods;
    }

    public function copy($goods, $amount){}

    /**
     * 批次上傳建立商品函式
     * 
     * @param  [array] $settings [商品設定參數陣列]
     * @param  [object] $em [entitiy manager]
     * @return [array] [回傳的商品實體陣列]
     */
    public function lazyCreate($settings, &$em)
    {
        /**
         * Symfony 的屬性套件，透過它可以用物件方式讀寫陣列
         * 
         * @var object
         */
        $accessor = PropertyAccess::createPropertyAccessor();

        /**
         * 製造實體個數
         * 
         * @var integer
         */
        $amount = ($num = $accessor->getValue($settings, '[amount]')) ? $num : 1;

        // 先除去數量元素，以防接下來的迴圈處理發生錯誤
        unset($settings['amount']);

        return $this->genGoodsVialoopWithAmount($amount, $settings, $em)
        ;
    }

    /**
     * 根據數量執行等次數的迴圈，每次產生一個新的商品實體，
     * 所以此迴圈正常來說會產生 $amount 個新的商品，
     * 並且分別綁定上不同的訂單和操作記錄
     * 
     * @param  [integer] $amount [需要產生的數量]
     * @param  [array] $settings [各屬性的設定值]
     * @param  [object] $em [entity manager]
     * @return [array] $goodsCollection
     */
    protected function genGoodsVialoopWithAmount($amount, $settings, &$em)
    {
        /**
         * 商品實體陣列
         * 
         * @var array(object)
         */
        $goodsCollection = array();

        for ($i = 0; $i < $amount; $i ++) {
            $this
                ->pushInCollection($goodsCollection)
                ->setBySettings($settings, $goodsCollection, $i)
            ;

            // 將結果保存，迴圈結束後再一次執行
            $em->persist($goodsCollection[$i]);
        }

        return $goodsCollection;
    }

    /**
     * 每次迴圈開始就new 一個商品實體並放入商品實體陣列
     * 
     * @param  [array] $goodsCollection [商品物件的容器陣列]
     */
    protected function pushInCollection(&$goodsCollection)
    {
        array_push($goodsCollection, new GoodsPassport);

        return $this;
    }

    /**
     * 根據傳入的設定進行屬性設置
     * 
     * @param [array] $settings [各屬性的設定值]
     * @param [array] $goodsCollection [商品物件的容器陣列]
     * @param [integer] $i [陣列索引]
     */
    protected function setBySettings($settings, &$goodsCollection, $index)
    {
        foreach ($settings as $key => $val) {
            $goodsCollection[$index]->$key($val);
        }

        return $this;
    }

    /**
     * 如果名稱為空或是未設，自動用 sku 
     * 
     * @param [array] $settings
     */
    protected function setDefualtName(&$settings)
    {
        if (!array_key_exists('setName', $settings) || empty($settings['setName'])) {
            $settings['setName'] = $settings['setOrgSn'];
        }

        return $this;
    }
}
