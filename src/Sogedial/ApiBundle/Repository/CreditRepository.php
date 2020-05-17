<?php

namespace Sogedial\ApiBundle\Repository;


use Doctrine\ORM\QueryBuilder;
use Sogedial\ApiBundle\Helper\RepositoryHelper;
use Sogedial\OAuthBundle\Entity\UserAdmin;
use Sogedial\OAuthBundle\Entity\UserCommercial;
use Sogedial\OAuthBundle\Entity\UserCustomer;

class CreditRepository extends EntityRepository
{

    protected $alias = "credit";

    public function findBy(array $filter, array $orderBy = null, $limit = null, $offset = null)
    {
        $qb = $this->createFindByQueryBuilder($filter, $orderBy, $limit, $offset);

        return $qb->getQuery()->getResult();
    }

    protected function createFindByQueryBuilder(array $filter, array $orderBy = null, $limit = null, $offset = null)
    {
        $qb = parent::createFindByQueryBuilder($filter, $orderBy, $limit, $offset);
        $this->getAcl($qb, $filter);
        $this->addSpecificQuery($qb, $filter);

        return $qb;
    }

    protected function addSpecificQuery(QueryBuilder $qb, array $filter)
    {
        if (isset($filter['order'])) {
            //addSpecificQuery est appelé apres getAcl qui contient deja les jointure necessaire, alors fait attention s il ya du deplacement
            $qb->andWhere($qb->expr()->eq('o.id', ':idOrder'))
                ->setParameter('idOrder', $filter['order']);
        }
        if (isset($filter['invoice'])) {
            $qb->andWhere($qb->expr()->eq('invoice.id', ':idInvoice'))
                ->setParameter('idInvoice', $filter['invoice']);
        }
    }

    protected function processCriteria($qb, $filter)
    {
        $sanitizedFilter = $filter;
        unset($sanitizedFilter['platform']);
        unset($sanitizedFilter['order']);
        unset($sanitizedFilter['invoice']);

        RepositoryHelper::processCriteria($qb, $sanitizedFilter);
    }

    private function getAcl(QueryBuilder $qb, array $filter)
    {
        $user = $this->tokenStorage->getToken()->getUser();
        $qb->join($this->getAlias() . '.invoiceItem', 'invoiceItem');
        $qb->join('invoiceItem.invoice', 'invoice');
        $qb->join('invoice.order', 'o');

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
            $qb->join('userCustomer.client', 'client');
            $qb->join('client.commercials', 'commercial', 'WITH', $qb->expr()->eq('commercial', ':userCommercial'))
                ->setParameter('userCommercial', $user);
        }
    }

    public function getCount(array $filter)
    {
        $qb = $this->createCountQueryBuilder($filter);

        return $qb->getQuery()->getOneOrNullResult();
    }


    protected function createCountQueryBuilder(array $filter)
    {
        $qb = parent::createCountQueryBuilder($filter);
        $this->getAcl($qb, $filter);
        $this->addSpecificQuery($qb, $filter);

        return $qb;
    }

}
