<?php

namespace Woojin\OrderBundle\NullEntity;

/**
 * Null PayType
 */
class NullPayType
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
     * Get name
     *
     * @return string 
     */
    public function getName()
    {
        return null;
    }

    /**
     * Get hasPrint
     *
     * @return float 
     */
    public function getDiscount()
    {
        return null;
    }
}