<?php

namespace Sogedial\ApiBundle\Repository;

use Doctrine\ORM\QueryBuilder;
use Sogedial\OAuthBundle\Entity\UserAdmin;
use Sogedial\OAuthBundle\Entity\UserCommercial;
use Sogedial\OAuthBundle\Entity\UserCustomer;

class OrderItemRepository extends EntityRepository
{
    protected $alias = 'oi';

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

        $this->getAcl($qb);

        return $qb->getQuery()->getResult();
    }

    public function getCount(array $filter)
    {
        $qb = $this->createCountQueryBuilder($filter);

        $this->getAcl($qb);

        return $qb->getQuery()->getOneOrNullResult();
    }

    private function getAcl(QueryBuilder $qb)
    {

        $qb->join($this->getAlias().'.order','o');

        $user = $this->tokenStorage->getToken()->getUser();
        if ($user instanceof UserAdmin) {
            $qb->andWhere('o.platform = :platform')
                ->setParameter('platform', $user->getPlatform());
       }
        if($user instanceof UserCommercial){
            $qb->join('o.user','user');
            $qb->join(UserCustomer::class,'userCustomer', 'WITH', $qb->expr()->eq('userCustomer','user'));
            $qb->join('userCustomer.client','client');
            $qb->join('client.commercials','commercials','WITH', $qb->expr()->eq('commercials',':commercial'))
                ->setParameter('commercial',$user);
        }
        if($user instanceof UserCustomer){
            $qb->join('o.user','user','WITH',$qb->expr()->eq('user',':user'))
            ->setParameter('user',$user);
        }

    }

}
