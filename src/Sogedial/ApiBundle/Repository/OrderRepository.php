<?php

namespace Sogedial\ApiBundle\Repository;

use Doctrine\ORM\QueryBuilder;
use Sogedial\ApiBundle\Entity\ClientStatus;
use Sogedial\OAuthBundle\Entity\UserAdmin;
use Sogedial\OAuthBundle\Entity\UserCommercial;
use Sogedial\OAuthBundle\Entity\UserCustomer;

class OrderRepository extends EntityRepository
{

    protected $alias = 'o';

    /**
     * @param $idOrder
     * @return mixed
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function getOrderWithItemsOrderItemsPlatform($idOrder)
    {
        $qb = $this->createQueryBuilder($this->alias);
        $qb->andWhere($this->alias . '.id = :idOrder')
            ->setParameter('idOrder', $idOrder)
            ->leftJoin($this->alias . '.platform', 'platform', 'WITH', 'platform =' . $this->alias . '.platform')
            ->addSelect('platform')
            ->leftJoin($this->alias . '.orderItems', 'orderItems', 'WITH', 'orderItems.order = ' . $this->alias)
            ->addSelect('orderItems')
            ->join('orderItems.item', 'item')
            ->addSelect('item');

        $qb->andWhere('item.active = :active')
            ->setParameter('active', true);

        return $qb->getQuery()->getOneOrNullResult();
    }

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
        $qb->join($this->getAlias() . '.status', 'status');
        if (PHP_SAPI != 'cli') {
            ($this->tokenStorage->getToken()->getUser() instanceof UserCustomer) ? $this->getAcl($qb, $filter['platform']) : $this->getAcl($qb);

        }

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
        $qb->join($this->getAlias() . '.status', 'status');
        ($this->tokenStorage->getToken()->getUser() instanceof UserCustomer) ? $this->getAcl($qb, $filter['platform']) : $this->getAcl($qb);

        return $qb->getQuery()->getOneOrNullResult();
    }

    /**
     * @param $qb
     */
    protected function getAcl(QueryBuilder $qb, $platform = null): void
    {
        $user = $this->getTokenStorage()->getToken()->getUser();

        if (!($user instanceof UserAdmin)) {
            $qb->join(UserCustomer::class, 'customer', 'WITH', $this->alias . '.user = customer');

            if ($user instanceof UserCustomer) {
                $client = $user->getClient();
                $clientStatus = $client->getClientStatusByPlatform($platform);
                $statusPreorder = $clientStatus->getStatusPreorder();
                $statusCatalog = $clientStatus->getStatusCatalog();

                $qb->andWhere('customer.client = :client')
                    ->setParameter('client', $user->getClient());
                $qb->andWhere('customer = :user')
                    ->setParameter('user', $user);

                if($statusPreorder == ClientStatus::STATUS_PREORDER_BLOCKED){
                    $qb->andWhere($qb->expr()->not($qb->expr()->eq($this->getAlias().'.isPreorder',':statusPreorder')))
                    ->setParameter('statusPreorder', true);
                }

                if($statusCatalog == ClientStatus::STATUS_CATALOG_BLOCKED){
                    $qb->andWhere($qb->expr()->not($qb->expr()->eq($this->getAlias().'.isPreorder',':statusPermanent')))
                        ->setParameter('statusPermanent', false);
                }

            } elseif ($user instanceof UserCommercial) {
                $qb->join('customer.client', 'client')
                    ->join('client.commercials', 'commercials')
                    ->andWhere('commercials = :commercial')
                    ->setParameter('commercial', $user);
            }
        }
    }
}
