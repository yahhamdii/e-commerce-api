<?php

namespace Sogedial\ApiBundle\Repository;

use Doctrine\ORM\Query\Expr;
use Doctrine\ORM\QueryBuilder;
use Sogedial\ApiBundle\Entity\Attribut;
use Sogedial\ApiBundle\Entity\ClientStatus;
use Sogedial\ApiBundle\Entity\GroupItem;
use Sogedial\ApiBundle\Entity\Item;
use Sogedial\ApiBundle\Entity\Promotion;
use Sogedial\ApiBundle\Entity\Category;
use Sogedial\ApiBundle\Exception\BadRequestException;
use Sogedial\ApiBundle\Exception\ForbiddenException;
use Sogedial\OAuthBundle\Entity\UserAdmin;
use Sogedial\OAuthBundle\Entity\UserCommercial;
use Sogedial\OAuthBundle\Entity\UserCustomer;

class ItemRepository extends EntityRepository
{

    protected $alias = 'i';

    /**
     * @param $qb
     * @param $filter
     */
    protected function processCriteria($qb, $filter)
    {

        $sanitizedFilter = $filter;

        if (isset($filter["categories.slug"]) && !is_null($filter["categories.slug"])) {

            $ids =  $this->getRepositoryInjected(Category::class)->getChildrenIdsFromSlug( $filter["categories.slug"] );
            $sanitizedFilter["categories.id"] = $ids;
            
        }


        unset($sanitizedFilter['is_new']);
        unset($sanitizedFilter['has_promotion']);
        unset($sanitizedFilter['is_ordered']);
        unset($sanitizedFilter['groupItem']);
        unset($sanitizedFilter['manufacturer']);
        unset($sanitizedFilter['stock']);
        unset($sanitizedFilter['selection']);         
        unset($sanitizedFilter["categories.slug"]);

        parent::processCriteria($qb, $sanitizedFilter);
    }

    /**
     * @param array $filter
     * @param array|null $orderBy
     * @param null $limit
     * @param null $offset
     * @param bool $showManufacturers
     * @param bool $limitManufacturers
     * @return array|mixed|Item[]
     */
    public function findBy(array $filter, array $orderBy = null, $limit = null, $offset = null, $showManufacturers = false, $limitManufacturers = true)
    {

        $qb = $this->createFindByQueryBuilder($filter, $orderBy, $limit, $offset);

        (isset($filter['platform'])) ? $this->getAcl($qb, $filter['platform']) : $this->getAcl($qb);

        $itemResults = $qb->getQuery()->getResult();
        $showManufacturers = filter_var($showManufacturers, FILTER_VALIDATE_BOOLEAN);

        if (!$showManufacturers) {
            return $itemResults;
        }

        return $this->showManufacturers($qb, $itemResults, $limitManufacturers);
    }

    /**
     * @param array $filter
     * @param array|null $orderBy
     * @param null $limit
     * @param null $offset
     * @return QueryBuilder
     */
    protected function createFindByQueryBuilder(array $filter, array $orderBy = null, $limit = null, $offset = null)
    {

        $qb = parent::createFindByQueryBuilder($filter, $orderBy, $limit, $offset);

        $qb->distinct();

        $this->addSpecificQuery($filter, $qb);

        return $qb;
    }

    /**
     * @param array $filter
     * @return QueryBuilder
     */
    protected function createCountQueryBuilder(array $filter)
    {   
        $qb = parent::createCountQueryBuilder($filter);

        $this->addSpecificQuery($filter, $qb);

        return $qb;
    }

    /**
     * @param $q
     * @param array $filter
     * @param array|null $orderBy
     * @param null $limit
     * @param null $offset
     * @param bool $showManufacturers
     * @param bool $limitManufacturers
     * @return array|mixed
     */
    public function getItemSuggestion($q, array $filter, array $orderBy = null, $limit = null, $offset = null, $showManufacturers = true, $limitManufacturers = true)
    {
        $qb = $this->createFindByQueryBuilder($filter, $orderBy, $limit, $offset);

        $this->addSuggestionQuery($q, $qb, $filter);

        $itemResults = $qb->getQuery()->getResult();
        $showManufacturers = filter_var($showManufacturers, FILTER_VALIDATE_BOOLEAN);
        if (!$showManufacturers)
            return $itemResults;

        return $this->showManufacturers($qb, $itemResults, $limitManufacturers);
    }

