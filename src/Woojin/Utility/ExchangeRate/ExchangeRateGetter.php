<?php

namespace Woojin\Utility\ExchangeRate;

use Doctrine\Common\Persistence\ManagerRegistry;

class ExchangeRateGetter
{
    /**
     * ManagerRegistry
     * 
     * @var [\Doctrine\Common\Persistence\ManagerRegistry]
     */
    protected $registry;

    protected $map = array();

    public function __construct(ManagerRegistry $registry)
    {
        $this->registry = $registry;
    }

    public function getExchangeRateByDate($datetime)
    {
        $month = substr($datetime, 0, 7);
        
        if (array_key_exists($month, $this->map)) {
            return $this->map[$month];
        }

        /**
         * Entity Manager
         * 
         * @var object
         */
        $em = $this->registry->getManager();

        $qb = $em->createQueryBuilder();

        $exchangeRate = $qb->select('ex')
            ->from('WoojinStoreBundle:ExchangeRate', 'ex')
            ->where($qb->expr()->eq('ex.month', $qb->expr()->literal($month)))
            ->getQuery()
            ->getResult()
        ;

        return $this->map[$month] = ($exchangeRate) ? $exchangeRate[0]->getRate() : 1;
    }
}