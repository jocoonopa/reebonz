<?php

namespace Woojin\GoodsBundle;

use Doctrine\Common\Persistence\ManagerRegistry;

class GoodsSaleSwitcher
{
    /**
     * ManagerRegistry
     * 
     * @var [\Doctrine\Common\Persistence\ManagerRegistry]
     */
    protected $registry;

    public function __construct(ManagerRegistry $registry)
    {
        $this->registry = $registry;
    }

    public function switchGoodesSaleStatusTo($statusId)
    {
        $endStatusId = $statusId; 
        
        $startStatusId = self::GS_ONSALE + self::GS_OFFSALE - $endStatusId;

        /**
         * Entity Manager
         * 
         * @var object
         */
        $em = $this->registry->getManager();

        /**
         * 商品下架狀態
         * 
         * @var \Woojin\GoodsBundle\Entity\GoodsStatus
         */
        $endStatus = $em->find('WoojinGoodsBundle:GoodsStatus', $endStatusId);

        /**
         * 商品產編陣列
         * 
         * @var array
         */
        $goodsIds = $this->getIdsFromRequest($row, $goodsIds);

        /**
         * QueryBuilder
         * 
         * @var object
         */
        $qb = $em->createQueryBuilder();

        /**
         * 商品實體陣列
         * 
         * @var array{object}
         */
        $goodses = $this->getWithfilterStatus($qb, $startStatusId, $goodsIds);

        $this->setGoodsesStatus($goodses, $em, $endStatus);

        return $this;
    }

    protected function getIdsFromRequest($row, $goodsIds)
    {
        $request = Request::createFromGlobals();

        return array_map(function ($row) use ($goodsIds) {
            array_push($goodsIds, $row['id']);
        }, $request->request->get('goodsPost'));
    } 

    protected function setGoodsesStatus($goodses, &$em, $status)
    {
        array_walk(function ($goods) use (&$em, $status) {
            // update 商品屬性
            $goods->setStatus($status);

            $em->persist($goods);
        }, $goodses);

        $em->flush();

        return $this;
    }

    protected function getWithfilterStatus(&$qb, $goodsIds, $statusId)
    {
        return $qb
            ->select('g')
            ->from('WoojinGoodsBundle:GoodsPassport', 'g')
            ->where(
                $qb->expr()->andX(
                    $qb->expr()->eq('g.status', self::GS_ONSALE),
                    $qb->expr()->in('g.id', $goodsIds)
                )
            )
            ->getQuery()
            ->getResult()
        ;
    }
}