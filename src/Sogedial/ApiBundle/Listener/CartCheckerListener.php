<?php

namespace Sogedial\ApiBundle\Listener;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Sogedial\ApiBundle\Entity\Cart;
use Sogedial\ApiBundle\Entity\CartItem;

class CartCheckerListener
{

    private $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    public function postLoad(LifecycleEventArgs $args)
    {
        $entity = $args->getObject();

        if (!$entity instanceof Cart) {
            return;
        }

        $this->check($entity);
    }

    private function check(Cart $cart)
    {
        $cartItems = $cart->getCartItems();
        foreach ($cartItems as $cartItem) {
            /** @var CartItem $cartItem */
            $item = $cartItem->getItem();

            if (!$item || !$item->getActive()) {
                $cart->removeCartItem($cartItem);
                $toRemove = $this->em->merge($cartItem);
                $this->em->remove($toRemove);
                $this->em->flush();
            }
        }
    }

}