<?php

namespace Sogedial\ApiBundle\Repository;

use Doctrine\ORM\Query\Expr;
use Doctrine\ORM\QueryBuilder;
use Sogedial\ApiBundle\Entity\ClientStatus;
use Sogedial\OAuthBundle\Entity\UserAdmin;
use Sogedial\OAuthBundle\Entity\UserCommercial;
use Sogedial\OAuthBundle\Entity\UserCustomer;

class PlatformRepository extends EntityRepository {

    protected $alias = 'p';

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

    protected function getAcl(QueryBuilder $qb): void
    {
        $user = $this->tokenStorage->getToken()->getUser();

        if ($user instanceof UserCustomer) {
            $qb->join($this->getAlias() . '.clients', 'clients')
                ->join('clients.customers', 'customers')
                ->andWhere('customers = :idCustomer')
                ->setParameter('idCustomer', $user->getId());

            $qb->join($this->getAlias() . '.clientStatus', 'clientStatus', Expr\Join::WITH,
                $qb->expr()->andX($qb->expr()->in('clientStatus.status', ':status'),
                    $qb->expr()->orX($qb->expr()->in('clientStatus.statusPreorder', ':status'), $qb->expr()->in('clientStatus.statusCatalog', ':status')),
                    $qb->expr()->eq('clientStatus.client', ':client')))
                ->setParameter('status', array(ClientStatus::STATUS_ACTIVE, ClientStatus::STATUS_INACTIVE))
                ->setParameter('client', $user->getClient());

        } elseif ($user instanceof UserCommercial) {
            $qb->join(UserCommercial::class, 'commercial', \Doctrine\ORM\Query\Expr\Join::WITH, 'commercial.platform = ' . $this->alias);
            $qb->andWhere('commercial = :idCommercial')
                ->setParameter('idCommercial', $user->getId());
        } elseif ($user instanceof UserAdmin) {
            $qb->andWhere($this->alias . '.id = :platformAdmin')
                ->setParameter('platformAdmin', $user->getPlatform());
        }
    }

}
