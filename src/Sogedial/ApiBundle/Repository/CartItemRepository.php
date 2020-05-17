<?php

namespace Sogedial\ApiBundle\Repository;

use Doctrine\ORM\QueryBuilder;
use Sogedial\OAuthBundle\Entity\UserAdmin;
use Sogedial\OAuthBundle\Entity\UserCommercial;
use Sogedial\OAuthBundle\Entity\UserCustomer;
use Sogedial\ApiBundle\Entity\Cart;
use Sogedial\ApiBundle\Entity\Item;

class CartItemRepository extends EntityRepository
{
    protected $alias = 'ci';

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

        //add join to orderBy category
        $qb->leftJoin('item.categories', 'categories');

        return $qb->getQuery()->getResult();
    }

    public function getCount(array $filter)
    {
        $qb = $this->createCountQueryBuilder($filter);

        $this->getAcl($qb, $filter);

        return $qb->getQuery()->getOneOrNullResult();
    }

    private function getAcl(QueryBuilder $qb, $filter)
    {
        $qb->join($this->getAlias() . '.item', 'item');
        $qb->join($this->getAlias().'.cart','cart');

        if (PHP_SAPI != 'cli') {
            $qb->andWhere('item.active = :active')
                ->setParameter('active', true);           
        }

        /* /!\ need to get cart Status to finish */
        /* Check if the item is always on the good cart preorder vs not */
        /*$arrayStatusPreorder = array(
            Cart::STATUS_CUSTOM_PREORDER, 
            Cart::STATUS_CURRENT_PREORDER,
            Cart::STATUS_MOQ_PREORDER,
            Cart::STATUS_STAND_BY_VALIDATION_PREORDER
        );
        $arrayStatusOrder = array( Cart::STATUS_CUSTOM, Cart::STATUS_CURRENT);                

        if( in_array( $cart['status'], $arrayStatusOrder)
            || in_array( $cart['status'], $arrayStatusPreorder) ){

            if(in_array( $cart['status'], $arrayStatusOrder) ){
                $qb->andWhere(                
                    $qb->expr()->orX(
                        $qb->expr()->neq('item.codeNature', ':codeNature'),
                        $qb->expr()->neq('item.frequency', ':frequency')
                    )
                );
            }
            if( in_array( $cart['status'], $arrayStatusPreorder) ){
                $qb->andWhere(                
                    $qb->expr()->orX(
                        $qb->expr()->eq('item.codeNature', ':codeNature'),
                        $qb->expr()->eq('item.frequency', ':frequency')
                    )
                );
            }
            $qb->setParameter('codeNature', Item::PRECMD_CODE_NATURE)
            ->setParameter('frequency', ord(Item::PRECMD_FREQUENCY));
        }*/
        
        $user = $this->tokenStorage->getToken()->getUser();

        if ($user instanceof UserAdmin) {
            $qb->andWhere('cart.platform = :platform')
                ->setParameter('platform', $user->getPlatform());
        }
        if($user instanceof UserCommercial){
            $qb->join('cart.user','user');
            $qb->join(UserCustomer::class,'userCustomer', 'WITH', $qb->expr()->eq('userCustomer','user'));
            $qb->join('userCustomer.client','client');
            $qb->join('client.commercials','commercials','WITH', $qb->expr()->eq('commercials',':commercial'))
                ->setParameter('commercial',$user);
        }
        if($user instanceof UserCustomer){
            $qb->join('cart.user','user','WITH',$qb->expr()->eq('user',':user'))
            ->setParameter('user',$user);
        }
    }

}
