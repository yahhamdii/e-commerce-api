<?php

namespace Sogedial\ApiBundle\Repository;

use Doctrine\ORM\QueryBuilder;
use Sogedial\OAuthBundle\Entity\UserAdmin;
use Sogedial\OAuthBundle\Entity\UserCustomer;


class MessageRepository extends EntityRepository
{
    protected $alias = 'm';

    public function findBy(array $filter, array $orderBy = null, $limit = null, $offset = null)
    {
        $qb = parent::createFindByQueryBuilder($filter, $orderBy, $limit, $offset);

        $this->getAcl($qb);

        return $qb->getQuery()->getResult();

    }

    private function getAcl(QueryBuilder $qb)
    {
        $user = $this->getTokenStorage()->getToken()->getUser();
        $date = new \Datetime('now');
        if ($user instanceof UserCustomer) {
            $qb->andWhere($qb->expr()->gte(':date',$this->getAlias() . '.dateBegin'))
                ->andWhere($qb->expr()->lte(':date',$this->getAlias() . '.dateEnd'))
            ->setParameter('date', $date);
        }

        if($user instanceof UserAdmin){
            $qb->andWhere($qb->expr()->eq($this->alias.'.platform', ':platform'))
                ->setParameter('platform', $user->getPlatform());
        }
    }
}
