<?php 
namespace Woojin\OrderBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

use JMS\Serializer\Annotation\ExclusionPolicy;
use JMS\Serializer\Annotation\Exclude;

use Woojin\OrderBundle\NullEntity\NullInvoice;
use Woojin\OrderBundle\NullEntity\NullPayType;

/**
 * @ORM\Entity(repositoryClass="Woojin\OrderBundle\Entity\OrdersRepository")
 * @ORM\Table(name="Orders")
 * @ORM\HasLifecycleCallbacks()
 */ 
class Orders
{
    const PT_CASH = 1;
    const PT_CARD = 2;

    /**
    * @ORM\ManyToOne(targetEntity="\Woojin\StoreBundle\Entity\Activity", inversedBy="orderses")
    * @ORM\JoinColumn(name="activity_id", referencedColumnName="id")
    * @var Activity
    */
    protected $activity;

    /**
     * @Exclude
     * @ORM\ManyToOne(targetEntity="Orders", inversedBy="childrens")
     * @ORM\JoinColumn(name="parent_id", referencedColumnName="id", onDelete="SET NULL")
     */
    protected $parent;

    /**
     * @ORM\OneToMany(targetEntity="Orders", mappedBy="parent")
     * @var Orders[]
     */
    protected $childrens;

    /**
     * @ORM\OneToMany(targetEntity="Ope", mappedBy="orders", cascade={"remove"})
     * @var Opes[]
     */
    protected $opes;

    /**
     * @Exclude
     * @ORM\OneToMany(targetEntity="Move", mappedBy="in_orders")
     * @var InMoves[]
     */
    protected $in_moves;

    /**
     * @Exclude
     * @ORM\OneToMany(targetEntity="Move", mappedBy="out_orders")
     * @var OutMoves[]
     */
    protected $out_moves;

    /**
     * @ORM\ManyToOne(targetEntity="\Woojin\GoodsBundle\Entity\GoodsPassport", inversedBy="orders", cascade={"persist"})
     * @ORM\JoinColumn(name="goods_passport_id", referencedColumnName="id")
     * @var GoodsPassport
     */
    protected $goods_passport;

    /**
     * @ORM\ManyToOne(targetEntity="PayType", inversedBy="orders")
     * @ORM\JoinColumn(name="pay_type_id", referencedColumnName="id")
     * @var PayType
     */
    protected $pay_type;

    /**
     * @ORM\ManyToOne(targetEntity="OrdersStatus", inversedBy="orders")
     * @ORM\JoinColumn(name="orders_status_id", referencedColumnName="id")
     * @var Orders
     */
    protected $status;

    /**
     * @ORM\ManyToOne(targetEntity="OrdersKind", inversedBy="orders")
     * @ORM\JoinColumn(name="orders_kind_id", referencedColumnName="id")
     * @var OrdersKind
     */
    protected $kind;

    /**
     * @ORM\ManyToOne(targetEntity="Custom", inversedBy="orders")
     * @ORM\JoinColumn(name="custom_id", referencedColumnName="id")
     * @var Custom
     */
    protected $custom;

    /**
     * @ORM\ManyToOne(targetEntity="Invoice", inversedBy="orders", cascade={"persist"})
     * @ORM\JoinColumn(name="invoice_id", referencedColumnName="id")
     * 
     * @var Invoice
     */
    protected $invoice;

    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\Column(type="text", length=255, nullable=true)
     */
    protected $memo;

    /**
     * @ORM\Column(type="integer", length=30, nullable=true)
     */
    protected $required;

    /**
     * @ORM\Column(type="integer", length=30, nullable=true)
     */
    protected $paid;

