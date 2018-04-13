<?php

namespace App\Repository;

use Doctrine\ORM\EntityRepository;

class ListOrderedRepository extends EntityRepository
{

    public function createQueryBuilder($alias, $indexBy = null)
    {
        $qb = parent::createQueryBuilder($alias, $indexBy);
        return $qb->orderBy($alias.'.order', 'ASC');
    }

    public function findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
    {
        if (is_null($orderBy)) {
            $orderBy = array('order' => 'ASC');
        }

        return parent::findBy($criteria, $orderBy, $limit, $offset);
    }
}