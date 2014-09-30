<?php

namespace Woojin\GoodsBundle\GoodsSettingHandler;

class NotRelatedEntityHandler
{
    const NO_IMG = '/img/404.png';

    public function run($request, $accessor, &$settings) 
    {
        return $this
            ->setName($request, $accessor, $settings)
            ->setDpo($request, $accessor, $settings)
            ->setPrice($request, $accessor, $settings)
            ->setFakePrice($request, $accessor, $settings)
            ->setCost($request, $accessor, $settings)
            ->setOrgSn($request, $accessor, $settings)
            ->setBrandSn($request, $accessor, $settings)
            ->setDes($request, $accessor, $settings)
            ->setMemo($request, $accessor, $settings)
            ->setPurchaseAt($request, $accessor, $settings)
            ->setExpirateAt($request, $accessor, $settings)
            ->setAllowDiscount($request, $accessor, $settings)
            ->setIsWeb($request, $accessor, $settings)
            ->setInType($request, $accessor, $settings)
            ->setImgPath($request, $accessor, $settings)
        ;     
    }

    protected function setName($request, $accessor, &$settings)
    {
        if ($name = $request->request->get('name')) {
            $accessor->setValue($settings, '[setName]', $name);
        }

        return $this;
    }

    protected function setDpo($request, $accessor, &$settings)
    {
        if ($dpo = $request->request->get('dpo')) {
            $accessor->setValue($settings, '[setDpo]', $dpo);
        }

        return $this;
    }

    protected function setPrice($request, $accessor, &$settings)
    {
        if ($price = $request->request->get('price')) {
            $accessor->setValue($settings, '[setPrice]', $price);
        }

        return $this;
    }

    protected function setFakePrice($request, $accessor, &$settings)
    {
        if ($fakePrice = $request->request->get('fake_price')) {
            $accessor->setValue($settings, '[setFakePrice]', $fakePrice);
        }

        return $this;
    }

    protected function setCost($request, $accessor, &$settings)
    {
        if ($cost = $request->request->get('cost')) {
            $accessor->setValue($settings, '[setCost]', $cost);
        }

        return $this;
    }

    protected function setOrgSn($request, $accessor, &$settings)
    {
        if ($orgSn = $request->request->get('org_sn')) {
            $accessor->setValue($settings, '[setOrgSn]', $orgSn);
        }

        return $this;
    }

    protected function setBrandSn($request, $accessor, &$settings)
    {
        if ($brandSn = $request->request->get('brand_sn')) {
            $accessor->setValue($settings, '[setBrandSn]', $brandSn);
        }

        return $this;
    }

    protected function setDes($request, $accessor, &$settings)
    {
        if ($des = $request->request->get('des')) {
            $accessor->setValue($settings, '[setDes]', $des);
        }

        return $this;
    }

    protected function setMemo($request, $accessor, &$settings)
    {
        if ($memo = $request->request->get('memo')) {
            $accessor->setValue($settings, '[setMemo]', $memo);
        }

        return $this;
    }

    protected function setPurchaseAt($request, $accessor, &$settings)
    {
        if ($purchaseAt = $request->request->get('purchase_at')) {
            $accessor->setValue($settings, '[setPurchaseAt]', new \DateTime($purchaseAt));
        }

        return $this;
    }

    protected function setExpirateAt($request, $accessor, &$settings)
    {
        if ($expirateAt = $request->request->get('expirate_at')) {
            $accessor->setValue($settings, '[setExpirateAt]', new \DateTime($expirateAt));
        } 

        return $this;
    }

    protected function setAllowDiscount($request, $accessor, &$settings)
    {
        $accessor->setValue($settings, '[setAllowDiscount]', $request->request->get('allow_discount', 1));  

        return $this; 
    }

    protected function setIsWeb($request, $accessor, &$settings)
    {
        $accessor->setValue($settings, '[setIsWeb]', $request->request->get('is_web')); 

        return $this;
    }

    protected function setInType($request, $accessor, &$settings)
    {
        $accessor->setValue($settings, '[setInType]', $request->request->get('in_type')); 

        return $this;
    }

    protected function setImgPath($request, $accessor, &$settings)
    {
        // 若是404表示移除圖片，需要更新，其他的動作都交給ImgController.php，
        // 這邊會這樣處理的原因是，移除圖片本身沒有檔案上傳，所以ImgController.php 不會被呼叫，
        // 連帶的商品的圖片路徑也不會被ImgController.php 修改，因此在商品資訊更新就要自行先處理，
        // 而如果確實有修改圖片且上傳的話，就不需要進行此動作
        if ($request->request->get('imgpath') === self::NO_IMG) {
            $accessor->setValue($settings, '[setImgpath]', self::NO_IMG);
        } 

        return $this;
    }
}