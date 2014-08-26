<?php

namespace Woojin\OrderBundle;

use Woojin\OrderBundle\Entity\Custom;
use Symfony\Component\Security\Core\SecurityContext;
use Symfony\Component\PropertyAccess\PropertyAccess;
use Doctrine\Common\Persistence\ManagerRegistry;

class CustomFactory implements \Woojin\BackendBundle\EntityFactory
{
  protected $registry;
  protected $context;

  public function __construct(ManagerRegistry $registry, SecurityContext $context)
  {
    $this->registry = $registry;
    $this->context = $context;
  }

  /**
   * 新增使用者
   * @param  [array] $settings [使用者屬性對應陣列, 完整格式為
   * array(
   *   'addRole' => 權限實體，
   *   'setUsernam' => 使用者名稱,
   *   'setEmail' => 使用者電子郵件,
   *   'setPassword' => 使用者密碼( 明碼 ),
   *   'setMobil' => 手機( 電話 ),
   *   'setChmod' => 權限碼,
   *   'setIsActive' => 是否啟用,
   *   'setStore' => 商店實體 
   * )
   * @return [object] 生成的訂單
   */
  public function create($settings)
  {
    $em = $this->registry->getManager();

    /**
     * 新增一個客戶實體
     * @var object
     */
    $custom = new Custom;

    // 使用交易機制以防萬一
    $em->getConnection()->beginTransaction();

    try {
      // 根據傳入的設定進行屬性設置
      foreach ($settings as $key => $val) {
        $custom->$key($val);
      }

      // 將結果保存，迴圈結束後再一次執行
      $em->persist($custom);

      $em->flush();
      $em->getConnection()->commit();
    } catch (\Exception $e) {
      $em->getConnection()->rollback();
      throw $e;
    }    

    return $custom;
  }

  /**
   * 使用者修改
   * @param  [array] $settings [使用者屬性對應陣列, 完整格式為
   * array(
   *   'addRole' => 權限實體，
   *   'setUsernam' => 使用者名稱,
   *   'setEmail' => 使用者電子郵件,
   *   'setPassword' => 使用者密碼( 明碼 ),
   *   'setMobil' => 手機( 電話 ),
   *   'setChmod' => 權限碼,
   *   'setIsActive' => 是否啟用,
   *   'setStore' => 商店實體 
   * )
   * @return [object] 修改後的使用者實體
   */
  public function update($settings, $custom)
  {
    $em = $this->registry->getManager();

    // 使用交易機制以防萬一
    $em->getConnection()->beginTransaction();

    try {
      // 根據傳入的設定進行屬性設置
      foreach ($settings as $key => $val) {
        $custom->$key($val);
      }

      // 將結果保存，迴圈結束後再一次執行
      $em->persist($custom);

      $em->flush();
      $em->getConnection()->commit();
    } catch (\Exception $e) {
      $em->getConnection()->rollback();
      throw $e;
    }    

    return $custom;
  }

  public function copy($customs, $amount)
  {

  }
}
