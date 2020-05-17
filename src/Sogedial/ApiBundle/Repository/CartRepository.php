<?php

namespace Sogedial\ApiBundle\Repository;

use Doctrine\ORM\QueryBuilder;
use Sogedial\ApiBundle\Entity\Cart;
use Sogedial\ApiBundle\Entity\ClientStatus;
use Sogedial\ApiBundle\Entity\Item;
use Sogedial\OAuthBundle\Entity\UserCommercial;
use Sogedial\OAuthBundle\Entity\UserCustomer;
use Sogedial\OAuthBundle\Entity\UserAdmin;


class CartRepository extends EntityRepository
{

    protected $alias = 'c';

    public function findBy(array $filter, array $orderBy = null, $limit = null, $offset = null)
    {

        $qb = $this->createFindByQueryBuilder($filter, $orderBy, $limit, $offset);

        ($this->tokenStorage->getToken()->getUser() instanceof UserCustomer) ? $this->getAcl($qb, $filter['platform']) : $this->getAcl($qb);

        $qb->groupBy($this->getAlias().'.id');

        return $qb->getQuery()->getResult();
    }

    public function getCount(array $filter)
    {
        $qb = $this->createCountQueryBuilder($filter);
        
        ($this->getTokenStorage()->getToken()->getUser() instanceof UserCustomer) ? $this->getAcl($qb, $filter['platform']) : $this->getAcl($qb);
       
        return $qb->getQuery()->getOneOrNullResult();
    }

    protected function getAcl(QueryBuilder $qb, $platform = null): void
    {
        $user = $this->tokenStorage->getToken()->getUser();
        $qb->join(UserCustomer::class, 'customer', 'WITH', $this->getAlias() . '.user = customer')
        ->leftJoin($this->getAlias().'.cartItems', 'cartItems', 'WITH', 'cartItems.cart = ' . $this->getAlias())
        ->join('cartItems.item', 'item', 'WITH', 'item = cartItems.item')
        ->andWhere('item.active = :active')
        ->setParameter('active', true);

        if ($user instanceof UserCustomer) {
            $client = $user->getClient();
            $clientStatus = $client->getClientStatusByPlatform($platform);
            $statusPreorder = $clientStatus->getStatusPreorder();
            $statusCatalog = $clientStatus->getStatusCatalog();
            $qb->andWhere('customer.client = :client')
                ->setParameter('client', $client)
                ->andWhere('customer = :user')
                ->setParameter('user', $user);

            if ($statusPreorder == ClientStatus::STATUS_PREORDER_BLOCKED) {
                $qb->andWhere($qb->expr()->notIn($this->getAlias().'.status', ':statusCart'))
                ->setParameter('statusCart', array(Cart::STATUS_CUSTOM_PREORDER, Cart::STATUS_CURRENT_PREORDER));
            }
            if ($statusCatalog == ClientStatus::STATUS_CATALOG_BLOCKED) {
                $qb->andWhere($qb->expr()->notIn($this->getAlias().'.status', ':statusCart'))
                    ->setParameter('statusCart', array(Cart::STATUS_CUSTOM, Cart::STATUS_CURRENT));
            }

        } elseif ($user instanceof UserCommercial) {
            $qb->join('customer.client', 'client')
                ->join('client.commercials', 'commercials')
                ->andWhere('commercials = :commercial')
                ->setParameter('commercial', $user);
        } elseif ($user instanceof UserAdmin) {
            $platform = $user->getPlatform();
            $qb->andWhere($this->getAlias().'.platform = :platform')
                ->setParameter('platform', $platform);
        }
    }

}
