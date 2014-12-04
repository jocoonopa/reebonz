<?php

namespace Woojin\OrderBundle;

use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Security\Core\SecurityContext;

class OrdersExporter
{
    const TEX_RATE = 1.05;
    const OK_TYPE_OUT = 2;
    const OS_CANCEL = 3;
    const PHPEXCEL_DEFAULT_PAGE = 0;

    /**
     * Service Container
     * 
     * @var [\Symfony\Component\DependencyInjection\ContainerInterface]
     */
    protected $container;

    /**
     * Context
     * 
     * @var [\Symfony\Component\Security\Core\SecurityContext]
     */
    protected $context;

    /** 
     * phpExcelObject
     * 
     * @var [object]
     */
    protected $phpExcelObj;

    /**
     * exGetter
     * 
     * @var [serviceObject]
     */
    protected $exGetter;

    public function __construct(ContainerInterface $container, SecurityContext $context)
    {
        $this->container = $container;

        $this->context = $context;

        $this->phpExcelObj = $this->container->get('phpexcel')->createPHPExcelObject();

        $this->exGetter = $this->container->get('exchangeRate.getter');
    }

    /**
     * 根據傳入的商品陣列產生報表
     * 
     * @param [array{\Woojin\OrderBundle\Entity}]
     * @return [object] [客戶端回應物件]
     */
    public function run($ordersGroup)
    {
        return $this
            ->setMeta()
            ->setTabViaIterateStores($ordersGroup)
            ->setActiveSheet()
            ->getResponse()
        ;
    }

    /**
     * 設置報表 meta 資訊
     */
    protected function setMeta()
    {
        /**
         * 取得目前使用者
         * 
         * @var object
         */
        $user = $this->context->getToken()->getUser();

        // 設置excel 的 一些meta資訊
        $this->phpExcelObj
            ->getProperties()
            ->setCreator('ReebonzSystem')
            ->setLastModifiedBy($user->getUsername())
            ->setTitle('訂單報表')
            ->setSubject('訂單報表')
            ->setDescription('根據傳入條件匯出excel訂單報表')
            ->setKeywords('Reebonz Export')
            ->setCategory('Orders')
        ;

        return $this;
    }

    /**
     * 依照 tabsArray 設置每個 tab
     * 
     * @param [array{\Woojin\OrderBundle\Entity}]
     */
    protected function setTabViaIterateStores($ordersGroup)
    {   
        /**
         * 部門分類的訂單實體陣列
         * 
         * @var [array]
         */
        $ordersesGroupWithStore = $this->getOrdersesGroupWithStore($ordersGroup);

        return $this->setEachTab($ordersesGroupWithStore);
    }

    /**
     * 取得按照部門分類的訂單實體陣列
     * 
     * @param  [array] $ordersGroup
     * @return [array] $ordersesGroupWithStore
     */
    protected function getOrdersesGroupWithStore($ordersGroup)
    {
        /**
         * 按照部門分類的訂單實體陣列
         * 
         * @var array
         */
        $ordersesGroupWithStore = array();

        // 迭代 ordersGroup 進行陣列重組
        foreach ($ordersGroup as $orders) {
            /**
             * 店碼
             * 
             * @var [string]
             */
            $storeName = $orders->getGoodsPassport()->getStore()->getName();

            if (!array_key_exists($storeName, $ordersesGroupWithStore)) {
                $ordersesGroupWithStore[$storeName] = array();
            }

            array_push($ordersesGroupWithStore[$storeName], $orders);
        }

        return $ordersesGroupWithStore;
    }

    /**
     * 迭代 $ordersesGroupWithStore 製作每個分頁
     * 
     * @var [array] $ordersesGroupWithStore
     */
    protected function setEachTab($ordersesGroupWithStore)
    {
        /**
         * sheet index
         * 
         * @var integer
         */
        $page = 0;

        foreach ($ordersesGroupWithStore as $storeName => $orderses) {
            /**
             * 最後一行的行數
             * 
             * @var [integer]
             */
            $finalRow = $this->getLastRowNum($orderses);

            /**
             * 售出商品數量
             * 
             * @var integer
             */
            $amount = 0;

            $this
                ->createTab($page)
                ->setTabTitle($storeName)
                ->setFirstRow($page)
                ->setCellViaIterateOrders($orderses, $page, $amount)  
                ->setTotalSoldAmountAtLastRow($finalRow, $amount, $page) 
            ;

            $page ++;
        }

        return $this;
    }

    /**
     * 最後一行的行數: 陣列大小 +1 (第一行是標題) + 1 (phpExcel 從 1 開始算) + 1 (下一行)
     * 
     * @var [integer]
     */
    protected function getLastRowNum($orderses)
    {
        return count($orderses) + 1 + 1 + 1;
    }

