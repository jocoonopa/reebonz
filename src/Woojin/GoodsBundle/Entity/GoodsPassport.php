<?php 
namespace Woojin\GoodsBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use JMS\Serializer\Annotation\ExclusionPolicy;
use JMS\Serializer\Annotation\Exclude;

use \Woojin\StoreBundle\NullEntity\NullActivity;

/**
 * @ORM\Entity(repositoryClass="Woojin\GoodsBundle\Entity\GoodsPassportRepository")
 * @ORM\Table(name="GoodsPassport")
 * @ORM\HasLifecycleCallbacks()
 */
class GoodsPassport
{
	/**
	* @Exclude
	* @ORM\OneToMany(targetEntity="\Woojin\OrderBundle\Entity\Move", mappedBy="out_goods_passport")
	* @var OutMoves[]
	*/
	protected $out_moves;

	/**
	* @Exclude
	* @ORM\OneToMany(targetEntity="\Woojin\OrderBundle\Entity\Move", mappedBy="in_goods_passport")
	* @var InMoves[]
	*/
	protected $in_moves;

	/**
	* @Exclude
	* @ORM\OneToMany(targetEntity="\Woojin\OrderBundle\Entity\Orders", mappedBy="goods_passport", cascade={"remove"})
	* @var Orders[]
	*/
	protected $orders;

	/**
	* @ORM\ManyToOne(targetEntity="\Woojin\StoreBundle\Entity\Store", inversedBy="goods_passports")
	* @ORM\JoinColumn(name="store_id", referencedColumnName="id")
	* @var Store
	*/
	protected $store;

	/**
	* @ORM\ManyToOne(targetEntity="\Woojin\OrderBundle\Entity\Custom", inversedBy="goods_passports")
	* @ORM\JoinColumn(name="consigner_id", referencedColumnName="id")
	* @var Consigner
	*/
	protected $consigner;

	/**
	* @ORM\ManyToOne(targetEntity="Supplier", inversedBy="goods_passports")
	* @ORM\JoinColumn(name="supplier_id", referencedColumnName="id")
	* @var Supplier
	*/
	protected $supplier;

	/**
	* @ORM\ManyToOne(targetEntity="Color", inversedBy="goods_passports")
	* @ORM\JoinColumn(name="color_id", referencedColumnName="id")
	* @var Color
	*/
	protected $color;

	/**
	* @ORM\ManyToOne(targetEntity="Brand", inversedBy="goods_passports")
	* @ORM\JoinColumn(name="brand_id", referencedColumnName="id")
	* @var Brand
	*/
	protected $brand;

	/**
	* @ORM\ManyToOne(targetEntity="Pattern", inversedBy="goods_passports")
	* @ORM\JoinColumn(name="pattern_id", referencedColumnName="id")
	* @var Pattern
	*/
	protected $pattern;

	/**
	* @ORM\ManyToOne(targetEntity="GoodsStatus", inversedBy="goods_passports")
	* @ORM\JoinColumn(name="goods_status_id", referencedColumnName="id")
	* @var Status
	*/
	protected $status;

	/**
	* @ORM\ManyToOne(targetEntity="GoodsSource", inversedBy="goods_passports")
	* @ORM\JoinColumn(name="goods_source_id", referencedColumnName="id")
	* @var Source
	*/
	protected $source;

	/**
	* @ORM\ManyToOne(targetEntity="\Woojin\StoreBundle\Entity\Activity", inversedBy="goods_passports")
	* @ORM\JoinColumn(name="activity_id", referencedColumnName="id")
	* @var Activity
	*/
	protected $activity;

	/**
	* @ORM\ManyToOne(targetEntity="GoodsMT", inversedBy="goods_passports")
	* @ORM\JoinColumn(name="goods_mt_id", referencedColumnName="id")
	* @var MT
	*/
	protected $mt;