    /**
     * @param $q
     * @param array $filter
     * @return mixed
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function getCountItemSuggestion($q, array $filter)
    {
        $qb = $this->createCountQueryBuilder($filter);

        $this->addSuggestionQuery($q, $qb, $filter);

        return $qb->getQuery()->getOneOrNullResult();
    }

    /**
     * @param $q
     * @param $qb
     * @return mixed
     */
    protected function addSuggestionQuery($q, $qb, $filter)
    {
        /** @var QueryBuilder $qb */
        (isset($filter['platform'])) ? $this->getAcl($qb, $filter['platform']) : $this->getAcl($qb);

        if (!in_array('manufacturer', $qb->getAllAliases())) {
            $qb->leftjoin('p.manufacturer', 'manufacturer');
        }

        foreach (explode(" ", $q) as $i => $key) {

            $qb->andWhere($qb->expr()->orX(
                    $qb->expr()->like($this->alias . ".name", ":regexp" . $i),
                    $qb->expr()->like($this->alias . ".ean13", ":regexp" . $i),
                    $qb->expr()->like($this->alias . ".reference", ":regexp" . $i),
                    $qb->expr()->like("manufacturer.name", ":regexp" . $i)
                ))
                ->setParameter("regexp" . $i, "%" . $key . "%")
            ;
        }

        return $qb;
    }

    /**
     * @param $filter
     * @param $qb
     * @return mixed
     */
    protected function addSpecificQuery($filter, QueryBuilder $qb)
    {
        $qb->join($this->getAlias() . '.categories', 'categories');


        if (isset($filter['is_new']) && $filter['is_new'] == true) {

            if (!in_array('stock', $qb->getAllAliases())) {
                $qb->join($this->getAlias() . '.stock', 'stock');
            }

            $limit = 90;

            if (isset($filter['platform'])) {
                $platform = $this->findPlatform($filter['platform']);
                if ($platform) {
                    $attribut = $platform->getAttributByKey(Attribut::KEY_NEW);
                    if ($attribut) {
                        if ($attribut->getValue() == 0)
                            $limit = 90;
                        if ($attribut->getValue() == 1)
                            $limit = 60;
                    }
                }
            }

            $dateEntryStockLimit = date("Y-m-d H:i:s", time() - 60 * 60 * 24 * $limit);
            $currentDate= date("Y-m-d H:i:s");
            $qb->andWhere($qb->expr()->orX($qb->expr()->between('stock.firstDateEntryInStock', ':dateEntryStockLimit', ':currentDate'), $qb->expr()->isNull('stock.firstDateEntryInStock')));
            $qb->setParameter('dateEntryStockLimit', $dateEntryStockLimit)
                ->setParameter('currentDate', $currentDate);
        }

        if (isset($filter['has_promotion'])) {
            
            $qb->innerJoin($this->getAlias() . '.promotions', 'promotions');
            $qb->andWhere('promotions.dateStartValidity <= :now');
            $qb->andWhere('promotions.dateEndValidity > :now');
            $qb->andWhere('promotions.displayAsPromotion =  :displayAsPromotion');
            $qb->andWhere('promotions.promoCode IN (:promoCodes)');

            $qb->setParameter('now', date("Y-m-d H:i:s", time()));
            $qb->setParameter('displayAsPromotion', true);
            $qb->setParameter('promoCodes', Promotion::CODES_PRIORITY);            

             if ($this->tokenStorage) {
                $user = $this->tokenStorage->getToken()->getUser();
                $client = $user->getClient();
                
                if (isset($filter['platform'])) {
                    $platform = $this->findPlatform($filter['platform']);
                    $userBrand = $client->getBrandByPlatform($platform);
                    if($userBrand){
                        $qb->andWhere(
                            $qb->expr()->orX(
                                $qb->expr()->isNull('promotions.brand'),
                                $qb->expr()->eq('promotions.brand', ':brand')
                            )
                        );
                        $qb->setParameter('brand', $userBrand);
                    }                    
                }

                $qb->andWhere(
                    $qb->expr()->orX(
                        $qb->expr()->isNull('promotions.client'), 
                        $qb->expr()->eq('promotions.client', ':client')
                    )
                );             
                $qb->setParameter('client', $client);
            }
        }

        // Simple case without, do not need a "having" sub request
        if (isset($filter['groupItem']) && !isset($filter['selection'])) {
            $qb->andWhere(
                $qb->expr()->orX(
                    $qb->expr()->in('groupItem.id', ':groupItem'),
                    $qb->expr()->in('groupItem.slug', ':groupItem')
                )
            )->setParameter('groupItem', $filter['groupItem']);

        }else if (isset($filter['selection'])) {
            /* All items in selection whith a AND */
            $subQb = $this->getMultipleSelectionsSubQuery($filter);
           
            $subQbResult = array_map(function($r){
                return $r['id'];
            },$subQb->getQuery()->getArrayResult());                   
                $qb->andWhere(
                    $qb->expr()->in($this->getAlias().'.id', ':slectionSubQuery')
                )->setParameter('slectionSubQuery', $subQbResult);            
        }

        if (isset($filter['is_ordered'])) {
            $qb->innerJoin($this->getAlias() . '.orderItems', 'orderItem');
            $qb->innerJoin('orderItem.order', 'o', Expr\Join::WITH, $qb->expr()->andX($qb->expr()->eq('o.user', ':user'), $qb->expr()->eq('o.platform', ':platform')))
                ->setParameter('user', $this->getTokenStorage()->getToken()->getUser()->getId())
                ->setParameter('platform', $filter['platform']);
        }

        if (isset($filter['manufacturer'])) {
            $qb->join($this->getAlias() . '.product', 'p');
            $qb->join('p.manufacturer', 'manufacturer', 'WITH', $qb->expr()->in('manufacturer.name', ':manufacturerName'))
                ->setParameter('manufacturerName', $filter['manufacturer']);
        }

        if (isset($filter['stock']) && $filter['stock'] == 'available') {
            if (!in_array('stock', $qb->getAllAliases())) {
                $qb->join($this->getAlias() . '.stock', 'stock');
            }

            $qb->andWhere($qb->expr()->andX($qb->expr()->gt('stock.valueCu', ':minQuantity'), $qb->expr()->gt('stock.valuePacking', ':minQuantity')))
                ->setParameter('minQuantity', 0);
        }

        return $qb;
    }