    /**
     * 最後一欄設置統計售出商品數量
     *
     * @param  [integer] $finalRow
     * @param  [integer] $amount
     * @param  [integer] $page
     */
    protected function setTotalSoldAmountAtLastRow($finalRow, $amount, $page = 0)
    {
        $this->phpExcelObj
            ->setActiveSheetIndex($page)
            ->setCellValue('A' . $finalRow, '總售出商品件數:      ' . $amount)
            ->setCellValue('C' . $finalRow, $amount)
            ->mergeCells('A' . $finalRow . ':C' . $finalRow)
        ;

        return $this;
    }

    /**
     * 建立新分頁
     * 
     * @param  [integer] $page
     */
    protected function createTab($page = 0)
    {
        if ($page === self::PHPEXCEL_DEFAULT_PAGE) {            
            return $this;
        }

        $this->phpExcelObj->createSheet($page);

        $this->phpExcelObj->setActiveSheetIndex($page);

        return $this;
    }

    /**
     * 分頁的 title
     * 
     * @param [string] $title
     */
    protected function setTabTitle($title)
    {
        $this->phpExcelObj->getActiveSheet()->setTitle($title);

        return $this;
    }

    /**
     * 設置第一行欄位名稱
     *
     * @param [integer] $page [分頁]
     */
    protected function setFirstRow($page = 0)
    {
        $this
            ->phpExcelObj
            ->setActiveSheetIndex($page)
            ->setCellValue('A1', '產編')
            ->setCellValue('B1', 'SKU')
            ->setCellValue('C1', '廠商')
            ->setCellValue('D1', '品牌')
            ->setCellValue('E1', '品名')
            ->setCellValue('F1', '成本')
            ->setCellValue('G1', '成本(新加坡幣)')
            ->setCellValue('H1', 'Margin')
            ->setCellValue('I1', '原價')
            ->setCellValue('J1', '折扣金額')
            ->setCellValue('K1', '實付')
            ->setCellValue('L1', '實付(新加坡幣)')
            ->setCellValue('M1', '現金')
			->setCellValue('N1', '發票號碼')
			->setCellValue('O1', '備註')
			->setCellValue('P1', '信用卡號')  
            ->setCellValue('Q1', '時間')   
            ->setCellValue('R1', '狀態')
		;

        return $this;
    }

    /**
     *  迭代OrdersGroup陣列，逐行->逐格填入對應資訊
     *  其中還會再對關連 Opes 進行迭代，以取得
     *  1. 刷卡
     *  2. 現金
     *  
     * @param [array{\Woojin\OrderBundle\Entity\Orders}] $ordersGroup
     * @param [integer] $page
     * @param [integer] $amount
     */
    protected function setCellViaIterateOrders($ordersGroup, $page = 0, &$amount)
    {        
        foreach ($ordersGroup as $key => $orders) {
            /**
             * 欄位對應陣列
             * 
             * @var array
             */
            $cellMap = $this->getCellMap($orders);

            $this->patchAmount($amount, $orders);

            // 設置該行所有cell值
            // $key + 1 (phpExcel 從 1 開始算) + 1( 第一行欄位介紹跳過 )
            $this->setEachRow($cellMap, $rowNum = ($key + 2), $page);

            // 如果是已經退貨的單，已顏色標註
            if ($this->isTurnBack($cellMap)) {
                $this->setColumnColor($rowNum);
            }
        }

        return $this;
    }

    /**
     * 檢查是否有退貨
     * 
     * @param  array  $cellMap
     * @return boolean        
     */
    protected function isTurnBack($cellMap)
    {
        return array_key_exists('R', $cellMap) && ($cellMap['R'] == '已退貨');
    }

    /**
     * 設置該列顏色
     * 
     * @param integer $rowNum
     */
    protected function setColumnColor($rowNum)
    {
        return $this->phpExcelObj->getActiveSheet()->getStyle('A' . $rowNum .':R' . $rowNum)->getFill()->applyFromArray(
            array(
                'type'       => \PHPExcel_Style_Fill::FILL_SOLID,
                'startcolor' => array('rgb' => 'E9E9E9'),
                'endcolor'   => array('rgb' => 'E9E9E9')
            )
        );
    }

    /**
     * 統計售出商品數量 + 1
     * 
     * @param  [integer] $amount [description]
     * @param  [\Woojin\OrderBundle\Entity\Orders] $orders
     */
    protected function patchAmount(&$amount, $orders)
    {
        if ($orders->getKind()->getType() === self::OK_TYPE_OUT && $orders->getStatus()->getId() !== self::OS_CANCEL) {
            $amount ++;
        }

        return $this;
    }