	/**
	* @ORM\ManyToOne(targetEntity="GoodsLevel", inversedBy="goods_passports")
	* @ORM\JoinColumn(name="goods_level_id", referencedColumnName="id")
	* @var GoodsLevel
	*/
	protected $level;

	/**
	* @var integer
	* 
	* @ORM\Id
	* @ORM\Column(type="integer")
	* @ORM\GeneratedValue(strategy="AUTO")
	*/
	protected $id;

	/**
	* @var integer
	* 
	* @ORM\Column(type="integer", nullable=true)
	*/
	protected $inherit_id;

	/**
	* @ORM\Column(type="string", nullable=true)
	*/
	protected $sn;

	/**
	* @ORM\Column(type="string")
	*/
	protected $name;

	/**
	* @var integer
	* 
	* @ORM\Column(type="integer", length=10)
	*/
	protected $cost;

	/**
	* @var integer
	* 
	* @ORM\Column(type="integer", length=10)
	*/
	protected $price;

	/**
	* @var integer
	* 
	* @ORM\Column(type="integer", length=10, nullable=true)
	*/
	protected $fake_price;

	/**
	* @var integer
	* 
	* @ORM\Column(type="integer", length=10, nullable=true)
	*/
	protected $feedback = 0;

	/**
	* @var string 
	* 
	* @ORM\Column(type="string", length=100, nullable=true)
	*/
	protected $org_sn;

	/**
	* @var string
	* 
	* @ORM\Column(type="text", nullable=true)
	*/
	protected $des;

	/**
	* @var string
	* 
	* @ORM\Column(type="text", nullable=true)
	*/
	protected $memo;

	/**
	* @var string
	* 
	* @ORM\Column(type="string", nullable=true)
	*/
	protected $brand_sn;

	/**
	* @var string
	* 
	* @ORM\Column(type="string", nullable=true)
	*/
	protected $dpo;

	/**
	* @var boolean
	*
	* @ORM\Column(name="allow_discount", type="boolean")
	*/
	protected $allow_discount;

	/**
	* @var float
	*
	* @ORM\Column(name="discount", type="float", nullable=true)
	*/
	private $discount;

	/**
	* @var boolean
	*
	* @ORM\Column(name="in_type", type="boolean")
	*/
	private $in_type;

	/**
	* @var boolean
	*
	* @ORM\Column(name="is_web", type="boolean")
	*/
	private $is_web;

	/**
	* @var string
	* 
	* @ORM\Column(type="string", nullable=true)
	*/
	private $imgpath;

	/**
	* @var \DateTime
	* 
	* @ORM\Column(type="datetime", type="datetime", nullable=true)
	*/
	protected $purchase_at;

	/**
	* @var \DateTime
	* 
	* @ORM\Column(type="datetime", type="datetime", nullable=true)
	*/
	protected $expirate_at;

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
	* @ORM\PostPersist
	*/
	public function autoSetInheritId()
	{
		$this->setInheritId($this->getId());
	}

	/**
	* @ORM\PostPersist
	* @ORM\PreUpdate
	*/
	public function autoSetSnWithReebonzWay() 
	{
		$this->setSn($this->getReebonzSn());
	}

	/**
	* 取得 REEBONZ 規則的產編
	* 
	* @return string
	*/
	protected function getReebonzSn()
	{
		/**
		 * Reebonz way sn
		 * 
		 * @var string
		 */
		$sn = null;

		$sn = $this->getStore()->getSn();
		$sn.= substr($this->getPurchaseAt()->format('Ymd'), 3);
		$sn.= $this->getSupplier()->getName();
		$sn.= str_pad($this->getId(), 5, 0, STR_PAD_LEFT);

		return $sn;
	}

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->out_moves = new \Doctrine\Common\Collections\ArrayCollection();
        $this->in_moves = new \Doctrine\Common\Collections\ArrayCollection();
        $this->orders = new \Doctrine\Common\Collections\ArrayCollection();
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
     * Set inherit_id
     *
     * @param integer $inheritId
     * @return GoodsPassport
     */
    public function setInheritId($inheritId)
    {
        $this->inherit_id = $inheritId;
    
        return $this;
    }

