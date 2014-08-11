<?php

namespace Woojin\OrderBundle\Entity;

use Doctrine\ORM\Tools\Pagination\Paginator;
use Symfony\Component\Routing\Exception\InvalidParameterException;

class CustomRepository extends \Woojin\WoojinRepository
{
	/**
	 * 藉由 id 組成的陣列取得實體
	 * 
	 * @param  [array] $ids 
	 * @return [array] 
	 */
	public function findByIds($ids)
	{
		$qb = $this->getEntityManager()->createQueryBuilder();

    	$qb->select('c')->from('WoojinOrderBundle:Custom', 'c')->where($qb->expr()->in('c.id', $ids));

    	$customs = $qb->getQuery()->getResult();

    	return $customs;
	}

	/**
	 * 使用制定的參數尋找客戶
	 * 
	 * @param  [array] $conditions [根據各種屬性組合的陣列]
	 * @param  [array] $orderBy [排序根據的欄位和冪向] [option]
	 * @param  [integer] $page [指定在第幾頁，亦即是 setFirstResult( ($page - 1) * $perPage )] [option]
	 * @param  [integer] $perPage [每頁幾筆，亦即是 setMaxResults() 中的引數] [option]
	 * @return [object] 取得的客戶實體陣列
	 */
    public function findByFilter($conditions, $orderBy = null, $page = null, $perPage = null)
    {
    	$qb = $this->getEntityManager()->createQueryBuilder();

    	$qb->select('c')->from('WoojinOrderBundle:Custom', 'c');
        
        // 根據傳入的條件陣列執行$qb 的方法
        $this->parseFilter($qb, $conditions);

        // 避免重複選到客戶
        $qb->groupBy('c.id');

        // 排序結果
        if ($orderBy) {
            $qb->orderBy('c.' . $orderBy['attr'], $orderBy['dir']);
        }

        // 取得的資料限制範圍
        if ($page && $perPage) {
        	$qb->setFirstResult(($page - 1) * $perPage)->setMaxResults($perPage);
        }

        return $qb->getQuery()->getResult();
    }

    /**
	 * 取得使用制定的參數尋找客戶時將會得到的數量
	 * 
	 * @param  [array] $conditions [根據各種屬性組合的陣列]
	 * @return [integer] 
	 */
    public function findCountByFilter($conditions)
    {
		$qb = $this->getEntityManager()->createQueryBuilder();

    	$qb->select('c')->from('WoojinOrderBundle:Custom', 'c');

    	// 根據傳入的條件陣列執行$qb 的方法
    	$this->parseFilter($qb, $conditions);

    	// 避免重複選到商品
    	$qb->groupBy('c.id');

    	/**
    	 * Starting with version 2.2 Doctrine ships with a Paginator for DQL queries,
    	 * Much more ez way to get count of query result
    	 * 
    	 * @var object
    	 */
    	$paginator = new Paginator($qb, $fetchJoinCollection = true);

        return count($paginator);
    }
}