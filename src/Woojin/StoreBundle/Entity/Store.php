<?php 
namespace Woojin\StoreBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation\ExclusionPolicy;
use JMS\Serializer\Annotation\Exclude;

/**
 * @ORM\Entity
 * @ORM\Table(name="Store")
 */
class Store
{
    /**
     * @Exclude
     * @ORM\OneToMany(targetEntity="\Woojin\OrderBundle\Entity\Move", mappedBy="req_store", cascade={"remove"})
     * @var ReqMoves[]
     */
    protected $req_moves;

    /**
     * @Exclude
     * @ORM\OneToMany(targetEntity="\Woojin\OrderBundle\Entity\Move", mappedBy="res_store", cascade={"remove"})
     * @var ResMoves[]
     */
    protected $res_moves;

    /**
    * @Exclude
    * @ORM\OneToMany(targetEntity="\Woojin\UserBundle\Entity\User", mappedBy="store")
    * @var User[]
    */
    protected $users;

    /**
    * @Exclude
    * @ORM\OneToMany(targetEntity="\Woojin\OrderBundle\Entity\Custom", mappedBy="store")
    * @var Custom[]
    */
    protected $customs;

    /**
    * @Exclude
    * @ORM\OneToMany(targetEntity="\Woojin\OrderBundle\Entity\Invoice", mappedBy="store")
    * @var Invoice[]
    */
    protected $invoices;

    /**
    * @Exclude
    * @ORM\OneToMany(targetEntity="\Woojin\GoodsBundle\Entity\GoodsPassport", mappedBy="store")
    * @var GoodsPassport[]
    */
    protected $goods_passports;

    /**
    * @ORM\Id
    * @ORM\Column(type="integer")
    * @ORM\GeneratedValue(strategy="AUTO")
    */
    protected $id;

    /**
    * @ORM\Column(type="string", length=30, unique=true)
    */
    protected $sn;

    /**
    * @ORM\Column(type="string", length=100, unique=true)
    */
    protected $name;
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->users = new \Doctrine\Common\Collections\ArrayCollection();
        $this->customs = new \Doctrine\Common\Collections\ArrayCollection();
        $this->invoices = new \Doctrine\Common\Collections\ArrayCollection();
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
     * Set sn
     *
     * @param string $sn
     * @return Store
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
     * @return Store
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
     * Add users
     *
     * @param \Woojin\UserBundle\Entity\User $users
     * @return Store
     */
    public function addUser(\Woojin\UserBundle\Entity\User $users)
    {
        $this->users[] = $users;
    
        return $this;
    }

    /**
     * Remove users
     *
     * @param \Woojin\UserBundle\Entity\User $users
     */
    public function removeUser(\Woojin\UserBundle\Entity\User $users)
    {
        $this->users->removeElement($users);
    }

    /**
     * Get users
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getUsers()
    {
        return $this->users;
    }

    /**
     * Add customs
     *
     * @param \Woojin\OrderBundle\Entity\Custom $customs
     * @return Store
     */
    public function addCustom(\Woojin\OrderBundle\Entity\Custom $customs)
    {
        $this->customs[] = $customs;
    
        return $this;
    }

    /**
     * Remove customs
     *
     * @param \Woojin\OrderBundle\Entity\Custom $customs
     */
    public function removeCustom(\Woojin\OrderBundle\Entity\Custom $customs)
    {
        $this->customs->removeElement($customs);
    }

    /**
     * Get customs
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getCustoms()
    {
        return $this->customs;
    }

    /**
     * Add invoices
     *
     * @param \Woojin\OrderBundle\Entity\Invoice $invoices
     * @return Store
     */
    public function addInvoice(\Woojin\OrderBundle\Entity\Invoice $invoices)
    {
        $this->invoices[] = $invoices;
    
        return $this;
    }

    /**
     * Remove invoices
     *
     * @param \Woojin\OrderBundle\Entity\Invoice $invoices
     */
    public function removeInvoice(\Woojin\OrderBundle\Entity\Invoice $invoices)
    {
        $this->invoices->removeElement($invoices);
    }

    /**
     * Get invoices
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getInvoices()
    {
        return $this->invoices;
    }

    /**
     * Add goods_passports
     *
     * @param \Woojin\GoodsBundle\Entity\GoodsPassport $goodsPassports
     * @return Store
     */
    public function addGoodsPassport(\Woojin\GoodsBundle\Entity\GoodsPassport $goodsPassports)
    {
        $this->goods_passports[] = $goodsPassports;
    
        return $this;
    }

    /**
     * Remove goods_passports
     *
     * @param \Woojin\GoodsBundle\Entity\GoodsPassport $goodsPassports
     */
    public function removeGoodsPassport(\Woojin\GoodsBundle\Entity\GoodsPassport $goodsPassports)
    {
        $this->goods_passports->removeElement($goodsPassports);
    }

    /**
     * Get goods_passports
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getGoodsPassports()
    {
        return $this->goods_passports;
    }

    /**
     * Add req_moves
     *
     * @param \Woojin\OrderBundle\Entity\Move $reqMoves
     * @return Store
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
     * @return Store
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