    /**
     * Get inherit_id
     *
     * @return integer 
     */
    public function getInheritId()
    {
        return $this->inherit_id;
    }

    /**
     * Set sn
     *
     * @param string $sn
     * @return GoodsPassport
     */
    public function setSn($sn)
    {
        $this->sn = $sn;
    
        return $this;
    }

    /**
     * Get sn
     *
     * @return string 
     */
    public function getSn()
    {
        return $this->sn;
    }

    /**
     * Set name
     *
     * @param string $name
     * @return GoodsPassport
     */
    public function setName($name)
    {
        $this->name = $name;
    
        return $this;
    }

    /**
     * Get name
     *
     * @return string 
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set cost
     *
     * @param integer $cost
     * @return GoodsPassport
     */
    public function setCost($cost)
    {
        $this->cost = $cost;
    
        return $this;
    }

    /**
     * Get cost
     *
     * @return integer 
     */
    public function getCost()
    {
        return $this->cost;
    }

    /**
     * Set price
     *
     * @param integer $price
     * @return GoodsPassport
     */
    public function setPrice($price)
    {
        $this->price = $price;
    
        return $this;
    }

    /**
     * Get price
     *
     * @return integer 
     */
    public function getPrice()
    {
        return $this->price;
    }

    /**
     * Set fake_price
     *
     * @param integer $fakePrice
     * @return GoodsPassport
     */
    public function setFakePrice($fakePrice)
    {
        $this->fake_price = $fakePrice;
    
        return $this;
    }

    /**
     * Get fake_price
     *
     * @return integer 
     */
    public function getFakePrice()
    {
        return $this->fake_price;
    }

    /**
     * Set feedback
     *
     * @param integer $feedback
     * @return GoodsPassport
     */
    public function setFeedback($feedback)
    {
        $this->feedback = $feedback;
    
        return $this;
    }

    /**
     * Get feedback
     *
     * @return integer 
     */
    public function getFeedback()
    {
        return $this->feedback;
    }

    /**
     * Set org_sn
     *
     * @param string $orgSn
     * @return GoodsPassport
     */
    public function setOrgSn($orgSn)
    {
        $this->org_sn = $orgSn;
    
        return $this;
    }

    /**
     * Get org_sn
     *
     * @return string 
     */
    public function getOrgSn()
    {
        return $this->org_sn;
    }

    /**
     * Set des
     *
     * @param string $des
     * @return GoodsPassport
     */
    public function setDes($des)
    {
        $this->des = $des;
    
        return $this;
    }

    /**
     * Get des
     *
     * @return string 
     */
    public function getDes()
    {
        return $this->des;
    }

    /**
     * Set memo
     *
     * @param string $memo
     * @return GoodsPassport
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
     * Set brand_sn
     *
     * @param string $brandSn
     * @return GoodsPassport
     */
    public function setBrandSn($brandSn)
    {
        $this->brand_sn = $brandSn;
    
        return $this;
    }

    /**
     * Get brand_sn
     *
     * @return string 
     */
    public function getBrandSn()
    {
        return $this->brand_sn;
    }

    /**
     * Set dpo
     *
     * @param string $dpo
     * @return GoodsPassport
     */
    public function setDpo($dpo)
    {
        $this->dpo = $dpo;
    
        return $this;
    }

    /**
     * Get dpo
     *
     * @return string 
     */
    public function getDpo()
    {
        return $this->dpo;
    }

    /**
     * Set allow_discount
     *
     * @param boolean $allowDiscount
     * @return GoodsPassport
     */
    public function setAllowDiscount($allowDiscount)
    {
        $this->allow_discount = $allowDiscount;
    
        return $this;
    }

    /**
     * Get allow_discount
     *
     * @return boolean 
     */
    public function getAllowDiscount()
    {
        return $this->allow_discount;
    }

