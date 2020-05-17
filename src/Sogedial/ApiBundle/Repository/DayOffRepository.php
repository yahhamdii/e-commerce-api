<?php

namespace Sogedial\ApiBundle\Repository;

use Sogedial\OAuthBundle\Entity\UserCustomer;
use Sogedial\OAuthBundle\Entity\UserCommercial;
use Sogedial\OAuthBundle\Entity\UserAdmin;


class DayOffRepository extends EntityRepository
{
    protected $alias = 'do';

    public function findBy(array $filter, array $orderBy = null, $limit = null, $offset = null)
    {

        $qb = $this->createFindByQueryBuilder($filter, $orderBy, $limit, $offset);

        $this->getAcl($qb);
        
        return $qb->getQuery()->getResult();
    }

    public function getCount(array $filter)
    {
        $qb = $this->createCountQueryBuilder($filter);
        $this->getAcl($qb);

        return $qb->getQuery()->getOneOrNullResult();
    }

    protected function getAcl($qb): void
    {
        $user = $this->tokenStorage->getToken()->getUser();        

        if ($user instanceof UserCustomer) {                        
            $qb->andWhere( $this->getAlias() . '.platform IN (:platforms)' )
            ->setParameter('platforms', $user->getClient()->getPlatforms());            
        }elseif ($user instanceof UserCommercial || $user instanceof UserAdmin) {
            $qb->andWhere($this->getAlias() . '.platform = :platform ')
            ->setParameter('platform', $user->getPlatform());
        }
    }
}