<?php

namespace Sogedial\ApiBundle\Service;

use Doctrine\ORM\EntityManager;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorage;

/**
 * Class RepositoryInjecter
 *
 * creer dont le but d injecter des services dans EntityRepository
 * afin de pouvoir l'acceder dans n'importe quelle repository
 */
class RepositoryInjecter
{

    private $em;
    private $tokenStorage;
    
    public function __construct(TokenStorage $tokenStorage, EntityManager $em)
    {
        $this->tokenStorage = $tokenStorage;        
        $this->em = $em;

    }

    /**
     * @param string $class
     */
    public function getRepository($class){

        return $this->em->getRepository($class)        
        ->setTokenStorage($this->tokenStorage);

    }
}