<?php

namespace Sogedial\ApiBundle\Repository;

use Sogedial\OAuthBundle\Entity\UserCustomer;
use Sogedial\OAuthBundle\Entity\UserCommercial;
use Sogedial\OAuthBundle\Entity\UserAdmin;


class DeliveryPlanningRepository extends EntityRepository
{
    protected $alias = 'dp';

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
                        
           /* $qb->andWhere( $this->getAlias() . '.platform IN (:platforms)' )
            ->setParameter('platforms', $user->getClient()->getPlatforms());*/

            $qb->leftJoin($this->getAlias() . '.children', 'children');       
            //$qb->select($this->getAlias().',children.id' );
            
            $orX = $qb->expr()->orX(
                $qb->expr()->isNull( 'children' ),
                $qb->expr()->andX(
                    $qb->expr()->isNotNull( $this->getAlias() . '.parent' ),
                    $qb->expr()->lte( $this->getAlias() . '.dateBegin', ':now' ),
                    $qb->expr()->orX(
                        $qb->expr()->gte( $this->getAlias() . '.dateEnd', ':now' ),
                        $qb->expr()->isNull( $this->getAlias() . '.dateEnd')
                    )
                )                
            );

            /*,
                $qb->expr()->andX(
                    $qb->expr()->isNotNull( 'children' ),
                    $qb->expr()->orX(
                        $qb->expr()->gte( 'children.dateBegin', ':now' ),
                        $qb->expr()->orX(
                            $qb->expr()->gte( 'children.dateEnd', ':now' ),
                            $qb->expr()->isNull( $this->getAlias() . '.dateEnd')
                        )
                    )
                )*/

            $now = new \DateTime();
           
            $qb->andWhere($orX)
            ->setParameter('now', $now->format(\DateTime::ATOM));
            
        }elseif ($user instanceof UserCommercial || $user instanceof UserAdmin) {            
            $qb->andWhere($this->getAlias() . '.platform = :platform ')
            ->setParameter('platform', $user->getPlatform());
        }
    }
}