    /**
     * 取得欄位對應陣列
     *
     * @param \Woojin\OrderBundle\Entity $orders
     * @return array
     */
    protected function getCellMap($orders)
    {
        /**
         * 現金已付
         * 
         * @var integer
         */
        $cashPaid = $orders->getCashPaid();

        /**
         * 刷卡已付
         * 
         * @var integer
         */
        $cardPaid = $orders->getCardPaid();

        /**
         * 總已付 (刷卡 + 現金)
         * 
         * @var integer
         */
        $paid = $orders->getPaid();

        /**
         * 發票編號
         * 
         * @var string
         */
        $invoiceSn = $orders->getInvoice()->getSn();

        /**
         * 訂單建立時間
         * 
         * @var string/Date
         */
        $createAt = $orders->getCreateAt()->format('Y-m-d H:i:s');

        /**
         * 商品原價
         * 
         * @var integer
         */
        $price = $orders->getGoodsPassport()->getPrice();

        /**
         * 活動優惠價
         * 
         * @var integer
         */
        $discountPrice = $orders->getRequired();

        /**
         * 優惠(原售價 - 訂單售價)
         * 
         * @var integer
         */
        $giff = $orders->getGoodsPassport()->getPrice() - $orders->getRequired();

        /**
         * 成本
         * 
         * @var integer
         */
        $cost = $orders->getGoodsPassport()->getCost();

        /**
         * 含稅成本
         * 
         * @var integer
         */
        $costWithTex = $cost / self::TEX_RATE;

        /**
         * 產編
         * 
         * @var string
         */
        $sn = $orders->getGoodsPassport()->getSn();

        /**
         * SKU
         * 
         * @var string
         */
        $sku = $orders->getGoodsPassport()->getOrgSn();

        /**
         * 進貨時間
         *
         * @var string
         */
        $purchaseAt = $orders->getGoodsPassport()->getPurchaseAt()->format('Y-m');

        /**
         * 操作記錄實體陣列
         * 
         * @var [\Woojin\OrderBundle\Entity\Ope]
         */
        $opes = $orders->getOpes();

        /**
         * 信用卡號全部合起來的字串
         */
        $cardSnOfAll = false;

        /**
         * 品牌
         * 
         * @var \Woojin\GoodsBundle\Entity\Brand 
         */
        $brand = $orders->getGoodsPassport()->getBrand();

        /**
         * 支付現金總額
         */
        $cashPaid = 0;

        foreach ($opes as $ope) {
            $cardSnOfAll .= $ope->addCardSnInList();

            $cashPaid += $ope->addCashPaid();
        }

        return array(
            'A' => $sn,
            'B' => $sku,
            'C' => $orders->getGoodsPassport()->getSupplier()->getName(),
            'D' => ($brand) ? $brand->getName() : '',
            'E' => $orders->getGoodsPassport()->getName(),
            'F' => $cost,
            'G' => ($cost / $this->exGetter->getExchangeRateByDate($purchaseAt)),
            'H' => ($orders->getPaid() === 0) ? 0 : ($orders->getPaid() - $orders->getGoodsPassport()->getCost())/$orders->getPaid(),
            'I' => $orders->getGoodsPassport()->getPrice(),
            'J' => $orders->getGoodsPassport()->getPrice() - $orders->getPaid(),
            'K' => $paid,
            'L' => ($paid / $this->exGetter->getExchangeRateByDate($purchaseAt)),
            'M' => $cashPaid,
            'N' => $orders->getInvoice()->getSn(), 
            'O' => $orders->getMemo(),
            'P' => ($cardSnOfAll) ? substr($cardSnOfAll, 0, -1) : '',
            'Q' => $orders->getCreateAt()->format('Y-m-d H:i:s'),
            'R' => (count($orders->getChildrens()) === 0) ? $orders->getStatus()->getName() : '已退貨'
        );
    }

    /**
     * setEachRow
     * 
     * @param [array] $cellMap 
     * @param [integer] $rowNum [行]
     * @param [integer] $page
     */
    protected function setEachRow($cellMap, $rowNum, $page = 0)
    {        
        foreach ($cellMap as $key => $val) {
            $this->setEachCell($key, $rowNum, $val, $page);
        }

        return $this;
    }

    /**
     * setEachCell
     * 
     * @param [string] $key    [欄]
     * @param [integer] $rowNum [行]
     * @param [type] $val
     * @param [integer] $page
     */
    protected function setEachCell($key, $rowNum, $val, $page = 0)
    {
        $this->phpExcelObj->setActiveSheetIndex($page)->setCellValue($key . $rowNum, $val);

        return $this;
    }

    protected function setActiveSheet()
    {        
        // Set active sheet index to the first sheet, so Excel opens this as the first sheet
        $this->phpExcelObj->setActiveSheetIndex(0);

        return $this;
    }

    protected function getResponse()
    {
        // create the writer
        $writer = $this->container->get('phpexcel')->createWriter($this->phpExcelObj, 'Excel2007');
        
        // create the response
        $response = $this->container->get('phpexcel')->createStreamedResponse($writer);
        
        // adding headers
        $response->headers->set('Content-Type', 'text/vnd.ms-excel; charset=utf-8');
        $response->headers->set('Content-Disposition', 'attachment;filename=orders_export_' . date("YmdHis") . '.xlsx');
        $response->headers->set('Pragma', 'public');
        $response->headers->set('Cache-Control', 'maxage=1');

        return $response;
    }
}