    /**
     * Set discount
     *
     * @param float $discount
     * @return GoodsPassport
     */
    public function setDiscount($discount)
    {
        $this->discount = $discount;
    
        return $this;
    }

    /**
     * Get discount
     *
     * @return float 
     */
    public function getDiscount()
    {
        return $this->discount;
    }

    /**
     * Set in_type
     *
     * @param boolean $inType
     * @return GoodsPassport
     */
    public function setInType($inType)
    {
        $this->in_type = $inType;
    
        return $this;
    }

    /**
     * Get in_type
     *
     * @return boolean 
     */
    public function getInType()
    {
        return $this->in_type;
    }

    /**
     * Set is_web
     *
     * @param boolean $isWeb
     * @return GoodsPassport
     */
    public function setIsWeb($isWeb)
    {
        $this->is_web = $isWeb;
    
        return $this;
    }

    /**
     * Get is_web
     *
     * @return boolean 
     */
    public function getIsWeb()
    {
        return $this->is_web;
    }

    /**
     * Set imgpath
     *
     * @param string $imgpath
     * @return GoodsPassport
     */
    public function setImgpath($imgpath)
    {
        $this->imgpath = $imgpath;
    
        return $this;
    }

    /**
     * Get imgpath
     *
     * @return string 
     */
    public function getImgpath()
    {
        return $this->imgpath;
    }

    /**
     * Set purchase_at
     *
     * @param \DateTime $purchaseAt
     * @return GoodsPassport
     */
    public function setPurchaseAt($purchaseAt)
    {
        $this->purchase_at = $purchaseAt;
    
        return $this;
    }

    /**
     * Get purchase_at
     *
     * @return \DateTime 
     */
    public function getPurchaseAt()
    {
        return $this->purchase_at;
    }

    /**
     * Set expirate_at
     *
     * @param \DateTime $expirateAt
     * @return GoodsPassport
     */
    public function setExpirateAt($expirateAt)
    {
        $this->expirate_at = $expirateAt;
    
        return $this;
    }

    /**
     * Get expirate_at
     *
     * @return \DateTime 
     */
    public function getExpirateAt()
    {
        return $this->expirate_at;
    }

    /**
     * Set create_at
     *
     * @param \DateTime $createAt
     * @return GoodsPassport
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
     * @return GoodsPassport
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
     * Add out_moves
     *
     * @param \Woojin\OrderBundle\Entity\Move $outMoves
     * @return GoodsPassport
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
     * Add in_moves
     *
     * @param \Woojin\OrderBundle\Entity\Move $inMoves
     * @return GoodsPassport
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
     * Add orders
     *
     * @param \Woojin\OrderBundle\Entity\Orders $orders
     * @return GoodsPassport
     */
    public function addOrder(\Woojin\OrderBundle\Entity\Orders $orders)
    {
        $this->orders[] = $orders;
    
        return $this;
    }

    /**
     * Remove orders
     *
     * @param \Woojin\OrderBundle\Entity\Orders $orders
     */
    public function removeOrder(\Woojin\OrderBundle\Entity\Orders $orders)
    {
        $this->orders->removeElement($orders);
    }

    /**
     * Get orders
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getOrders()
    {
        return $this->orders;
    }

    /**
     * Set store
     *
     * @param \Woojin\StoreBundle\Entity\Store $store
     * @return GoodsPassport
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
     * Set consigner
     *
     * @param \Woojin\OrderBundle\Entity\Custom $consigner
     * @return GoodsPassport
     */
    public function setConsigner(\Woojin\OrderBundle\Entity\Custom $consigner = null)
    {
        $this->consigner = $consigner;
    
        return $this;
    }

    /**
     * Get consigner
     *
     * @return \Woojin\OrderBundle\Entity\Custom 
     */
    public function getConsigner()
    {
        return $this->consigner;
    }

