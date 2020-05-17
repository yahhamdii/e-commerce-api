<?php

namespace Sogedial\ApiBundle\Repository;

use Doctrine\ORM\QueryBuilder;
use Sogedial\OAuthBundle\Entity\UserAdmin;
use Sogedial\OAuthBundle\Entity\UserCommercial;
use Sogedial\OAuthBundle\Entity\UserCustomer;


class InvoiceItemRepository extends EntityRepository
{
    protected $alias = 'ii';

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

        $this->getAcl($qb, $filter);

        return $qb->getQuery()->getResult();
    }

    public function getCount(array $filter)
    {
        $qb = $this->createCountQueryBuilder($filter);

        $this->getAcl($qb, $filter);

        return $qb->getQuery()->getOneOrNullResult();
    }

    private function getAcl(QueryBuilder $qb, array $filter)
    {

        $qb->join($this->getAlias().'.invoice','invoice');

        $qb->join('invoice.order', 'o');
        $user = $this->tokenStorage->getToken()->getUser();

        if ($user instanceof UserCustomer) {
            $qb->join('o.platform', 'platform', 'WITH', $qb->expr()->eq('platform', ':platformCustomer'));
            $qb->join('o.user', 'userCustomer', 'WITH', $qb->expr()->eq('userCustomer', ':customer'));
            $qb->setParameter('customer', $user);
            $qb->setParameter('platformCustomer', $filter['platform']);
        }
        if ($user instanceof UserAdmin) {
            $qb->join('o.platform', 'platform', 'WITH', $qb->expr()->eq('platform', ':platformAdmin'));
            $qb->setParameter('platformAdmin', $user->getPlatform());
        }
        if ($user instanceof UserCommercial) {
            $qb->join('o.platform', 'platform', 'WITH', $qb->expr()->eq('platform', ':platformCommercial'))
                ->setParameter('platformCommercial', $user->getPlatform());
            $qb->join('o.user', 'userSuper');
            $qb->join(UserCustomer::class, 'userCustomer', 'WITH', $qb->expr()->eq('userSuper', 'userCustomer'));
            $qb->join('invoice.client', 'client');
            $qb->join('client.commercials', 'commercial', 'WITH', $qb->expr()->eq('commercial', ':userCommercial'))
                ->setParameter('userCommercial', $user);
        }

    }

    protected function processCriteria($qb, $filter)
    {

        $sanitizedFilter = $filter;

        unset($sanitizedFilter['platform']);

        parent::processCriteria($qb, $sanitizedFilter);
    }
}