    /**
     * @var \DateTime
     * 
     * @ORM\Column(type="datetime", type="datetime")
     */
    protected $create_at;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="update_at", type="datetime")
     */
    protected $update_at;

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
     * Constructor
     */
    public function __construct()
    {
        $this->childrens = new \Doctrine\Common\Collections\ArrayCollection();
        $this->opes = new \Doctrine\Common\Collections\ArrayCollection();
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
     * Set memo
     *
     * @param string $memo
     * @return Orders
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
     * Set required
     *
     * @param integer $required
     * @return Orders
     */
    public function setRequired($required)
    {
        $this->required = $required;
    
        return $this;
    }

    /**
     * Get required
     *
     * @return integer 
     */
    public function getRequired()
    {
        return $this->required;
    }

    /**
     * Set paid
     *
     * @param integer $paid
     * @return Orders
     */
    public function setPaid($paid)
    {
        $this->paid = $paid;
    
        return $this;
    }

    /**
     * Get paid
     *
     * @return integer 
     */
    public function getPaid()
    {
        return $this->paid;
    }

    /**
     * Set create_at
     *
     * @param \DateTime $createAt
     * @return Orders
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
     * @return Orders
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
     * Set parent
     *
     * @param \Woojin\OrderBundle\Entity\Orders $parent
     * @return Orders
     */
    public function setParent(\Woojin\OrderBundle\Entity\Orders $parent = null)
    {
        $this->parent = $parent;
    
        return $this;
    }

    /**
     * Get parent
     *
     * @return \Woojin\OrderBundle\Entity\Orders 
     */
    public function getParent()
    {
        return $this->parent;
    }

    /**
     * Add childrens
     *
     * @param \Woojin\OrderBundle\Entity\Orders $childrens
     * @return Orders
     */
    public function addChildren(\Woojin\OrderBundle\Entity\Orders $childrens)
    {
        $this->childrens[] = $childrens;
    
        return $this;
    }

    /**
     * Remove childrens
     *
     * @param \Woojin\OrderBundle\Entity\Orders $childrens
     */
    public function removeChildren(\Woojin\OrderBundle\Entity\Orders $childrens)
    {
        $this->childrens->removeElement($childrens);
    }

    /**
     * Get childrens
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getChildrens()
    {
        return $this->childrens;
    }

    /**
     * Add opes
     *
     * @param \Woojin\OrderBundle\Entity\Ope $opes
     * @return Orders
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
     * 讓 opes 為空，以免儲存操作記錄時資料庫被灌暴
     */
    public function setOpesNull()
    {
        $this->opes = null;
    }

    /**
     * Set goods_passport
     *
     * @param \Woojin\GoodsBundle\Entity\GoodsPassport $goodsPassport
     * @return Orders
     */
    public function setGoodsPassport(\Woojin\GoodsBundle\Entity\GoodsPassport $goodsPassport = null)
    {
        $this->goods_passport = $goodsPassport;
    
        return $this;
    }

    /**
     * Get goods_passport
     *
     * @return \Woojin\GoodsBundle\Entity\GoodsPassport 
     */
    public function getGoodsPassport()
    {
        return $this->goods_passport;
    }

    /**
     * Set pay_type
     *
     * @param \Woojin\OrderBundle\Entity\PayType $payType
     * @return Orders
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
     * Set status
     *
     * @param \Woojin\OrderBundle\Entity\OrdersStatus $status
     * @return Orders
     */
    public function setStatus(\Woojin\OrderBundle\Entity\OrdersStatus $status = null)
    {
        $this->status = $status;
    
        return $this;
    }

    /**
     * Get status
     *
     * @return \Woojin\OrderBundle\Entity\OrdersStatus 
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * Set kind
     *
     * @param \Woojin\OrderBundle\Entity\OrdersKind $kind
     * @return Orders
     */
    public function setKind(\Woojin\OrderBundle\Entity\OrdersKind $kind = null)
    {
        $this->kind = $kind;
    
        return $this;
    }

    /**
     * Get kind
     *
     * @return \Woojin\OrderBundle\Entity\OrdersKind 
     */
    public function getKind()
    {
        return $this->kind;
    }

    /**
     * Set custom
     *
     * @param \Woojin\OrderBundle\Entity\Custom $custom
     * @return Orders
     */
    public function setCustom(\Woojin\OrderBundle\Entity\Custom $custom = null)
    {
        $this->custom = $custom;
    
        return $this;
    }

    /**
     * Get custom
     *
     * @return \Woojin\OrderBundle\Entity\Custom 
     */
    public function getCustom()
    {
        return $this->custom;
    }

    /**
     * Set invoice
     *
     * @param \Woojin\OrderBundle\Entity\Invoice $invoice
     * @return Orders
     */
    public function setInvoice(\Woojin\OrderBundle\Entity\Invoice $invoice = null)
    {
        $this->invoice = $invoice;
    
        return $this;
    }

    /**
     * Get invoice
     *
     * @return \Woojin\OrderBundle\Entity\Invoice 
     */
    public function getInvoice()
    {
        return (!$this->invoice) ? new NullInvoice : $this->invoice;
    }

    /**
     * Add in_moves
     *
     * @param \Woojin\OrderBundle\Entity\Move $inMoves
     * @return Orders
     */
    public function addInMove(\Woojin\OrderBundle\Entity\Move $inMoves)
    {
        $this->in_moves[] = $inMoves;
    
        return $this;
    }

    /**
     * Remove in_moves
     *
     * @param \Woojin\OrderBundle\Entity\Move $inMoves
     */
    public function removeInMove(\Woojin\OrderBundle\Entity\Move $inMoves)
    {
        $this->in_moves->removeElement($inMoves);
    }

    /**
     * Get in_moves
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getInMoves()
    {
        return $this->in_moves;
    }

    /**
     * Add out_moves
     *
     * @param \Woojin\OrderBundle\Entity\Move $outMoves
     * @return Orders
     */
    public function addOutMove(\Woojin\OrderBundle\Entity\Move $outMoves)
    {
        $this->out_moves[] = $outMoves;
    
        return $this;
    }

    /**
     * Remove out_moves
     *
     * @param \Woojin\OrderBundle\Entity\Move $outMoves
     */
    public function removeOutMove(\Woojin\OrderBundle\Entity\Move $outMoves)
    {
        $this->out_moves->removeElement($outMoves);
    }

    /**
     * Get out_moves
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getOutMoves()
    {
        return $this->out_moves;
    }

    /**
     * 取得現金已付總額
     * 
     * @return integer
     */
    public function getCashPaid()
    {
        return $this->getSingleTypeOfPaid(self::PT_CASH);
    }

    /**
     * 取得刷卡已付總額
     * 
     * @return integer
     */
    public function getCardPaid()
    {
        return $this->getSingleTypeOfPaid(self::PT_CARD);
    }

    /**
     * 取得某種付費方式的已付總額
     * 
     * @param  [integer] $type [付費方式的id]
     * @return [integer] $total
     */
    protected function getSingleTypeOfPaid($type)
    {
        /**
         * 總金額
         * 
         * @var integer
         */
        $total = 0;

        foreach ($this->opes as $ope) {
            if ($ope->getPayType()->getId() === $type) {
                $total += $ope->getMoney();
            }
        }

        return $total;
    }

    /**
     * Set activity
     *
     * @param \Woojin\StoreBundle\Entity\Activity $activity
     * @return Orders
     */
    public function setActivity(\Woojin\StoreBundle\Entity\Activity $activity = null)
    {
        $this->activity = $activity;
    
        return $this;
    }

    /**
     * Get activity
     *
     * @return \Woojin\StoreBundle\Entity\Activity 
     */
    public function getActivity()
    {
        return $this->activity;
    }
}