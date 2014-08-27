<?php

namespace Woojin\GoodsBundle;

use Symfony\Component\DependencyInjection\ContainerInterface;

class GoodsExporter
{
	const IN_TYPE_NORMAL_DES = '一般';
	const IN_TYPE_CONSIGN_DES = '寄賣';

	protected $container;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;

        $this->phpExcelObj = $this->container->get('phpexcel')->createPHPExcelObject();
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
            ->setCellValue('M1', '市價')
            ->setCellValue('N1', '優惠價*') // 真實顯示價格為此
            ->setCellValue('O1', '備註')
            ->setCellValue('P1', '允許折扣')// {0: 否,1: 是}
            ->setCellValue('Q1', '允許網路販售')// {0: 否,1: 是}
            ->setCellValue('R1', '進貨類型') // {0: 否,1: 是}
            ->setCellValue('S1', '對應圖片') // 請將對應的圖片丟到 /img/yy-mm-dd/ 裡
            ->setCellValue('T1', '狀態')
            ->setCellValue('U1', '產編')
        ;

        return $this;
    }

    protected function setCellViaIterateGoods($goodsGroup)
    {
    	// 迭代商品陣列，逐行->逐格填入對應資訊
        foreach ($goodsGroup as $key => $eachOne) {

            $this->phpExcelObj
            	->setActiveSheetIndex(0)
                ->setCellValue('A' . ($key + 2), substr($eachOne->getSn(), 0, 1))
                ->setCellValue('B' . ($key + 2), (is_object($purchaseAt = $eachOne->getPurchaseAt())) ? $purchaseAt->format('Y-m-d') : '')
                ->setCellValue('C' . ($key + 2), (is_object($expirateAt = $eachOne->getExpirateAt())) ? $expirateAt->format('Y-m-d') : '')
                ->setCellValue('D' . ($key + 2), (is_object($supplier = $eachOne->getSupplier())) ? $supplier->getName() : '')
                ->setCellValue('E' . ($key + 2), (is_object($brand = $eachOne->getBrand())) ? $brand->getName() : '')
                ->setCellValue('F' . ($key + 2), $eachOne->getOrgSn()) // 廠商型號
                ->setCellValue('G' . ($key + 2), $eachOne->getName()) 
                ->setCellValue('H' . ($key + 2), (is_object($pattern = $eachOne->getPattern())) ? $pattern->getName() : '')
                ->setCellValue('I' . ($key + 2), (is_object($color = $eachOne->getColor())) ? $color->getName() : '')
                ->setCellValue('J' . ($key + 2), (is_object($level = $eachOne->getLevel())) ? $level->getName() : '')
                ->setCellValue('K' . ($key + 2), $eachOne->getDpo()) // 系統內部編號
                ->setCellValue('L' . ($key + 2), $eachOne->getCost())
                ->setCellValue('M' . ($key + 2), $eachOne->getFakePrice()) // 市場價
                ->setCellValue('N' . ($key + 2), $eachOne->getPrice()) // 真實顯示價格為此
                ->setCellValue('O' . ($key + 2), $eachOne->getMemo())
                ->setCellValue('P' . ($key + 2), ($eachOne->getAllowDiscount()) ? '是' : '否')// {0: 否,1: 是}
                ->setCellValue('Q' . ($key + 2), ($eachOne->getIsWeb()) ? '是' : '否' )// {0: 否,1: 是}
                ->setCellValue('R' . ($key + 2), $this->getInTypeCellValue($eachOne)) // {0: 否,1: 是}
                ->setCellValue('S' . ($key + 2), $eachOne->getImgpath()) // 請將對應的圖片丟到 /img/yy-mm-dd/ 裡
                ->setCellValue('T' . ($key + 2), $eachOne->getStatus()->getName())
                ->setCellValue('U' . ($key + 2), $eachOne->getSn())
            ;
        }
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
        $response->headers->set('Content-Disposition', 'attachment;filename=goods_export.xlsx');
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