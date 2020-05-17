<?php

namespace Sogedial\ApiBundle\EventListener;

use Doctrine\ORM\Event\LifecycleEventArgs;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

/**
 * Description of InsertUserListener
 *
 * @author Nidhal BEN KHALIFA <nidhalbk@icloud.com>
 */
class InsertUserListener {

    private $tokenStorage;

    public function __construct(TokenStorageInterface $tokenStorage) {
        $this->tokenStorage = $tokenStorage;
    }

    public function postLoad(LifecycleEventArgs $args) {
        $entity = $args->getEntity();
        if (method_exists($entity, 'setCurrentUser') && $this->tokenStorage->getToken()) {
            $entity->setCurrentUser($this->tokenStorage->getToken()->getUser());
        }
    }

}
