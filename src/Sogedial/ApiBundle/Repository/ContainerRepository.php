<?php

namespace Sogedial\ApiBundle\Repository;

use Doctrine\ORM\QueryBuilder;
use Sogedial\ApiBundle\Helper\RepositoryHelper;
use Sogedial\OAuthBundle\Entity\UserAdmin;
use Sogedial\OAuthBundle\Entity\UserCommercial;
use Sogedial\OAuthBundle\Entity\UserCustomer;

class ContainerRepository extends EntityRepository
{
    protected $alias = 'c';


    public function findBy(array $filter, array $orderBy = null, $limit = null, $offset = null)
    {
        $qb = $this->createFindByQueryBuilder($filter, $orderBy, $limit, $offset);

        return $qb->getQuery()->getResult();
    }


    public function getCount(array $filter)
    {
        $qb = $this->createCountQueryBuilder($filter);

        return $qb->getQuery()->getOneOrNullResult();
    }

    private function getAcl(QueryBuilder $qb)
    {
        $qb->join($this->getAlias() . '.containerCartItems', 'containerCartItems')
            ->join('containerCartItems.cartItem', 'cartItem')
            ->join('cartItem.cart', 'cart');

        $user = $this->tokenStorage->getToken()->getUser();
        if ($user instanceof UserAdmin) {
            $qb->andWhere('cart.platform = :platform')
                ->setParameter('platform', $user->getPlatform());
        }
        if ($user instanceof UserCommercial) {
            $qb->join('cart.user', 'user');
            $qb->join(UserCustomer::class, 'userCustomer', 'WITH', $qb->expr()->eq('userCustomer', 'user'));
            $qb->join('userCustomer.client', 'client');
            $qb->join('client.commercials', 'commercials', 'WITH', $qb->expr()->eq('commercials', ':commercial'))
                ->setParameter('commercial', $user);
        }
        if ($user instanceof UserCustomer) {
            $qb->join('cart.user', 'user', 'WITH', $qb->expr()->eq('user', ':user'))
                ->setParameter('user', $user);
        }
    }

    protected function createFindByQueryBuilder(array $filter, array $orderBy = null, $limit = null, $offset = null)
    {
        $qb = parent::createFindByQueryBuilder($filter, $orderBy, $limit, $offset);
        $this->getAcl($qb);
        $this->addSpecificQuery($qb, $filter);

        return $qb;

    }

    protected function createCountQueryBuilder(array $filter)
    {
        $qb = parent::createCountQueryBuilder($filter);
        $this->getAcl($qb);
        $this->addSpecificQuery($qb, $filter);

        return $qb;
    }

    protected function processCriteria($qb, $filter)
    {
        $sanitizedFilter = $filter;
        unset($sanitizedFilter['cart']);

        RepositoryHelper::processCriteria($qb, $sanitizedFilter);
    }


    protected function addSpecificQuery(QueryBuilder $qb, array $filter)
    {
        if (isset($filter['cart'])) {
            //addSpecificQuery est appelé apres getAcl qui contient deja les jointure necessaire, alors fait attention s il ya du deplacement
            $qb->andWhere($qb->expr()->eq('cart.id', ':idCart'))
                ->setParameter('idCart', $filter['cart']);
        }
    }

}
