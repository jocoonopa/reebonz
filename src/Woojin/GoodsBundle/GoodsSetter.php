<?php 

namespace Woojin\GoodsBundle;

/**
 * 處理傳入工廠的 $settings 陣列
 */
class GoodsSetter
{
	const NO_IMG = '/img/404.png';
	const NONE_ENTITY = 0;
	const GS_ON_SALE = 1;

	protected $settings = array();

	/**
	 * 取得設定陣列
	 * 
	 * @return [array]
	 */
	public function getSettings()
	{
		return $this->settings;
	}

	/**
     * 設置更新商品設定陣列
     * 
     * @param [object] $accessor
     * @param [array] $settings
     * @param [object] $request
     */
    public function setUpdateSettings($accessor, $request, $em)
    {      
        return $this
	            ->setUpdateSettingsWithValue($accessor, $request)
	            ->setUpdateSettingsWithEntity($accessor, $request, $em)
	        ;
    }

    /**
     * 設置更新商品陣列屬於字串和數字的元素
     * 
     * @param [object] $accessor 
     * @param [array] $settings 
     * @param [object] $request
     */
    protected function setUpdateSettingsWithValue($accessor, $request)
    {
        if ($name = $request->request->get('name')) {
            $accessor->setValue($this->settings, '[setName]', $name);
        }
        
        if ($dpo = $request->request->get('dpo')) {
            $accessor->setValue($this->settings, '[setDpo]', $dpo);
        }
        
        if ($price = $request->request->get('price')) {
            $accessor->setValue($this->settings, '[setPrice]', $price);
        }
       
        if ($fakePrice = $request->request->get('fake_price')) {
            $accessor->setValue($this->settings, '[setFakePrice]', $fakePrice);
        }
        
        if ($cost = $request->request->get('cost')) {
            $accessor->setValue($this->settings, '[setCost]', $cost);
        }
        
        if ($orgSn = $request->request->get('org_sn')) {
            $accessor->setValue($this->settings, '[setOrgSn]', $orgSn);
        }
        
        if ($brandSn = $request->request->get('brand_sn')) {
            $accessor->setValue($this->settings, '[setBrandSn]', $brandSn);
        }
        
        if ($des = $request->request->get('des')) {
            $accessor->setValue($this->settings, '[setDes]', $des);
        }
        
        if ($memo = $request->request->get('memo')) {
            $accessor->setValue($this->settings, '[setMemo]', $memo);
        }
        
        if ($isWeb = $request->request->get('is_web')) {
            $accessor->setValue($this->settings, '[setIsWeb]', $isWeb);
        }
        
        if ($purchaseAt = $request->request->get('purchase_at')) {
            $accessor->setValue($this->settings, '[setPurchaseAt]', new \DateTime($purchaseAt));
        }
        
        if ($expirateAt = $request->request->get('expirate_at')) {
            $accessor->setValue($this->settings, '[setExpirateAt]', new \DateTime($expirateAt));
        }     
        
        if ($allowDiscount = $request->request->get('allow_discount')) {
            $accessor->setValue($this->settings, '[setAllowDiscount]', $allowDiscount);   
        }

        // 若是404表示移除圖片，需要更新，其他的動作都交給ImgController.php，
        // 這邊會這樣處理的原因是，移除圖片本身沒有檔案上傳，所以ImgController.php 不會被呼叫，
        // 連帶的商品的圖片路徑也不會被ImgController.php 修改，因此在商品資訊更新就要自行先處理，
        // 而如果確實有修改圖片且上傳的話，就不需要進行此動作
        if ($request->request->get('imgpath') === '/img/404.png') {
            $accessor->setValue($this->settings, '[setImgpath]', '/img/404.png');
        } 

        return $this;
    }

    /**
     * 設置商品陣列中屬於關聯實體的元素
     * 
     * @param [object] $accessor 
     * @param [array] $settings 
     * @param [object] $request  
     * @param [object] $em       
     */
    protected function setUpdateSettingsWithEntity($accessor, $request, $em)
    {
        if ($brand = $request->request->get('brand')) {
            $accessor->setValue($this->settings, '[setBrand]', $em->find('WoojinGoodsBundle:Brand', $brand));
        }
        
        if ($color = $request->request->get('color')) {
            $accessor->setValue($this->settings, '[setColor]', $em->find('WoojinGoodsBundle:Color', $color));   
        }
        
        if ($pattern = $request->request->get('pattern')) {
            $accessor->setValue($this->settings, '[setPattern]', $em->find('WoojinGoodsBundle:Pattern', $pattern));   
        }
        
        if ($level = $request->request->get('level')) {
            $accessor->setValue($this->settings, '[setLevel]', $em->find('WoojinGoodsBundle:GoodsLevel', $level));   
        }
        
        if ($source = $request->request->get('source')) {
            $accessor->setValue($this->settings, '[setSource]', $em->find('WoojinGoodsBundle:GoodsSource', $source));   
        }
        
        if ($mt = $request->request->get('mt')) {
            $accessor->setValue($this->settings, '[setMt]', $em->find('WoojinGoodsBundle:GoodsMT', $mt));   
        }
        
        if ($supplier = $request->request->get('supplier')) {
            $accessor->setValue($this->settings, '[setSupplier]', $em->find('WoojinGoodsBundle:Supplier', $supplier));
        }
        
        if ($status = $request->request->get('status')) {
            $accessor->setValue($this->settings, '[setStatus]', $em->find('WoojinGoodsBundle:GoodsStatus', $status));   
        }

        if ($store = $request->request->get('store')) {
            $accessor->setValue($this->settings, '[setStore]', $em->find('WoojinStoreBundle:Store', $store)); // 原本是不可直接修改所屬店，需透過調貨，但Reebonz 這邊不需要這種限制
        }  

        return $this;
    }

