<?php

namespace Woojin\GoodsBundle\GoodsSettingHandler;

class RelatedEntityHandler
{
    const GS_ONSALE = 1;

    /**
     * 設置商品陣列中屬於關聯實體的元素
     * 
     * @param [object] $accessor 
     * @param [array] $settings 
     * @param [object] $request  
     * @param [object] $em       
     */
    public function run($request, $accessor, $em, &$settings)
    {
        return $this
            ->setBrand($request, $accessor, $em, $settings)
            ->setColor($request, $accessor, $em, $settings)
            ->setPattern($request, $accessor, $em, $settings)
            ->setLevel($request, $accessor, $em, $settings)
            ->setSource($request, $accessor, $em, $settings)
            ->setMt($request, $accessor, $em, $settings)
            ->setSupplier($request, $accessor, $em, $settings)
            ->setStatus($request, $accessor, $em, $settings)
            ->setStore($request, $accessor, $em, $settings)
        ;
    }

    protected function setBrand($request, $accessor, $em, &$settings)
    {
        if ($brand = $request->request->get('brand')) {
            $accessor->setValue($settings, '[setBrand]', $em->find('WoojinGoodsBundle:Brand', $brand));
        }

        return $this;
    } 

    protected function setColor($request, $accessor, $em, &$settings)
    {
        if ($color = $request->request->get('color')) {
            $accessor->setValue($settings, '[setColor]', $em->find('WoojinGoodsBundle:Color', $color));   
        }

        return $this;
    } 

    protected function setPattern($request, $accessor, $em, &$settings)
    {
        if ($pattern = $request->request->get('pattern')) {
            $accessor->setValue($settings, '[setPattern]', $em->find('WoojinGoodsBundle:Pattern', $pattern));   
        }

        return $this;
    } 

    protected function setLevel($request, $accessor, $em, &$settings)
    {
        if ($level = $request->request->get('level')) {
            $accessor->setValue($settings, '[setLevel]', $em->find('WoojinGoodsBundle:GoodsLevel', $level));   
        }

        return $this;
    } 

    protected function setSource($request, $accessor, $em, &$settings)
    {
        if ($source = $request->request->get('source')) {
            $accessor->setValue($settings, '[setSource]', $em->find('WoojinGoodsBundle:GoodsSource', $source));   
        }

        return $this;
    } 

    protected function setMt($request, $accessor, $em, &$settings)
    {
        if ($mt = $request->request->get('mt')) {
            $accessor->setValue($settings, '[setMt]', $em->find('WoojinGoodsBundle:GoodsMT', $mt));   
        }

        return $this;
    } 

    protected function setSupplier($request, $accessor, $em, &$settings)
    {
        if ($supplier = $request->request->get('supplier')) {
            $accessor->setValue($settings, '[setSupplier]', $em->find('WoojinGoodsBundle:Supplier', $supplier));
        }

        return $this;
    } 

    protected function setStatus($request, $accessor, $em, &$settings)
    {
        if ($status = $request->request->get('status', self::GS_ONSALE)) {
            $accessor->setValue($settings, '[setStatus]', $em->find('WoojinGoodsBundle:GoodsStatus', $status)); 

            $accessor->setValue($settings, '[setActivity]', null);
        }

        return $this;
    } 

    protected function setStore($request, $accessor, $em, &$settings)
    {
        if ($store = $request->request->get('store')) {
            $accessor->setValue($settings, '[setStore]', $em->find('WoojinStoreBundle:Store', $store)); // 原本是不可直接修改所屬店，需透過調貨，但Reebonz 這邊不需要這種限制
        }

        return $this;
    } 
}