<?php
// src/Acme/UserBundle/Entity/User.php
namespace Woojin\UserBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Security\Core\User\AdvancedUserInterface;
use JMS\Serializer\Annotation\ExclusionPolicy;
use JMS\Serializer\Annotation\Exclude;

/**
 * Woojin\UserBundle\Entity\User
 *
 * @ORM\Table(name="Users")
 * @ORM\Entity(repositoryClass="Woojin\UserBundle\Entity\UserRepository")
 * @ORM\HasLifecycleCallbacks()
 */
class User implements AdvancedUserInterface, \Serializable
{
  /**
   * @Exclude
   * @ORM\OneToMany(targetEntity="\Woojin\OrderBundle\Entity\Move", mappedBy="req_user", cascade={"remove"})
   * @var ReqMoves[]
   */
  protected $req_moves;

  /**
   * @Exclude
   * @ORM\OneToMany(targetEntity="\Woojin\OrderBundle\Entity\Move", mappedBy="res_user", cascade={"remove"})
   * @var ResMoves[]
   */
  protected $res_moves;

  /**
   * @ORM\ManyToMany(targetEntity="Role", inversedBy="users")
   * @var Role
   */
  private $roles;

  /**
   * @Exclude
   * @ORM\OneToMany(targetEntity="\Woojin\StoreBundle\Entity\MetaRecord", mappedBy="user")
   * @var MetaRecord[]
   */
  protected $meta_records;

  /**
   * @Exclude
   * @ORM\OneToMany(targetEntity="\Woojin\OrderBundle\Entity\Ope", mappedBy="user")
   * @var Ope[]
   */
  protected $opes;

  /**
   * @Exclude
   * @ORM\OneToMany(targetEntity="UsersLog", mappedBy="user")
   * @var UsersLog[]
   */
  protected $users_logs;

  /**
   * @Exclude
   * @ORM\OneToMany(targetEntity="UsersHabit", mappedBy="user")
   * @var UsersHabit[]
   */
  protected $users_habits;

  /**
   * @ORM\ManyToOne(targetEntity="\Woojin\StoreBundle\Entity\Store", inversedBy="users")
   * @ORM\JoinColumn(name="store_id", referencedColumnName="id")
   * @var Store
   */
  protected $store;

  /**
   * @ORM\Column(type="integer")
   * @ORM\Id
   * @ORM\GeneratedValue(strategy="AUTO")
   */
  private $id;

  /**
   * @ORM\Column(type="string", length=25, unique=true)
   */
  private $realname;

  /**
   * @ORM\Column(type="string", length=25, unique=true)
   */
  private $username;

  /**
   * @Exclude
   * @ORM\Column(type="string", length=255)
   */
  private $salt;

  /**
   * @Exclude
   * @ORM\Column(type="string", length=255)
   */
  private $password;

  /**
   * @ORM\Column(type="string", length=60, unique=true)
   */
  private $email;

  /**
   * @ORM\Column(type="string", length=60, unique=true)
   */
  private $mobil;

  /**
   * @ORM\Column(type="datetime", length=60)
   */
  private $createAt;

  /**
   * @ORM\Column(type="datetime", length=60)
   */
  private $updateAt;

  /**
   * @ORM\Column(type="datetime", length=60, nullable=true)
   */
  private $stopAt;

  /**
   * @Exclude
   * @ORM\Column(type="integer", length=60)
   */
  private $chmod;

  /**
   * @ORM\Column(name="is_active", type="boolean")
   */
  private $isActive;

  /**
   * @Exclude
   * @ORM\Column(type="string", nullable=true)
   */
  private $csrf;

  /**
   * @ORM\PrePersist
   */
  public function autoSetCreateAt()
  {
    $this->setCreateAt(new \Datetime());
  }

  /**
   * @ORM\PrePersist
   * @ORM\PreUpdate
   */
  public function autoSetUpdateAt()
  {
      $this->setUpdateAt(new \Datetime());
  }

  public function __construct()
  {
    $this->isActive = true;
    $this->salt = md5(uniqid(null, true));
    $this->roles = new ArrayCollection();
  }

  /**
   * @inheritDoc
   */
  public function eraseCredentials()
  {
  }

  /**
   * @see \Serializable::serialize()
   */
  public function serialize()
  {
    return serialize(array(
      $this->id,
    ));
  }

  /**
   * @see \Serializable::unserialize()
   */
  public function unserialize($serialized)
  {
    list (
      $this->id,
    ) = unserialize($serialized);
  }

  /**
   * @inheritDoc
   */
  public function getUsername()
  {
    return $this->username;
  }