    /**
     * Set supplier
     *
     * @param \Woojin\GoodsBundle\Entity\Supplier $supplier
     * @return GoodsPassport
     */
    public function setSupplier(\Woojin\GoodsBundle\Entity\Supplier $supplier = null)
    {
        $this->supplier = $supplier;
    
        return $this;
    }

    /**
     * Get supplier
     *
     * @return \Woojin\GoodsBundle\Entity\Supplier 
     */
    public function getSupplier()
    {
        return $this->supplier;
    }

    /**
     * Set color
     *
     * @param \Woojin\GoodsBundle\Entity\Color $color
     * @return GoodsPassport
     */
    public function setColor(\Woojin\GoodsBundle\Entity\Color $color = null)
    {
        $this->color = $color;
    
        return $this;
    }

    /**
     * Get color
     *
     * @return \Woojin\GoodsBundle\Entity\Color 
     */
    public function getColor()
    {
        return $this->color;
    }

    /**
     * Set brand
     *
     * @param \Woojin\GoodsBundle\Entity\Brand $brand
     * @return GoodsPassport
     */
    public function setBrand(\Woojin\GoodsBundle\Entity\Brand $brand = null)
    {
        $this->brand = $brand;
    
        return $this;
    }

    /**
     * Get brand
     *
     * @return \Woojin\GoodsBundle\Entity\Brand 
     */
    public function getBrand()
    {
        return $this->brand;
    }

    /**
     * Set pattern
     *
     * @param \Woojin\GoodsBundle\Entity\Pattern $pattern
     * @return GoodsPassport
     */
    public function setPattern(\Woojin\GoodsBundle\Entity\Pattern $pattern = null)
    {
        $this->pattern = $pattern;
    
        return $this;
    }

    /**
     * Get pattern
     *
     * @return \Woojin\GoodsBundle\Entity\Pattern 
     */
    public function getPattern()
    {
        return $this->pattern;
    }

    /**
     * Set status
     *
     * @param \Woojin\GoodsBundle\Entity\GoodsStatus $status
     * @return GoodsPassport
     */
    public function setStatus(\Woojin\GoodsBundle\Entity\GoodsStatus $status = null)
    {
        $this->status = $status;
    
        return $this;
    }

    /**
     * Get status
     *
     * @return \Woojin\GoodsBundle\Entity\GoodsStatus 
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * Set source
     *
     * @param \Woojin\GoodsBundle\Entity\GoodsSource $source
     * @return GoodsPassport
     */
    public function setSource(\Woojin\GoodsBundle\Entity\GoodsSource $source = null)
    {
        $this->source = $source;
    
        return $this;
    }

    /**
     * Get source
     *
     * @return \Woojin\GoodsBundle\Entity\GoodsSource 
     */
    public function getSource()
    {
        return $this->source;
    }

    /**
     * Set activity
     *
     * @param \Woojin\StoreBundle\Entity\Activity $activity
     * @return GoodsPassport
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
    	return (!$this->activity) ? new NullActivity : $this->activity;
    }

    /**
     * Set mt
     *
     * @param \Woojin\GoodsBundle\Entity\GoodsMT $mt
     * @return GoodsPassport
     */
    public function setMt(\Woojin\GoodsBundle\Entity\GoodsMT $mt = null)
    {
        $this->mt = $mt;
    
        return $this;
    }

    /**
     * Get mt
     *
     * @return \Woojin\GoodsBundle\Entity\GoodsMT 
     */
    public function getMt()
    {
        return $this->mt;
    }

    /**
     * Set level
     *
     * @param \Woojin\GoodsBundle\Entity\GoodsLevel $level
     * @return GoodsPassport
     */
    public function setLevel(\Woojin\GoodsBundle\Entity\GoodsLevel $level = null)
    {
        $this->level = $level;
    
        return $this;
    }

    /**
     * Get level
     *
     * @return \Woojin\GoodsBundle\Entity\GoodsLevel 
     */
    public function getLevel()
    {
        return $this->level;
    }
}