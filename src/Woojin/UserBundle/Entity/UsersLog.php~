<?php 
namespace Woojin\UserBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="UsersLog")
 * @ORM\HasLifecycleCallbacks()
 */
class UsersLog
{
  /**
   * @ORM\ManyToOne(targetEntity="User", inversedBy="users_logs")
   * @ORM\JoinColumn(name="user_id", referencedColumnName="id")
   * @var User
   */
  protected $user;

  /**
   * @ORM\Id
   * @ORM\Column(type="integer")
   * @ORM\GeneratedValue(strategy="AUTO")
   */
  protected $id;

  /**
   * @ORM\Column(type="datetime", length=50)
   */
  protected $create_at;

  /**
   * @ORM\Column(type="string", length=30)
   */
  protected $ip;

  /**
   * @ORM\Column(type="string", length=150, nullable=true)
   */
  protected $error;

  /**
   * @ORM\PrePersist
   */
  public function autoSetCreateAt()
  {
    $this->setCreateAt(new \Datetime());
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
     * Set create_at
     *
     * @param \DateTime $createAt
     * @return UsersLog
     */
    public function setCreateAt($createAt)
    {
        $this->create_at = $createAt;
    
        return $this;
    }

    /**
     * Get create_at
     *
     * @return \DateTime 
     */
    public function getCreateAt()
    {
        return $this->create_at;
    }

    /**
     * Set ip
     *
     * @param string $ip
     * @return UsersLog
     */
    public function setIp($ip)
    {
        $this->ip = $ip;
    
        return $this;
    }

    /**
     * Get ip
     *
     * @return string 
     */
    public function getIp()
    {
        return $this->ip;
    }

    /**
     * Set error
     *
     * @param string $error
     * @return UsersLog
     */
    public function setError($error)
    {
        $this->error = $error;
    
        return $this;
    }

    /**
     * Get error
     *
     * @return string 
     */
    public function getError()
    {
        return $this->error;
    }

    /**
     * Set user
     *
     * @param \Woojin\UserBundle\Entity\User $user
     * @return UsersLog
     */
    public function setUser(\Woojin\UserBundle\Entity\User $user = null)
    {
        $this->user = $user;
    
        return $this;
    }

    /**
     * Get user
     *
     * @return \Woojin\UserBundle\Entity\User 
     */
    public function getUser()
    {
        return $this->user;
    }
}