<?php

namespace Woojin\OrderBundle;

use Woojin\OrderBundle\Entity\Order;
use Woojin\OrderBundle\Entity\Ope

/**
 *  用來將發票資料組成成適當格式回送給客戶端
 */
class InvoiceFormatter
{
	public function setInvoiceMeta($invoiceRepo, $invoice)
  {   
    $invoiceRepo->id = $invoice->getId();
    $invoiceRepo->sn = $invoice->getSn();
    $invoiceRepo->store = $invoice->getStore()->getStoreName();
    $invoiceRepo->creatAt = $invoice->getCreateAt();
    $invoiceRepo->updateAt = $invoice->getUpdateAt();
    $invoiceRepo->hasPrint = $invoice->getHasPrint();

    return $this;
  }

  public function setInvoiceCustom($customRepo, $custom)
  {
    $customRepo->name = $custom->getCustomName();
    $customRepo->phone = $custom->getCustomMobil();

    return $this;
  }

  public function setInvoiceOrdersGoods($goods, $goodsPassport)
  {
    $goods->brand = $goodsPassport->getBrandSn()->getBrandSnName();
            
    $goods->brandType = $goodsPassport->getBrandSn()->getBrandType()
        ->getBrandTypeName();
    
    $goods->brandSn = $goodsPassport->getBrandSn()->getBrandType()
        ->getBrand()->getBrandName();

    $goods->name = $goodsPassport->getGoodsName();
    $goods->sn = $goodsPassport->getGoodsSn();
    $goods->orgSn = $goodsPassport->getGoodsOrgSn();

    if (is_object($goodsPassport->getGoodsMt())) {
        $goods->material = $goodsPassport->getGoodsMt()->getName();
    }
    
    $goods->level = $goodsPassport->getGoodsLevel()->getGoodsLevelName();

    return $this;
  }

  public function setInvoiceOrders($orderRepo, $order)
  {
    $orderRepo->id = $order->getOrdersId();
    $orderRepo->paid = $order->getOrdersPaid();
    $orderRepo->required = $order->getOrdersRequired();
    $orderRepo->status = $order->getOrdersStatus()->getOrdersStatusName();
    $orderRepo->memo = $order->getOrdersMemo();

    return $this;
  }
}
