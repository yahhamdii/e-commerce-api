<?php

namespace Sogedial\ApiBundle\Repository;


class PricingRepository extends EntityRepository
{
    protected $alias = 'pricing';

    public function findBy(array $filter, array $orderBy = null, $limit = null, $offset = null)
    {
        $qb = $this->createFindByQueryBuilder($filter, $orderBy, $limit, $offset);

        return $qb->getQuery()->getResult();
    }

    protected function createFindByQueryBuilder(array $filter, array $orderBy = null, $limit = null, $offset = null)
    {
        $qb = parent::createFindByQueryBuilder($filter, $orderBy, $limit, $offset);

        return $qb;
    }


    public function getCount(array $filter)
    {
        $qb = $this->createCountQueryBuilder($filter);

        return $qb->getQuery()->getOneOrNullResult();
    }


    protected function createCountQueryBuilder(array $filter)
    {
        $qb = parent::createCountQueryBuilder($filter);

        return $qb;
    }


}
