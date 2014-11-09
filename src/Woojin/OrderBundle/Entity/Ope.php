<?php 
namespace Woojin\OrderBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

use JMS\Serializer\Annotation\ExclusionPolicy;
use JMS\Serializer\Annotation\Exclude;

use Woojin\OrderBundle\NullEntity\NullPayType;

/**
 * @ORM\Entity
 * @ORM\Table(name="Ope")
 * @ORM\HasLifecycleCallbacks()
 */
class Ope
{
    const PAY_TYPE_CASH = 1;

    /**
     * @Exclude
     * @ORM\ManyToOne(targetEntity="Orders", inversedBy="opes", cascade={"persist"})
     * @ORM\JoinColumn(name="orders_id", referencedColumnName="id")
     * @var Orders
     */
    protected $orders;

    /**
     * @ORM\ManyToOne(targetEntity="PayType", inversedBy="opes", cascade={"persist"})
     * @ORM\JoinColumn(name="pay_type_id", referencedColumnName="id")
     * @var Orders
     */
    protected $pay_type;

    /**
     * @ORM\ManyToOne(targetEntity="\Woojin\UserBundle\Entity\User", inversedBy="opes")
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
     * @ORM\Column(type="integer")
     */
    private $type;

    /**
     * 物件json序列化字串
     * 
     * @ORM\Column(type="text")
     */
    private $content;

    /**
     * @ORM\Column(type="datetime", length=50)
     */
    protected $create_at;

    /**
     * @ORM\Column(type="datetime", length=50)
     */
    protected $update_at;

    /**
     * @ORM\Column(type="string", length=20, nullable=true)
     */
    protected $card_sn;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    protected $memo;

    /**
     * 該動作影響金額，+ 表示入店，- 表示付出
     * @ORM\Column(type="integer", nullable=true)
     */
    protected $money;

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
     * Set type
     *
     * @param integer $type
     * @return Ope
     */
    public function setType($type)
    {
        $this->type = $type;
    
        return $this;
    }

    /**
     * Get type
     *
     * @return integer 
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Set content
     *
     * @param string $content
     * @return Ope
     */
    public function setContent($content)
    {
        $this->content = $content;
    
        return $this;
    }

    /**
     * Get content
     *
     * @return string 
     */
    public function getContent()
    {
        return $this->content;
    }

    /**
     * Set create_at
     *
     * @param \DateTime $createAt
     * @return Ope
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
     * Set update_at
     *
     * @param \DateTime $updateAt
     * @return Ope
     */
    public function setUpdateAt($updateAt)
    {
        $this->update_at = $updateAt;
    
        return $this;
    }

    /**
     * Get update_at
     *
     * @return \DateTime 
     */
    public function getUpdateAt()
    {
        return $this->update_at;
    }

    /**
     * Set memo
     *
     * @param string $memo
     * @return Ope
     */
    public function setMemo($memo)
    {
        $this->memo = $memo;
    
        return $this;
    }

    /**
     * Get memo
     *
     * @return string 
     */
    public function getMemo()
    {
        return $this->memo;
    }

    /**
     * Set money
     *
     * @param integer $money
     * @return Ope
     */
    public function setMoney($money)
    {
        $this->money = $money;
    
        return $this;
    }

    /**
     * Get money
     *
     * @return integer 
     */
    public function getMoney()
    {
        return $this->money;
    }

    /**
     * Set orders
     *
     * @param \Woojin\OrderBundle\Entity\Orders $orders
     * @return Ope
     */
    public function setOrders(\Woojin\OrderBundle\Entity\Orders $orders = null)
    {
        $this->orders = $orders;
    
        return $this;
    }

    /**
     * Get orders
     *
     * @return \Woojin\OrderBundle\Entity\Orders 
     */
    public function getOrders()
    {
        return $this->orders;
    }

    /**
     * Set pay_type
     *
     * @param \Woojin\OrderBundle\Entity\PayType $payType
     * @return Ope
     */
    public function setPayType(\Woojin\OrderBundle\Entity\PayType $payType = null)
    {
        $this->pay_type = $payType;
    
        return $this;
    }

    /**
     * Get pay_type
     *
     * @return \Woojin\OrderBundle\Entity\PayType 
     */
    public function getPayType()
    {
        return (!$this->pay_type) ? new NullPayType : $this->pay_type;
    }

    /**
     * Set user
     *
     * @param \Woojin\UserBundle\Entity\User $user
     * @return Ope
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

    /**
     * Set card_sn
     *
     * @param string $cardSn
     * @return Ope
     */
    public function setCardSn($cardSn)
    {
        $this->card_sn = $cardSn;
    
        return $this;
    }

    /**
     * Get card_sn
     *
     * @return string 
     */
    public function getCardSn()
    {
        return $this->card_sn;
    }

    public function addCardSnInList()
    {
        return ($this->getCardSn()) ? (string) $this->getCardSn() . ',' : '';
    }

    public function addCashPaid()
    {
        return (self::PAY_TYPE_CASH === $this->getPayType()->getId()) ? $this->getMoney() : 0;
    }
}