<?php

namespace Woojin\StoreBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Activity
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="Woojin\StoreBundle\Entity\ActivityRepository")
 */
class Activity
{
  /**
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
   * @var \DateTime
   *
   * @ORM\Column(name="start_at", type="datetime")
   */
  private $startAt;

  /**
   * @var \DateTime
   *
   * @ORM\Column(name="end_at", type="datetime")
   */
  private $endAt;

  /**
   * @var string
   *
   * @ORM\Column(name="description", type="text")
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
        $this->startAt = $startAt;
    
        return $this;
    }

    /**
     * Get startAt
     *
     * @return \DateTime 
     */
    public function getStartAt()
    {
        return $this->startAt;
    }

    /**
     * Set endAt
     *
     * @param \DateTime $endAt
     * @return Activity
     */
    public function setEndAt($endAt)
    {
        $this->endAt = $endAt;
    
        return $this;
    }

    /**
     * Get endAt
     *
     * @return \DateTime 
     */
    public function getEndAt()
    {
        return $this->endAt;
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
}