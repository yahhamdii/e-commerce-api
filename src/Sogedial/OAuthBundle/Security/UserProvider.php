<?php

namespace Sogedial\OAuthBundle\Security;


use Sogedial\OAuthBundle\Entity\User;
use Sogedial\OAuthBundle\Entity\UserAdmin;
use Sogedial\OAuthBundle\Entity\UserCommercial;
use Sogedial\OAuthBundle\Entity\UserCustomer;
use Sogedial\OAuthBundle\Repository\UserRepository;
use Sogedial\OAuthBundle\Utils\PasswordUpdaterInterface;

use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Doctrine\ORM\EntityManager;

class UserProvider implements UserProviderInterface
{

    private $userRepository;
    private $passwordUpdater;
    private $em;


    /**
     * UserProvider constructor.
     * @param UserRepository $userRepository
     * @param PasswordUpdaterInterface $passwordUpdater
     * @param EntityManager $em
     */
    public function __construct(UserRepository $userRepository, PasswordUpdaterInterface $passwordUpdater, EntityManager $em )
    {

        $this->userRepository = $userRepository;        
        $this->passwordUpdater= $passwordUpdater;
        $this->em = $em;
    }


    /**
     * @param string $username
     * @return mixed
     */
    public function loadUserByUsername($username)
    {
        $user = $this->findUser($username);

        return $user;
    }


    /**
     * @param UserInterface $user
     * @return mixed
     */
    public function refreshUser(UserInterface $user)
    {
        $class = get_class($user);

        if (!$this->supportsClass($class)) {
            throw new UnsupportedUserException(
                sprintf('Instances of "%s" are not supported.', get_class($user))
            );
        }

        return $this->findUser($user->getUsername());
    }

    /**
     * Whether this provider supports the given user class.
     *
     * @param string $class
     *
     * @return bool
     */
    public function supportsClass($class)
    {
        $classSupported = array('user'=> User::class, 'Admin' => UserAdmin::class, 'Commercial' => UserCommercial::class, 'Customer' => UserCustomer::class);
        return in_array($class, $classSupported);
    }

    /**
     * @param $username
     * @return mixed
     */
    private function findUser($username)
    {
        $user = $this->userRepository->getUserByUsername($username);

        if (!$user) {
            throw new UsernameNotFoundException(
                sprintf('Username "%s" does not exist.', $username)
            );
        }

        return $user;

    }
    public function createUser($username, $email, $plainPassword, $role, $enabled){
        
        try{
            $this->findUser($username);
            throw new \Exception(
                sprintf('Username "%s" already not exists.', $username)
            );
        }catch(UsernameNotFoundException $e){
             $user = $this->userRepository->createUser($this->passwordUpdater, $username, $email, $plainPassword, $role, $enabled); 
            return $user;             
        }
    }
   
}