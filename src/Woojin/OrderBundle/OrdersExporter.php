<?php

namespace Woojin\OrderBundle;

use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Security\Core\SecurityContext;

class OrdersExporter
{
    const TEX_RATE = 1.05;

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

    public function __construct(ContainerInterface $container, SecurityContext $context)
    {
        $this->container = $container;

        $this->context = $context;

        $this->phpExcelObj = $this->container->get('phpexcel')->createPHPExcelObject();
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
                ->setFirstRow()
                ->setCellViaIterateOrders($ordersGroup)
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
     * 設置第一行欄位名稱
     */
    protected function setFirstRow()
    {
        // 設置各欄位名稱
        $this
            ->phpExcelObj
            ->setActiveSheetIndex(0)
            ->setCellValue('A1', '原價')
            ->setCellValue('B1', '優惠價')
            ->setCellValue('C1', '折扣')
            ->setCellValue('D1', '現金')
            ->setCellValue('E1', '刷卡')
            ->setCellValue('F1', '實付總計')
            ->setCellValue('G1', '成本')
            ->setCellValue('H1', '含稅成本')
            ->setCellValue('I1', '產編')
            ->setCellValue('J1', 'SKU')
            ->setCellValue('K1', '活動')
            ->setCellValue('L1', '折扣')
            ->setCellValue('M1', '發票')
            ->setCellValue('N1', '建立日期')
        ;

        return $this;
    }

    protected function setCellViaIterateOrders($ordersGroup)
    {
        // 迭代OrdersGroup陣列，逐行->逐格填入對應資訊
        // 其中還會再對關連 Opes 進行迭代，以取得
        // 1. 刷卡
        // 2. 現金
        // 兩個資訊
        foreach ($ordersGroup as $key => $orders) {
            /**
             * 欄位對應陣列
             * 
             * @var array
             */
            $cellMap = $this->getCellMap($orders);

            // 設置該行所有cell值
            // $key + 1 (phpExcel 從 1 開始算) + 1( 第一行欄位介紹跳過 )
            $this->setEachRow($cellMap, $key + 2);
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
         * 活動名稱
         * 
         * @var string
         */
        $activityName = $orders->getGoodsPassport()->getActivity()->getName(); 

        /**
         * 活動優惠方式
         * 
         * @var string
         */
        $activityContent = $orders->getGoodsPassport()->getActivity()->getActivityGiffDes();

        return array(
            'A' => $price,
            'B' => $discountPrice,
            'C' => $giff,
            'D' => $cashPaid,
            'E' => $cardPaid,
            'F' => $paid,
            'G' => $cost,
            'H' => $costWithTex,
            'I' => $sn,
            'J' => $sku,
            'K' => $activityName,
            'L' => $activityContent,
            'M' => $invoiceSn,
            'N' => $createAt
        );
    }

    /**
     * setEachRow
     * 
     * @param [array] $cellMap 
     * @param [integer] $rowNum [行]
     */
    protected function setEachRow($cellMap, $rowNum)
    {        
        foreach ($cellMap as $key => $val) {
            $this->setEachCell($key, $rowNum, $val);
        }

        return $this;
    }

    /**
     * setEachCell
     * 
     * @param [string] $key    [欄]
     * @param [integer] $rowNum [行]
     * @param [type] $val
     */
    protected function setEachCell($key, $rowNum, $val)
    {
        $this->phpExcelObj->setActiveSheetIndex(0)->setCellValue($key . $rowNum, $val);

        return $this;
    }

    protected function setActiveSheet()
    {
        $this->phpExcelObj->getActiveSheet()->setTitle('報表');
        
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
        $response->headers->set('Content-Disposition', 'attachment;filename=orders_export.xlsx');
        $response->headers->set('Pragma', 'public');
        $response->headers->set('Cache-Control', 'maxage=1');

        return $response;
    }
}