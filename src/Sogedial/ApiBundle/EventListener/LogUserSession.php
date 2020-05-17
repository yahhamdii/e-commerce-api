<?php

namespace Sogedial\ApiBundle\EventListener;

use Doctrine\ORM\Event\LifecycleEventArgs;
use Symfony\Component\HttpKernel\Event\FilterResponseEvent;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

/**
 * Description of InsertUserListener
 *
 * @author Nidhal BEN KHALIFA <nidhalbk@icloud.com>
 */
class LogUserSession
{

    private $tokenStorage;

    public function __construct(TokenStorageInterface $tokenStorage)
    {
        $this->tokenStorage = $tokenStorage;
    }

    public function onKernelResponse(FilterResponseEvent $event)
    {
//        print_r('<pre>');
//        dump($this->securityTokenStorage);
//        die;
    }

}
