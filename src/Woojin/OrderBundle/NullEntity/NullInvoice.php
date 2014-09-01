<?php

namespace Woojin\OrderBundle\NullEntity;

/**
 * Null Invoice
 */
class NullInvoice
{
    /**
     * Get id
     *
     * @return integer 
     */
    public function getId()
    {
        return null;
    }

    /**
     * Get sn
     *
     * @return string 
     */
    public function getSn()
    {
        return null;
    }

    /**
     * Get hasPrint
     *
     * @return boolean 
     */
    public function getHasPrint()
    {
        return null;
    }

    /**
     * Get createAt
     *
     * @return \DateTime 
     */
    public function getCreateAt()
    {
        return null;
    }

    /**
     * Get updateAt
     *
     * @return \DateTime 
     */
    public function getUpdateAt()
    {
        return null;
    }

    /**
     * Get orders
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getOrders()
    {
        return null;
    }

    /**
     * Get store
     *
     * @return \Woojin\StoreBundle\Entity\Store 
     */
    public function getStore()
    {
        return null;
    }

    /**
     * Get custom
     *
     * @return \Woojin\OrderBundle\Entity\Custom 
     */
    public function getCustom()
    {
        return null;
    }

    /**
     * Get user
     *
     * @return \Woojin\UserBundle\Entity\User 
     */
    public function getUser()
    {
        return null;
    }
}