    /**
     * @param QueryBuilder $qb
     * @param null $idPlatform
     * @throws BadRequestException
     * @throws ForbiddenException
     */
    protected function getAcl($qb, $idPlatform = null): void
    {
        if ($this->tokenStorage) {
            $user = $this->tokenStorage->getToken()->getUser();
        }

        if (!in_array('p', $qb->getAllAliases())) {
            $qb->join($this->alias . '.product', 'p');
        }

        $qb->andWhere($this->alias . '.active = :active')
            ->setParameter('active', true);

        if ($this->tokenStorage && $user instanceof UserCustomer) {

            $client = $user->getClient();

            //utilisation de getAllGroupItems(), pour avoir tous les groupItems de tous les groupClients du clients
            $groupItems = $client->getAllGroupItems();

            $ids = [];
            /** @var GroupItem $groupItem */
            foreach ($groupItems as $groupItem) {
                $ids[] = $groupItem->getId();
            }

            if (count($ids) == 0) {
                throw new ForbiddenException('This client does not have any Items');
            }

            $qb->join($this->alias . '.groupItems', 'groupItem', 'WITH', $qb->expr()->in('groupItem.id', $ids));

            $qb->andWhere($qb->expr()->eq('groupItem.platform', ':platform'))
                ->setParameter('platform', $idPlatform);

            $clientStatus = $client->getClientStatusByPlatform($idPlatform);

            if (!$clientStatus) {
                throw new BadRequestException("the client does not have a status in the given platform");
            }

            $statusPreorder = $clientStatus->getStatusPreorder();
            $statusCatalog = $clientStatus->getStatusCatalog();

            if ($statusPreorder != ClientStatus::STATUS_PREORDER_ACTIVE && $statusCatalog != ClientStatus::STATUS_CATALOG_ACTIVE) {
                throw new ForbiddenException('client blocked in this platform !');
            }

            if ($statusCatalog == ClientStatus::STATUS_CATALOG_BLOCKED) {
                $qb->andWhere($qb->expr()->neq('groupItem.status', ':status'))
                    ->setParameter('status', GroupItem::STATUS_CATALOG);
                $this->isPreorderQuery($qb);
            }

            if ($statusPreorder == ClientStatus::STATUS_PREORDER_BLOCKED) {
                $qb->andWhere($qb->expr()->neq('groupItem.status', ':status'))
                    ->setParameter('status', GroupItem::STATUS_PREORDER);
                $this->isPreorderQuery($qb, false);
            }

        } elseif ($this->tokenStorage && ($user instanceof UserCommercial || $user instanceof UserAdmin)) {
            $platform = $user->getPlatform();
            $qb->andWhere($qb->expr()->eq($this->alias . '.platform', ':platform'))
                ->setParameter('platform', $platform->getId());
        }
    }

