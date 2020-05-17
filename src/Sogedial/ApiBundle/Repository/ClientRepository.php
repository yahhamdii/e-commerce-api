<?php

namespace Sogedial\ApiBundle\Repository;

use Doctrine\ORM\QueryBuilder;
use Sogedial\OAuthBundle\Entity\UserAdmin;
use Sogedial\OAuthBundle\Entity\UserCommercial;
use Sogedial\OAuthBundle\Entity\UserCustomer;

class ClientRepository extends EntityRepository {

    protected $alias = 'cl';

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

    public function getClientSuggestion($q, array $filter, array $orderBy = null, $limit = null, $offset = null)
    {
        $qb = $this->createFindByQueryBuilder($filter, $orderBy, $limit, $offset);

        $this->addSuggestionQuery($q, $qb);

        return $qb->getQuery()->getResult();
    }

    public function getCountClientSuggestion($q, array $filter) {

        $qb = $this->createCountQueryBuilder($filter);

        $this->addSuggestionQuery($q, $qb);

        return $qb->getQuery()->getOneOrNullResult();
    }

    protected function createFindByQueryBuilder(array $filter, array $orderBy = null, $limit = null, $offset = null)
    {
        $qb =  parent::createFindByQueryBuilder($filter, $orderBy, $limit, $offset);
        $this->getAcl($qb);

        $qb->distinct();

        $this->addSpecifiqueQuery($qb, $filter);
        
        return $qb;
    }

    protected function createCountQueryBuilder(array $filter)
    {
        $qb = parent::createCountQueryBuilder($filter);
        $this->getAcl($qb);
        $this->addSpecifiqueQuery($qb, $filter);

        return $qb;
    }


    protected function getAcl(QueryBuilder $qb): void
    {
        $user = $this->tokenStorage->getToken()->getUser();
        
        if ($user instanceof UserCustomer) {

            $qb->join($this->getAlias() . '.customers', 'customers')
                ->andWhere('customers.id = :idCustomer')
                ->setParameter('idCustomer', $user->getId());

        } elseif ($user instanceof UserAdmin || $user instanceof UserCommercial) {

            $qb->leftJoin($this->getAlias() . '.clientStatus', 'clientStatus', 'WITH', $qb->expr()->eq('clientStatus.platform', ':platform'))
                ->setParameter('platform', $user->getPlatform());

            $qb->leftJoin($this->getAlias() . '.groupClients', 'groupClients', 'WITH', $qb->expr()->eq('groupClients.id', ':platformUser'))
              ->setParameter('platformUser', $user->getPlatform());

            $qb->leftJoin($this->getAlias() . '.customers', 'customers');

            if ($user instanceof UserCommercial) {
                $qb->join($this->getAlias() . '.commercials', 'commercials')
                    ->andWhere('commercials.id = :idCommercial')
                    ->setParameter('idCommercial', $user->getId());

            } elseif ($user instanceof UserAdmin) {
                $qb->join($this->alias . '.platforms', 'platform', 'WITH', $qb->expr()->eq('platform', ':adminPlatform'))
                    ->setParameter('adminPlatform', $user->getPlatform()->getId());
            }
        }
    }

    protected function addSuggestionQuery($q, QueryBuilder $qb)
    {
        $qb->andWhere('REGEXP(cl.name, :regexp) = true OR REGEXP(customers.lastname, :regexp) = true OR REGEXP(customers.firstname, :regexp) = true
            OR REGEXP(groupClients.name, :regexp) = true OR REGEXP(groupClients.label, :regexp) = true OR REGEXP(cl.extCode, :regexp) = true')
            ->setParameter('regexp', $this->createRegexp($q));

        return $qb;
    }

    protected function addSpecifiqueQuery(QueryBuilder $qb, array $filter)
    {
        if(isset($filter['preorder']) || isset($filter['permanent']) || isset($filter['clientStatus'])){

            if(!in_array('clientStatus',$qb->getAllAliases())){

                $qb->join($this->alias . '.clientStatus', 'clientStatus');
            }
            if (isset($filter['preorder'])) {

                $qb->andWhere($qb->expr()->in('clientStatus.statusPreorder',':StatusPreorder'))
                    ->setParameter('StatusPreorder', $filter['preorder']);
            }

            if (isset($filter['permanent'])) {

                $qb->andWhere($qb->expr()->in('clientStatus.statusCatalog',':StatusPermanent'))
                    ->setParameter('StatusPermanent', $filter['permanent']);
            }

            if(isset($filter['clientStatus'])){
                if(in_array('INACTIVE', $filter['clientStatus'])){
                    $qb->andWhere(
                        $qb->expr()->orX(
                            $qb->expr()->in('clientStatus.status', ':clientStatusPlatform'),
                            $qb->expr()->isNull('clientStatus'),
                            $qb->expr()->eq($this->getAlias().'.status', ':inactiveStatus')                                                
                        )                        
                    );
                    $qb->setParameter('inactiveStatus', 'INACTIVE');                 
                }else{
                    $qb->andWhere($qb->expr()->in('clientStatus.status', ':clientStatusPlatform'));
                }
                $qb->setParameter('clientStatusPlatform', $filter['clientStatus']);
            }
        }

        return $qb;
    }

    protected function processCriteria($qb, $filter)
    {
        $sanitizedFilter = $filter;
        unset($sanitizedFilter['preorder']);
        unset($sanitizedFilter['permanent']);
        unset($sanitizedFilter['clientStatus']);

        parent::processCriteria($qb, $sanitizedFilter);
    }

}
