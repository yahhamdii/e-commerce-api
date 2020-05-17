<?php

namespace Sogedial\ApiBundle\Repository;

use Sogedial\OAuthBundle\Entity\UserCustomer;
use Sogedial\OAuthBundle\Entity\UserCommercial;

class ProductRepository extends EntityRepository
{

    protected $alias = 'pr';

    /**
     * @param array $filter
     * @param array|null $orderBy
     * @param null $limit
     * @param null $offset
     * @return array|mixed
     */
    public function findBy(array $filter, array $orderBy = null, $limit = null, $offset = null)
    {
        $qb = $this->createFindByQueryBuilder($filter, $orderBy, $limit, $offset);

        return $qb->getQuery()->getResult();
    }

    /**
     * @param array $filter
     * @return mixed
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function getCount(array $filter)
    {
        $qb = $this->createCountQueryBuilder($filter);
        
        return $qb->getQuery()->getOneOrNullResult();

    }

}
