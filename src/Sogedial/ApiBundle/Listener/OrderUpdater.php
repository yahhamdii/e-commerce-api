<?php


namespace Sogedial\ApiBundle\Listener;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Sogedial\ApiBundle\Entity\Order;
use Sogedial\ApiBundle\Entity\OrderItem;

class OrderUpdater
{
    public function postPersist(LifecycleEventArgs $args)
    {
        $entity = $args->getEntity();
        if (!$entity instanceof Order) {
            return;
        }

        $this->generateNumber($entity, $args->getEntityManager());
        $this->updateQuantity($entity, $args->getEntityManager());
        
    }


    //besoin de passer par les event doctrine et specialement postPersist, pcq a ce moment la qu'on a le id
    //et le number est base sur l id
    public function generateNumber(Order $entity, EntityManager $em)
    {
        $number = "W" . str_pad($entity->getId(), 7, '0', STR_PAD_LEFT);
        $entity->setNumber($number);
        $em->flush();
    }

    private function updateQuantity(Order $order, EntityManager $em){
        $orderItems = $order->getOrderItems();
        /** @var OrderItem $orderItem */
        foreach ($orderItems as $orderItem){
            $item = $orderItem->getItem();
            
            $stock = $item->getInitialStock();
            if($stock){
                $reference = $orderItem->getQuantityOrdered();
                $stock->setValuePacking($stock->getValuePacking() - $reference['packages']);
                $stock->setValueCu($stock->getValueCu() - $reference['items']);
                $em->merge($stock);
            }

            if(!$item->getShouldReplaceRegularStock()){
                $promotion = $item->getPromotion();
                if($promotion){                    
                    $promotion->getStockCommitmentRemaining($promotion->getStockCommitmentRemaining() - $reference['packages']);                
                    $em->merge($promotion);
                }
            }         
        }

        $em->flush();
    }

}