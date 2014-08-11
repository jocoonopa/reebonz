<?php 
namespace Woojin\OrderBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

use JMS\Serializer\Annotation\ExclusionPolicy;
use JMS\Serializer\Annotation\Exclude;

/**
 * @ORM\Entity(repositoryClass="Woojin\OrderBundle\Entity\MoveRepository")
 * @ORM\Table(name="Move")
 * @ORM\HasLifecycleCallbacks()
 */
class Move
{
    /**
     * @ORM\ManyToOne(targetEntity="Orders", inversedBy="in_moves")
     * @ORM\JoinColumn(name="in_orders_id", referencedColumnName="id")
     * @var InOrders
     */
    protected $in_orders;

    /**
     * @ORM\ManyToOne(targetEntity="Orders", inversedBy="out_moves")
     * @ORM\JoinColumn(name="out_orders_id", referencedColumnName="id")
     * @var ResOrders
     */
    protected $out_orders;

    /**
     * @ORM\ManyToOne(targetEntity="\Woojin\UserBundle\Entity\User", inversedBy="req_moves")
     * @ORM\JoinColumn(name="req_user_id", referencedColumnName="id")
     * @var ReqUser
     */
    protected $req_user;

    /**
     * @ORM\ManyToOne(targetEntity="\Woojin\UserBundle\Entity\User", inversedBy="res_moves")
     * @ORM\JoinColumn(name="res_user_id", referencedColumnName="id")
     * @var ResOrders
     */
    protected $res_user;

    /**
     * @ORM\ManyToOne(targetEntity="\Woojin\StoreBundle\Entity\Store", inversedBy="req_moves")
     * @ORM\JoinColumn(name="req_store_id", referencedColumnName="id")
     * @var ReqStore
     */
    protected $req_store;

    /**
     * @ORM\ManyToOne(targetEntity="\Woojin\StoreBundle\Entity\Store", inversedBy="res_moves")
     * @ORM\JoinColumn(name="res_store_id", referencedColumnName="id")
     * @var ResStore
     */
    protected $res_store;

    /**
     * @ORM\ManyToOne(targetEntity="\Woojin\GoodsBundle\Entity\GoodsPassport", inversedBy="out_moves")
     * @ORM\JoinColumn(name="out_goods_passport_id", referencedColumnName="id")
     * @var OutGoodsPassport
     */
    protected $out_goods_passport;

    /**
     * @ORM\ManyToOne(targetEntity="\Woojin\GoodsBundle\Entity\GoodsPassport", inversedBy="in_moves")
     * @ORM\JoinColumn(name="in_goods_passport_id", referencedColumnName="id")
     * @var InGoodsPassport
     */
    protected $in_goods_passport;

    /**
     * @ORM\ManyToOne(targetEntity="OrdersStatus", inversedBy="moves")
     * @ORM\JoinColumn(name="orders_status_id", referencedColumnName="id")
     * @var Status
     */
    protected $status;

    /**
     * @ORM\OneToMany(targetEntity="MoveMemo", mappedBy="move", cascade={"remove"})
     * @var Memos[]
     */
    protected $memos;

    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

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
        $this->memos = new \Doctrine\Common\Collections\ArrayCollection();
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
     * @return Move
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
     * @return Move
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
     * Set in_orders
     *
     * @param \Woojin\OrderBundle\Entity\Orders $inOrders
     * @return Move
     */
    public function setInOrders(\Woojin\OrderBundle\Entity\Orders $inOrders = null)
    {
        $this->in_orders = $inOrders;
    
        return $this;
    }

    /**
     * Get in_orders
     *
     * @return \Woojin\OrderBundle\Entity\Orders 
     */
    public function getInOrders()
    {
        return $this->in_orders;
    }

    /**
     * Set out_orders
     *
     * @param \Woojin\OrderBundle\Entity\Orders $outOrders
     * @return Move
     */
    public function setOutOrders(\Woojin\OrderBundle\Entity\Orders $outOrders = null)
    {
        $this->out_orders = $outOrders;
    
        return $this;
    }