  /**
   * @inheritDoc
   */
  public function getSalt()
  {
    return $this->salt;
  }

  /**
   * @inheritDoc
   */
  public function getPassword()
  {
    return $this->password;
  }

  /**
   * @inheritDoc
   */
  public function getRoles()
  {
    return $this->roles->toArray();
  }

  public function isAccountNonExpired()
  {
    return true;
  }

  public function isAccountNonLocked()
  {
    return true;
  }

  public function isCredentialsNonExpired()
  {
    return true;
  }

  public function isEnabled()
  {
    return $this->isActive;
  }

    /**
     * Get id
     *
     * @return integer 
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set username
     *
     * @param string $username
     * @return User
     */
    public function setUsername($username)
    {
        $this->username = $username;
    
        return $this;
    }

    /**
     * Set salt
     *
     * @param string $salt
     * @return User
     */
    public function setSalt($salt)
    {
        $this->salt = $salt;
    
        return $this;
    }

    /**
     * Set password
     *
     * @param string $password
     * @return User
     */
    public function setPassword($password)
    {
        $this->password = $password;
    
        return $this;
    }

    /**
     * Set email
     *
     * @param string $email
     * @return User
     */
    public function setEmail($email)
    {
        $this->email = $email;
    
        return $this;
    }

    /**
     * Get email
     *
     * @return string 
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * Set mobil
     *
     * @param string $mobil
     * @return User
     */
    public function setMobil($mobil)
    {
        $this->mobil = $mobil;
    
        return $this;
    }

    /**
     * Get mobil
     *
     * @return string 
     */
    public function getMobil()
    {
        return $this->mobil;
    }

    /**
     * Set createAt
     *
     * @param \DateTime $createAt
     * @return User
     */
    public function setCreateAt($createAt)
    {
        $this->createAt = $createAt;
    
        return $this;
    }

    /**
     * Get createAt
     *
     * @return \DateTime 
     */
    public function getCreateAt()
    {
        return $this->createAt;
    }

    /**
     * Set updateAt
     *
     * @param \DateTime $updateAt
     * @return User
     */
    public function setUpdateAt($updateAt)
    {
        $this->updateAt = $updateAt;
    
        return $this;
    }

    /**
     * Get updateAt
     *
     * @return \DateTime 
     */
    public function getUpdateAt()
    {
        return $this->updateAt;
    }

    /**
     * Set stopAt
     *
     * @param \DateTime $stopAt
     * @return User
     */
    public function setStopAt($stopAt)
    {
        $this->stopAt = $stopAt;
    
        return $this;
    }

    /**
     * Get stopAt
     *
     * @return \DateTime 
     */
    public function getStopAt()
    {
        return $this->stopAt;
    }

    /**
     * Set chmod
     *
     * @param integer $chmod
     * @return User
     */
    public function setChmod($chmod)
    {
        $this->chmod = $chmod;
    
        return $this;
    }

    /**
     * Get chmod
     *
     * @return integer 
     */
    public function getChmod()
    {
        return $this->chmod;
    }

    /**
     * Set isActive
     *
     * @param boolean $isActive
     * @return User
     */
    public function setIsActive($isActive)
    {
        $this->isActive = $isActive;
    
        return $this;
    }

    /**
     * Get isActive
     *
     * @return boolean 
     */
    public function getIsActive()
    {
        return $this->isActive;
    }

    /**
     * Set csrf
     *
     * @param string $csrf
     * @return User
     */
    public function setCsrf($csrf)
    {
        $this->csrf = $csrf;
    
        return $this;
    }

    /**
     * Get csrf
     *
     * @return string 
     */
    public function getCsrf()
    {
        return $this->csrf;
    }

    /**
     * Add roles
     *
     * @param \Woojin\UserBundle\Entity\Role $roles
     * @return User
     */
    public function addRole(\Woojin\UserBundle\Entity\Role $roles)
    {
        $this->roles[] = $roles;
    
        return $this;
    }

    /**
     * Remove roles
     *
     * @param \Woojin\UserBundle\Entity\Role $roles
     */
    public function removeRole(\Woojin\UserBundle\Entity\Role $roles)
    {
        $this->roles->removeElement($roles);
    }

    /**
     * Add meta_records
     *
     * @param \Woojin\StoreBundle\Entity\MetaRecord $metaRecords
     * @return User
     */
    public function addMetaRecord(\Woojin\StoreBundle\Entity\MetaRecord $metaRecords)
    {
        $this->meta_records[] = $metaRecords;
    
        return $this;
    }