    /**
     * 設置新增商品設定陣列
     * 
     * @param [object] $accessor
     * @param [array] $settings
     * @param [object] $request 
     * @param [object] $em      
     */
    public function setCreateSettings($accessor, $request, $em)
    {
        return $this
	            ->setCreateSettingsWithValue($accessor, $this->settings, $request)
	            ->setCreateSettingsWithEntity($accessor, $this->settings, $request, $em)
	        ;
    }

    /**
     * 設置新增商品設定陣列中屬於字串和數字的元素
     * 
     * @param [object] $accessor
     * @param [array] $settings
     * @param [object] $request
     */
    protected function setCreateSettingsWithValue($accessor, &$settings, $request)
    {
        $accessor->setValue($settings, '[setName]', $request->request->get('name'));
        $accessor->setValue($settings, '[setInType]', $request->request->get('in_type'));
        $accessor->setValue($settings, '[setDpo]', $request->request->get('dpo'));
        $accessor->setValue($settings, '[setPrice]', $request->request->get('price'));
        $accessor->setValue($settings, '[setFakePrice]', $request->request->get('fake_price'));
        $accessor->setValue($settings, '[setFeedback]', $request->request->get('feedback'));
        $accessor->setValue($settings, '[setCost]', $request->request->get('cost'));
        $accessor->setValue($settings, '[setOrgSn]', $request->request->get('org_sn'));
        $accessor->setValue($settings, '[setBrandSn]', $request->request->get('brand_sn'));
        $accessor->setValue($settings, '[setDes]', $request->request->get('des'));
        $accessor->setValue($settings, '[setMemo]', $request->request->get('memo'));
        $accessor->setValue($settings, '[setIsWeb]', $request->request->get('is_web'));
        $accessor->setValue($settings, '[setPurchaseAt]', new \DateTime($request->request->get('purchase_at')));
        $accessor->setValue($settings, '[setExpirateAt]', new \DateTime($request->request->get('expirate_at')));
        $accessor->setValue($settings, '[setImgpath]', self::NO_IMG); 
        $accessor->setValue($settings, '[amount]', $request->request->get('amount')); 

        return $this;
    }

    /**
     * 設置新增商品設定陣列屬於物件的雲素
     * 
     * @param [object] $accessor
     * @param [array] $settings
     * @param [object] $request 
     * @param [object] $em   
     */
    protected function setCreateSettingsWithEntity($accessor, &$settings, $request, $em)
    {
        $accessor->setValue($settings, '[setAllowDiscount]', $request->request->get('allow_discount'));
        $accessor->setValue($settings, '[setBrand]', $em->find('WoojinGoodsBundle:Brand', $request->request->get('brand', self::NONE_ENTITY)));
        $accessor->setValue($settings, '[setColor]', $em->find('WoojinGoodsBundle:Color', $request->request->get('color', self::NONE_ENTITY)));
        $accessor->setValue($settings, '[setPattern]', $em->find('WoojinGoodsBundle:Pattern', $request->request->get('pattern', self::NONE_ENTITY)));
        $accessor->setValue($settings, '[setLevel]', $em->find('WoojinGoodsBundle:GoodsLevel', $request->request->get('level', self::NONE_ENTITY)));
        $accessor->setValue($settings, '[setSource]', $em->find('WoojinGoodsBundle:GoodsSource' , $request->request->get('source', self::NONE_ENTITY)));
        $accessor->setValue($settings, '[setMt]', $em->find('WoojinGoodsBundle:GoodsMT', $request->request->get('mt', self::NONE_ENTITY)));
        $accessor->setValue($settings, '[setSupplier]', $em->find('WoojinGoodsBundle:Supplier', $request->request->get('supplier', self::NONE_ENTITY)));
        $accessor->setValue($settings, '[setStatus]', $em->find('WoojinGoodsBundle:GoodsStatus', $request->request->get('status', self::GS_ON_SALE)));
        $accessor->setValue($settings, '[setStore]', $em->find('WoojinStoreBundle:Store', $request->request->get('store', 1))); 

        return $this;
    }
}