<?php

namespace Woojin\StoreBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation\ExclusionPolicy;
use JMS\Serializer\Annotation\Exclude;

/**
 * Activity
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="Woojin\StoreBundle\Entity\ActivityRepository")
 */
class Activity
{
  /**
   * @Exclude
   * @ORM\OneToMany(targetEntity="\Woojin\GoodsBundle\Entity\GoodsPassport", mappedBy="activity")
   * @var GoodsPassport[]
   */
  protected $goods_passports;

  /**
   * @var integer
   *
   * @ORM\Column(name="id", type="integer")
   * @ORM\Id
   * @ORM\GeneratedValue(strategy="AUTO")
   */
  private $id;

  /**
   * @var string
   *
   * @ORM\Column(name="name", type="string", length=255, unique=true)
   */
  private $name;

  /**
   * @var string
   *
   * @ORM\Column(name="discount", type="integer", nullable=true)
   */
  private $discount;

  /**
   * @var string
   *
   * @ORM\Column(name="exceed", type="integer", nullable=true)
   */
  private $exceed;

  /**
   * @var string
   *
   * @ORM\Column(name="minus", type="integer", nullable=true)
   */
  private $minus;

  /**
   * @var \DateTime
   *
   * @ORM\Column(name="start_at", type="datetime")
   */
  private $start_at;

  /**
   * @var \DateTime
   *
   * @ORM\Column(name="end_at", type="datetime")
   */
  private $end_at;

  /**
   * @var string
   *
   * @ORM\Column(name="description", type="text", nullable=true)
   */
  private $description;
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->goods_passports = new \Doctrine\Common\Collections\ArrayCollection();
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
     * Set name
     *
     * @param string $name
     * @return Activity
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
     * Set startAt
     *
     * @param \DateTime $startAt
     * @return Activity
     */
    public function setStartAt($startAt)
    {
        $this->start_at = $startAt;
    
        return $this;
    }

    /**
     * Get startAt
     *
     * @return \DateTime 
     */
    public function getStartAt()
    {
        return $this->start_at;
    }

    /**
     * Set endAt
     *
     * @param \DateTime $endAt
     * @return Activity
     */
    public function setEndAt($endAt)
    {
        $this->end_at = $endAt;
    
        return $this;
    }

    /**
     * Get endAt
     *
     * @return \DateTime 
     */
    public function getEndAt()
    {
        return $this->end_at;
    }

    /**
     * Set description
     *
     * @param string $description
     * @return Activity
     */
    public function setDescription($description)
    {
        $this->description = $description;
    
        return $this;
    }

    /**
     * Get description
     *
     * @return string 
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Add goods_passports
     *
     * @param \Woojin\GoodsBundle\Entity\GoodsPassport $goodsPassports
     * @return Activity
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
     * Set discount
     *
     * @param float $discount
     * @return Activity
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
     * Set exceed
     *
     * @param integer $exceed
     * @return Activity
     */
    public function setExceed($exceed)
    {
        $this->exceed = $exceed;
    
        return $this;
    }

    /**
     * Get exceed
     *
     * @return integer 
     */
    public function getExceed()
    {
        return $this->exceed;
    }

    /**
     * Set minus
     *
     * @param integer $minus
     * @return Activity
     */
    public function setMinus($minus)
    {
        $this->minus = $minus;
    
        return $this;
    }

    /**
     * Get minus
     *
     * @return integer 
     */
    public function getMinus()
    {
        return $this->minus;
    }
}