    /**
     * Get out_orders
     *
     * @return \Woojin\OrderBundle\Entity\Orders 
     */
    public function getOutOrders()
    {
        return $this->out_orders;
    }

    /**
     * Set req_user
     *
     * @param \Woojin\UserBundle\Entity\User $reqUser
     * @return Move
     */
    public function setReqUser(\Woojin\UserBundle\Entity\User $reqUser = null)
    {
        $this->req_user = $reqUser;
    
        return $this;
    }

    /**
     * Get req_user
     *
     * @return \Woojin\UserBundle\Entity\User 
     */
    public function getReqUser()
    {
        return $this->req_user;
    }

    /**
     * Set res_user
     *
     * @param \Woojin\UserBundle\Entity\User $resUser
     * @return Move
     */
    public function setResUser(\Woojin\UserBundle\Entity\User $resUser = null)
    {
        $this->res_user = $resUser;
    
        return $this;
    }

    /**
     * Get res_user
     *
     * @return \Woojin\UserBundle\Entity\User 
     */
    public function getResUser()
    {
        return $this->res_user;
    }

    /**
     * Set req_store
     *
     * @param \Woojin\StoreBundle\Entity\Store $reqStore
     * @return Move
     */
    public function setReqStore(\Woojin\StoreBundle\Entity\Store $reqStore = null)
    {
        $this->req_store = $reqStore;
    
        return $this;
    }

    /**
     * Get req_store
     *
     * @return \Woojin\StoreBundle\Entity\Store 
     */
    public function getReqStore()
    {
        return $this->req_store;
    }

    /**
     * Set res_store
     *
     * @param \Woojin\StoreBundle\Entity\Store $resStore
     * @return Move
     */
    public function setResStore(\Woojin\StoreBundle\Entity\Store $resStore = null)
    {
        $this->res_store = $resStore;
    
        return $this;
    }

    /**
     * Get res_store
     *
     * @return \Woojin\StoreBundle\Entity\Store 
     */
    public function getResStore()
    {
        return $this->res_store;
    }

    /**
     * Set out_goods_passport
     *
     * @param \Woojin\GoodsBundle\Entity\GoodsPassport $outGoodsPassport
     * @return Move
     */
    public function setOutGoodsPassport(\Woojin\GoodsBundle\Entity\GoodsPassport $outGoodsPassport = null)
    {
        $this->out_goods_passport = $outGoodsPassport;
    
        return $this;
    }

    /**
     * Get out_goods_passport
     *
     * @return \Woojin\GoodsBundle\Entity\GoodsPassport 
     */
    public function getOutGoodsPassport()
    {
        return $this->out_goods_passport;
    }

    /**
     * Set in_goods_passport
     *
     * @param \Woojin\GoodsBundle\Entity\GoodsPassport $inGoodsPassport
     * @return Move
     */
    public function setInGoodsPassport(\Woojin\GoodsBundle\Entity\GoodsPassport $inGoodsPassport = null)
    {
        $this->in_goods_passport = $inGoodsPassport;
    
        return $this;
    }

    /**
     * Get in_goods_passport
     *
     * @return \Woojin\GoodsBundle\Entity\GoodsPassport 
     */
    public function getInGoodsPassport()
    {
        return $this->in_goods_passport;
    }

    /**
     * Set status
     *
     * @param \Woojin\OrderBundle\Entity\OrdersStatus $status
     * @return Move
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
     * Add memos
     *
     * @param \Woojin\OrderBundle\Entity\MoveMemo $memos
     * @return Move
     */
    public function addMemo(\Woojin\OrderBundle\Entity\MoveMemo $memos)
    {
        $this->memos[] = $memos;
    
        return $this;
    }

    /**
     * Remove memos
     *
     * @param \Woojin\OrderBundle\Entity\MoveMemo $memos
     */
    public function removeMemo(\Woojin\OrderBundle\Entity\MoveMemo $memos)
    {
        $this->memos->removeElement($memos);
    }

    /**
     * Get memos
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getMemos()
    {
        return $this->memos;
    }
}