<?php

namespace Woojin\UserBundle;

use Woojin\UserBundle\Entity\User;

use Symfony\Component\Security\Core\SecurityContext;
use Symfony\Component\PropertyAccess\PropertyAccess;
use Symfony\Component\DependencyInjection\ContainerInterface;

class UserFactory implements \Woojin\BackendBundle\EntityFactory
{
  protected $em;
  protected $context;

  public function __construct(\Doctrine\ORM\EntityManager $em, SecurityContext $context,ContainerInterface $container)
  {
    $this->em = $em;
    $this->context = $context;
    $this->container = $container;
  }

  /**
   * 新增使用者
   * @param  [array] $settings [使用者屬性對應陣列, 完整格式為
   * array(
   *   'addRole' => 權限實體，
   *   'setRealname' => 真實姓名,
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
    /**
     * Encoder
     * @var object
     */
    $encoder = $this->container->get('security.encoder_factory')->getEncoder($user = new User());
    
    /**
     * Symfony 的屬性套件，透過它可以用物件方式讀寫陣列
     * @var object
     */
    $accessor = PropertyAccess::createPropertyAccessor();

    /**
     * 加密後的密碼，資料庫存的是它
     * @var string
     */
    $password = $encoder->encodePassword($accessor->getValue($settings,'[setPassword]'), $user->getSalt());

    // 將加密過的密碼擺回設定
    $accessor->setValue($settings, '[setPassword]', $password);

    // 使用交易機制以防萬一
    $this->em->getConnection()->beginTransaction();

    try {
      // 根據傳入的設定進行屬性設置
      foreach ($settings as $key => $val) {
        $user->$key($val);
      }

      // 將結果保存，迴圈結束後再一次執行
      $this->em->persist($user);

      $this->em->flush();
      $this->em->getConnection()->commit();
    } catch (Exception $e) {
      $this->em->getConnection()->rollback();
      throw $e;
    }    

    return $user;
  }

  /**
   * 使用者修改
   * @param  [array] $settings [使用者屬性對應陣列, 完整格式為
   * array(
   *   'addRole' => 權限實體，
   *   'setRealname' => 真實姓名,
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
  public function update($settings, $user)
  {
    /**
     * Encoder
     * @var object
     */
    $encoder = $this->container->get('security.encoder_factory')->getEncoder($user);
    
    /**
     * Symfony 的屬性套件，透過它可以用物件方式讀寫陣列
     * @var object
     */
    $accessor = PropertyAccess::createPropertyAccessor();

    /**
     * 取得現有權限，回傳會是一個陣列，而該陣列應該只會有一個 role 實體
     * @var array{obejct}
     */
    $roles = $user->getRoles();

    // 使用交易機制以防萬一
    $this->em->getConnection()->beginTransaction();

    try {    
      // 移除現有權限
      $user->removeRole($roles[0]);

      // 根據傳入的設定進行屬性設置
      foreach ($settings as $key => $val) {
        $user->$key($val);
      }

      // 將結果保存，迴圈結束後再一次執行
      $this->em->persist($user);

      $this->em->flush();
      $this->em->getConnection()->commit();
    } catch (Exception $e) {
      $this->em->getConnection()->rollback();
      throw $e;
    }    

    return $user;
  }

  public function copy($users, $amount)
  {

  }
}
