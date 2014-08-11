<?php

namespace Woojin\OrderBundle;

use Woojin\OrderBundle\Entity\Orders;
use Woojin\OrderBundle\Entity\Ope;

use Symfony\Component\Security\Core\SecurityContext;

class OpeFactory implements \Woojin\BackendBundle\EntityFactory
{
  protected $em;
  protected $context;

  public function __construct(\Doctrine\ORM\EntityManager $em, SecurityContext $context)
  {
    $this->em = $em;
    $this->context = $context;
  }

  /**
   * 操作記錄製造
   * @param  [array] $settings [操作記錄屬性對應陣列, 完整格式為
   * array(
   *   'setOrders' => 關聯訂單實體,
   *   'setType' => 是CUD哪種動作,
   *   'setMoney' => 此次動作影響金額,
   *   'setContent' => 被影響之訂單實體json字串,
   *   'setUser' => 訂單操作者, 
   * )
   * @return [object] 生成的操作記錄
   */
  public function create($settings)
  {
    // 使用交易機制以防萬一
    $this->em->getConnection()->beginTransaction();

    try {
      $ope = new Ope;

      // 根據傳入的設定進行屬性設置
      foreach ($settings as $key => $val) {
        $ope->$key($val);
      }

      // 將結果保存，迴圈結束後再一次執行
      $this->em->persist($ope);

      $this->em->flush();
      $this->em->getConnection()->commit();
    } catch (Exception $e) {
      $this->em->getConnection()->rollback();
      throw $e;
    }    

    return $ope;
  }

  /**
   * 操作記錄修改
   * @param  [array] $settings [操作記錄屬性對應陣列, 完整格式為
   * array(
   *   'setOrders' => 關聯訂單實體,
   *   'setType' => 是CUD哪種動作,
   *   'setMoney' => 此次動作影響金額,
   *   'setContent' => 被影響之訂單實體json字串,
   *   'setUser' => 訂單應付金額, 
   * )
   * @return [object] 操作記錄
   */
  public function update($settings, $opes)
  {
    // 使用交易機制以防萬一
    $this->em->getConnection()->beginTransaction();

    try {
        // 根據傳入的設定進行屬性設置
        foreach ($settings as $key => $val) {
          $ope->$key($val);
        }

      // 將結果保存，迴圈結束後再一次執行
      $this->em->persist($ope);

      $this->em->flush();
      $this->em->getConnection()->commit();
    } catch (Exception $e) {
      $this->em->getConnection()->rollback();
      throw $e;
    }    

    return $ope;
  }

  public function copy($opes, $amount)
  {

  }
}
