<?php

namespace Woojin\GoodsBundle;

use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Security\Core\SecurityContext;

class GoodsExporter
{
	const IN_TYPE_NORMAL_DES = '一般';
	const IN_TYPE_CONSIGN_DES = '寄賣';

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
     * @param [array{\Woojin\GoodsBundle\Entity}]
     * @return [object] [客戶端回應物件]
     */
    public function run($goodsGroup)
    {
    	return $this
	    		->setMeta()
	    		->setFirstRow()
	    		->setCellViaIterateGoods($goodsGroup)
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
            ->setTitle('商品報表')
            ->setSubject('商品報表')
            ->setDescription('根據傳入條件匯出excel商品報表')
            ->setKeywords('Reebonz Export')
            ->setCategory('Goods')
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
            ->setCellValue('A1', '部門*')
            ->setCellValue('B1', '進貨日*')
            ->setCellValue('C1', '到期日')
            ->setCellValue('D1', '廠商*')
            ->setCellValue('E1', '品牌*')
            ->setCellValue('F1', 'SKU') // 廠商型號
            ->setCellValue('G1', '商品描述*') // 商品名稱
            ->setCellValue('H1', '款式*')
            ->setCellValue('I1', '花色*')
            ->setCellValue('J1', '商品狀況')
            ->setCellValue('K1', 'DPO#') // 系統內部編號
            ->setCellValue('L1', '單價成本*(含稅)')
            ->setCellValue('M1', '成本(新幣)')
            ->setCellValue('N1', '市價')
            ->setCellValue('O1', '優惠價*') // 真實顯示價格為此
            ->setCellValue('P1', '備註')
            ->setCellValue('Q1', '允許折扣')// {0: 否,1: 是}
            ->setCellValue('R1', '允許網路販售')// {0: 否,1: 是}
            ->setCellValue('S1', '進貨類型') // {0: 否,1: 是}
            ->setCellValue('T1', '對應圖片') // 請將對應的圖片丟到 /img/yy-mm-dd/ 裡
            ->setCellValue('U1', '狀態')
            ->setCellValue('V1', '產編')
            ->setCellValue('W1', '活動')
        ;

        return $this;
    }

    protected function setCellViaIterateGoods($goodsGroup)
    {
    	/**
    	 * ProGetter
    	 * 
    	 * @var [\Woojin\Entity\ProGetter]
    	 */
    	$EntityProGetter = $this->container->get('entity.pro.getter');

    	// 迭代商品陣列，逐行->逐格填入對應資訊
        foreach ($goodsGroup as $key => $eachOne) {

            $purchaseAt = $EntityProGetter->getDate($eachOne->getPurchaseAt());

            $this->phpExcelObj
            	->setActiveSheetIndex(0)
                ->setCellValue('A' . ($key + 2), $EntityProGetter->getName($eachOne->getStore())) // 部門
                ->setCellValue('B' . ($key + 2), $EntityProGetter->getDate($eachOne->getPurchaseAt())) // 進貨時間
                ->setCellValue('C' . ($key + 2), $EntityProGetter->getDate($eachOne->getExpirateAt())) // 到期時間
                ->setCellValue('D' . ($key + 2), $EntityProGetter->getName($eachOne->getSupplier())) // 廠商名稱
                ->setCellValue('E' . ($key + 2), $EntityProGetter->getName($eachOne->getBrand())) // 品牌名稱
                ->setCellValue('F' . ($key + 2), $eachOne->getOrgSn()) // 廠商型號
                ->setCellValue('G' . ($key + 2), $eachOne->getName()) // 商品名稱
                ->setCellValue('H' . ($key + 2), $EntityProGetter->getName($eachOne->getPattern())) // 款式名稱
                ->setCellValue('I' . ($key + 2), $EntityProGetter->getName($eachOne->getColor())) // 顏色名稱
                ->setCellValue('J' . ($key + 2), $EntityProGetter->getName($eachOne->getLevel())) // 商品狀況名稱
                ->setCellValue('K' . ($key + 2), $eachOne->getDpo()) // 系統內部編號
                ->setCellValue('L' . ($key + 2), $eachOne->getCost()) // 成本
                ->setCellValue('M' . ($key + 2), ($eachOne->getCost() / $this->exGetter->getExchangeRateByDate($purchaseAt)))
                ->setCellValue('N' . ($key + 2), $eachOne->getFakePrice()) // 市場價
                ->setCellValue('O' . ($key + 2), $eachOne->getPrice()) // 真實顯示價格為此
                ->setCellValue('P' . ($key + 2), $eachOne->getMemo()) // 備註
                ->setCellValue('Q' . ($key + 2), $EntityProGetter->getTrueFalseDes($eachOne->getAllowDiscount()))// {0: 否,1: 是}
                ->setCellValue('R' . ($key + 2), $EntityProGetter->getTrueFalseDes($eachOne->getIsWeb()))// {0: 否,1: 是}
                ->setCellValue('S' . ($key + 2), $this->getInTypeCellValue($eachOne)) // {0: 否,1: 是}
                ->setCellValue('T' . ($key + 2), $eachOne->getImgpath()) // 請將對應的圖片丟到 /img/yy-mm-dd/ 裡
                ->setCellValue('U' . ($key + 2), $eachOne->getStatus()->getName()) // 商品狀態, ex: 上架
                ->setCellValue('V' . ($key + 2), $eachOne->getSn()) // 產編
                ->setCellValue('W' . ($key + 2), $EntityProGetter->getName($eachOne->getActivity())) // 活動
            ;
        }

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
        $response->headers->set('Content-Disposition', 'attachment;filename=goods_export_' . date("Y-m-d H:i:s") .'.xlsx');
        $response->headers->set('Pragma', 'public');
        $response->headers->set('Cache-Control', 'maxage=1');

        return $response;
    }

    /**
     * 取得 excel 進貨類型那一格的值
     * 
     * @param  \Woojin\GoodsBundle\Entity\GoodsPassport $goods
     * @return string
     */
    protected function getInTypeCellValue($goods)
    {
    	if (!($consigner = $goods->getConsigner())) {
    		return self::IN_TYPE_NORMAL_DES;
    	}

    	return self::IN_TYPE_CONSIGN_DES . $consigner->getEmail() . '[' . $consigner->getName() . $consigner->getSex() . ']';
    }
}