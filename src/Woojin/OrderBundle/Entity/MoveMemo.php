<?php 
namespace Woojin\OrderBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

use JMS\Serializer\Annotation\ExclusionPolicy;
use JMS\Serializer\Annotation\Exclude;

/**
 * @ORM\Entity
 * @ORM\Table(name="MoveMemo")
 * @ORM\HasLifecycleCallbacks()
 */
class MoveMemo
{
    /**
     * @Exclude
     * @ORM\ManyToOne(targetEntity="Move", inversedBy="memos")
     * @ORM\JoinColumn(name="move_id", referencedColumnName="id")
     * @var Move
     */
    protected $move;

    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\Column(type="text", length=255, nullable=true)
     */
    protected $content;

    /**
     * @ORM\Column(type="datetime", length=50)
     */
    protected $create_at;

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
     * Set content
     *
     * @param string $content
     * @return MoveMemo
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
     * @return MoveMemo
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
     * Set move
     *
     * @param \Woojin\OrderBundle\Entity\Move $move
     * @return MoveMemo
     */
    public function setMove(\Woojin\OrderBundle\Entity\Move $move = null)
    {
        $this->move = $move;
    
        return $this;
    }

    /**
     * Get move
     *
     * @return \Woojin\OrderBundle\Entity\Move 
     */
    public function getMove()
    {
        return $this->move;
    }
}