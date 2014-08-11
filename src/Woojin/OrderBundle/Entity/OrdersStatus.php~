<?php 
namespace Woojin\OrderBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

use JMS\Serializer\Annotation\ExclusionPolicy;
use JMS\Serializer\Annotation\Exclude;

/**
 * @ORM\Entity
 * @ORM\Table(name="OrdersStatus")
 */
class OrdersStatus
{
    /**
     * @Exclude
     * @ORM\OneToMany(targetEntity="Move", mappedBy="status", cascade={"remove"})
     * @var Moves[]
     */
    protected $moves;

    /**
     * @Exclude
     * @ORM\OneToMany(targetEntity="Orders", mappedBy="status")
     * @var Orders[]
     */
    protected $orders;

    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\Column(type="string", length=100)
     */
    protected $name;
    /**
     * Constructor
     */
    public function __construct()
    {
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
     * Set name
     *
     * @param string $name
     * @return OrdersStatus
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
     * Add orders
     *
     * @param \Woojin\OrderBundle\Entity\Orders $orders
     * @return OrdersStatus
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
     * Add moves
     *
     * @param \Woojin\OrderBundle\Entity\Move $moves
     * @return OrdersStatus
     */
    public function addMove(\Woojin\OrderBundle\Entity\Move $moves)
    {
        $this->moves[] = $moves;
    
        return $this;
    }

    /**
     * Remove moves
     *
     * @param \Woojin\OrderBundle\Entity\Move $moves
     */
    public function removeMove(\Woojin\OrderBundle\Entity\Move $moves)
    {
        $this->moves->removeElement($moves);
    }

    /**
     * Get moves
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getMoves()
    {
        return $this->moves;
    }
}