    /**
     * @param array $filter
     * @return mixed
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function getCount(array $filter)
    {
        $qb = $this->createCountQueryBuilder($filter);
        (isset($filter['platform'])) ? $this->getAcl($qb, $filter['platform']) : $this->getAcl($qb);
        return $qb->getQuery()->getOneOrNullResult();
    }

     /**
     * @param array $filter
     * @return QueryBuilder $qb
     */
    protected function getMultipleSelectionsSubQuery(array $filter){
        
        $toTest = $filter['selection'];
        if(isset($filter['groupItem'])){
            $toTest =  array_merge($filter['selection'], $filter['groupItem']);
        }        
        $subQb = parent::createFindByQueryBuilder($filter, array());
        $subQb->join($this->alias . '.groupItems', 'groupItem');

        $subQb->select($this->getAlias().'.id');

        $subQb->andWhere(
            $subQb->expr()->orX(
                $subQb->expr()->in('groupItem.slug', ':selection'),
                $subQb->expr()->in('groupItem.id', ':selection')
            )
        )->setParameter('selection',  $toTest);

        $subQb->groupBy($this->getAlias().'.id')
        ->having('COUNT( '.$this->getAlias().'.id) = :count')
        ->setParameter('count', count($toTest));
        
        return $subQb;
    }


    /**
     * @param QueryBuilder $qb
     * @param bool $isPreorder
     */
    protected function isPreorderQuery(QueryBuilder $qb, $isPreorder = true): void
    {
        $qb->andWhere($qb->expr()->eq($this->alias . '.isPreorder', ':isPreorder'));
        $qb->setParameter('isPreorder', $isPreorder);
    }

    /**
     * @param QueryBuilder $qb
     * @param $itemResults
     * @param bool $limitManufacturers
     * @return array
     */
    private function showManufacturers(QueryBuilder $qb, $itemResults, $limitManufacturers = true): array
    {
        if (!in_array('manufacturer', $qb->getAllAliases())) {
            $qb->join('p.manufacturer', 'manufacturer');
        }

        $qb->groupBy('manufacturer.id');
        $qb->orderBy('manufacturer.id', 'DESC');
        $qb->select(array(
            'COUNT(DISTINCT '. $this->getAlias().'.id) as numberDataAvailable',
            'manufacturer.id',
            'manufacturer.slug',
            'manufacturer.name',
            'manufacturer.extCode'
        ));

        $limitManufacturers = filter_var($limitManufacturers, FILTER_VALIDATE_BOOLEAN);
        if (!$limitManufacturers) {
            $qb->setFirstResult(null)
                ->setMaxResults(null);
        } else {
            $this->processLimit($qb, 5, 0);
        }

        $manufacturersResults = $qb->getQuery()->getResult();

        return array(
            'manufacturers' => $manufacturersResults,
            'items' => $itemResults,
        );
    }

    protected function processOrderBy($qb, $orderBy)
    {
        if(is_array($orderBy) && array_key_exists('random', $orderBy)){
            $qb->addOrderBy('RAND()');
            $orderBy = null;
        }
        parent::processOrderBy($qb, $orderBy);
    }


}
