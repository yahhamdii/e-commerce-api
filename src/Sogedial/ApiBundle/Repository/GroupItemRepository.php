<?php

namespace Sogedial\ApiBundle\Repository;

use Sogedial\OAuthBundle\Entity\UserAdmin;
use Sogedial\OAuthBundle\Entity\UserCommercial;
use Sogedial\OAuthBundle\Entity\UserCustomer;

class GroupItemRepository extends EntityRepository {

    protected $alias = 'gi';

    public function getByBrand($brand, $status = null)
    {
        $user = $this->tokenStorage->getToken()->getUser();

        if ($user instanceof UserCommercial || $user instanceof UserAdmin) {

            $platform = $user->getPlatform();

            $qb = $this->createQueryBuilder($this->getAlias())            
            ->innerJoin( $this->getAlias().'.items','items')
            ->innerJoin($this->getAlias().'.groupClients', 'groupClients')
            ->where('groupClients.brand = :brand')
            ->andWhere($this->getAlias().'.platform = :platform')
            ->andWhere('items.active = 1')
            ->setParameter('platform',  $platform)
            ->setParameter('brand', $brand);

            if($status !== null && $status !== ""){
                $qb->andWhere($this->getAlias().'.status = :status')
                ->setParameter('status',  $status);
            }

            return $qb->getQuery()->getResult();

        }
        
        return null;
    }
}