    /**
     * Remove meta_records
     *
     * @param \Woojin\StoreBundle\Entity\MetaRecord $metaRecords
     */
    public function removeMetaRecord(\Woojin\StoreBundle\Entity\MetaRecord $metaRecords)
    {
        $this->meta_records->removeElement($metaRecords);
    }

    /**
     * Get meta_records
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getMetaRecords()
    {
        return $this->meta_records;
    }

    /**
     * Add opes
     *
     * @param \Woojin\OrderBundle\Entity\Ope $opes
     * @return User
     */
    public function addOpe(\Woojin\OrderBundle\Entity\Ope $opes)
    {
        $this->opes[] = $opes;
    
        return $this;
    }

    /**
     * Remove opes
     *
     * @param \Woojin\OrderBundle\Entity\Ope $opes
     */
    public function removeOpe(\Woojin\OrderBundle\Entity\Ope $opes)
    {
        $this->opes->removeElement($opes);
    }

    /**
     * Get opes
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getOpes()
    {
        return $this->opes;
    }

    /**
     * Add users_logs
     *
     * @param \Woojin\UserBundle\Entity\UsersLog $usersLogs
     * @return User
     */
    public function addUsersLog(\Woojin\UserBundle\Entity\UsersLog $usersLogs)
    {
        $this->users_logs[] = $usersLogs;
    
        return $this;
    }

    /**
     * Remove users_logs
     *
     * @param \Woojin\UserBundle\Entity\UsersLog $usersLogs
     */
    public function removeUsersLog(\Woojin\UserBundle\Entity\UsersLog $usersLogs)
    {
        $this->users_logs->removeElement($usersLogs);
    }

    /**
     * Get users_logs
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getUsersLogs()
    {
        return $this->users_logs;
    }

    /**
     * Add users_habits
     *
     * @param \Woojin\UserBundle\Entity\UsersHabit $usersHabits
     * @return User
     */
    public function addUsersHabit(\Woojin\UserBundle\Entity\UsersHabit $usersHabits)
    {
        $this->users_habits[] = $usersHabits;
    
        return $this;
    }

    /**
     * Remove users_habits
     *
     * @param \Woojin\UserBundle\Entity\UsersHabit $usersHabits
     */
    public function removeUsersHabit(\Woojin\UserBundle\Entity\UsersHabit $usersHabits)
    {
        $this->users_habits->removeElement($usersHabits);
    }

    /**
     * Get users_habits
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getUsersHabits()
    {
        return $this->users_habits;
    }

    /**
     * Set store
     *
     * @param \Woojin\StoreBundle\Entity\Store $store
     * @return User
     */
    public function setStore(\Woojin\StoreBundle\Entity\Store $store = null)
    {
        $this->store = $store;
    
        return $this;
    }

    /**
     * Get store
     *
     * @return \Woojin\StoreBundle\Entity\Store 
     */
    public function getStore()
    {
        return $this->store;
    }

    /**
     * Set realname
     *
     * @param string $realname
     * @return User
     */
    public function setRealname($realname)
    {
        $this->realname = $realname;
    
        return $this;
    }

    /**
     * Get realname
     *
     * @return string 
     */
    public function getRealname()
    {
        return $this->realname;
    }

    /**
     * Add req_moves
     *
     * @param \Woojin\OrderBundle\Entity\Move $reqMoves
     * @return User
     */
    public function addReqMove(\Woojin\OrderBundle\Entity\Move $reqMoves)
    {
        $this->req_moves[] = $reqMoves;
    
        return $this;
    }

    /**
     * Remove req_moves
     *
     * @param \Woojin\OrderBundle\Entity\Move $reqMoves
     */
    public function removeReqMove(\Woojin\OrderBundle\Entity\Move $reqMoves)
    {
        $this->req_moves->removeElement($reqMoves);
    }

    /**
     * Get req_moves
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getReqMoves()
    {
        return $this->req_moves;
    }

    /**
     * Add res_moves
     *
     * @param \Woojin\OrderBundle\Entity\Move $resMoves
     * @return User
     */
    public function addResMove(\Woojin\OrderBundle\Entity\Move $resMoves)
    {
        $this->res_moves[] = $resMoves;
    
        return $this;
    }

    /**
     * Remove res_moves
     *
     * @param \Woojin\OrderBundle\Entity\Move $resMoves
     */
    public function removeResMove(\Woojin\OrderBundle\Entity\Move $resMoves)
    {
        $this->res_moves->removeElement($resMoves);
    }

    /**
     * Get res_moves
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getResMoves()
    {
        return $this->res_moves;
    }
}