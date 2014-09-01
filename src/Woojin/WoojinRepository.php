<?php

namespace Woojin;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Symfony\Component\Routing\Exception\InvalidParameterException;

class WoojinRepository extends EntityRepository
{
    /**
     * 根據傳入的條件陣列執行$qb 的方法
     * 
     * @param  [object] $qb         
     * @param  [array] $conditions
     * @return [object] $qb
     */
    protected function parseFilter($qb, $conditions)
    {
        // 根據條件執行對應的 query builder 方法，這邊就不啦了，應該不會有別的地方會用到吧
        foreach ($conditions as $attr => $condition) {
            // 解碼$attr
            $attr = $this->attrDecode($attr);

            if (!is_array($condition)) {

                switch ($condition)
                {
                    case 'isNotNull':

                        $qb->andWhere($qb->expr()->isNotNull($attr));
                        
                        break;

                    case 'isNull':

                        $qb->andWhere($qb->expr()->isNotNull($attr));

                        break;

                    default:
                        break;
                }
                
                continue;
            }

            foreach ($condition as $type => $val) {

                switch ($type)
                {
                    case 'in':

                        $qb->andWhere($qb->expr()->in($attr, $this->fixConForRelEntity($val)));

                        break;

                    case 'notIn':

                        $qb->andWhere($qb->expr()->notIn($attr, $this->fixConForRelEntity($val)));

                        break;

                    case 'eq':

                        $qb->andWhere($qb->expr()->eq($attr, $this->fixConForRelEntity($val)));

                        break;

                    case 'like':
                        
                        $orx = $qb->expr()->orX();

                        foreach ($val as $each) {                           
                            $orx->add($qb->expr()->like($attr, $qb->expr()->literal('%' . $each . '%')));
                        }

                        $qb->andWhere($orx);

                        break;

                    case 'gte':

                        $orx = $qb->expr()->orX();

                        foreach ($val as $each) {
                            $orx->add($qb->expr()->gte($attr, $qb->expr()->literal($each . " 00:00:00")));
                        }

                        $qb->andWhere($orx);

                        break;

                    case 'lte':

                        $orx = $qb->expr()->orX();

                        foreach ($val as $each) {
                            $orx->add($qb->expr()->lte($attr, $qb->expr()->literal($each . " 23:59:59")));
                        }

                        $qb->andWhere($orx);

                        break;

                    default:
                        break;
                }
            }
        }

        return $qb;
    }

    /**
     * 若傳入的是關連的實體，修正其組成，取出其 id 鍵值重組新的陣列
     *
     * Example: 
     * 
     * array(
     *     array('id' => 2),
     *     array('id' => 11)
     * )
     *
     * will return array(2, 11)
     * 
     * @param  [array] $val
     * @return [array]  
     */
    protected function fixConForRelEntity($val)
    {
        // 關連實體傳過來會是陣列，我們只要他們的 id 放入 QueryBuilder
        // 故判斷為陣列時果斷取得其鍵名為id的值              
        if (!$val) {   
           throw new InvalidParameterException('Wrong format!!');        
        }

        // 若是字串或是數字且不是物件，則不需要做重組，直接返回$val
        if (isset($val[0]) && !is_array($val[0])) {
            return $val;
        }

        if (isset($val['id'])) {
            return $val['id'];
        }

        if (version_compare(phpversion(), '5.5.0', '>')) {
            // PHP 5.5+ 可用 array_column,server版本較低則使用array_map
            return array_column($val, 'id');
        } 
        
        return array_map(function ($element) { 
            return $element['id'];
        }, $val);        
    }

    /**
     * 屬性大寫字首轉為小寫 + .
     * 
     * @param  [string] $attr
     * @return [string]
     */
    protected function attrDecode($attr)
    {
        $upper = array('G', 'O', 'P', 'C', 'M', 'R', 'S', 'I');

        if (!in_array(substr($attr, 0, 1), $upper)) {
            return 'G' . $attr;
        }

        return strtolower(substr($attr, 0, 1)) . '.' . substr($attr, 1);
    }
}

