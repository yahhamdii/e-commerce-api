<?php
namespace Sogedial\OAuthBundle\EventListener;

use FOS\OAuthServerBundle\Event\OAuthEvent;
use FOS\OAuthServerBundle\Security\Authentication\Token\OAuthToken;
use Psr\Log\LoggerInterface;

use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Event\FilterResponseEvent;

use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use FOS\OAuthServerBundle\Storage\OAuthStorage;



use Symfony\Component\HttpKernel\KernelEvents;

class OAuthEventListener
{
    private $logger;
    private $securityTokenStorage;
    private $fosOauthTokenStorage;
    
    public function __construct(LoggerInterface  $logger, TokenStorageInterface $securityTokenStorage, OAuthStorage $fosOauthTokenStorage){
        $this->logger = $logger;
        $this->securityTokenStorage = $securityTokenStorage;
        $this->fosOauthTokenStorage = $fosOauthTokenStorage;
    }
    
    public function onPreAuthorizationProcess(OAuthEvent $event)
    {
         $this->logger->info('Event onPreAuthorizationProcess');
    }

    public function onPostAuthorizationProcess(OAuthEvent $event)
    {
         $this->logger->info('Event onPostAuthorizationProcess');
    }
    
     public function onAuthenticationSuccess()
    {
         $this->logger->info('Event onAuthenticationSuccess');
    }
    
     public function onInteractiveLogin()
    {
         $this->logger->info('Event onInteractiveLogin');
    }
    
     public function onAuthenticationFailure()
    {
         $this->logger->info('Event onAuthenticationFailure');
    }
    
    public function onKernelResponse(FilterResponseEvent $event)
    {
        /** @var \FOS\OAuthServerBundle\Security\Authentication\Token\OAuthToken $_token */
        /*
        $_token = $this->securityTokenStorage->getToken();
        
        if ( $_token ) {
            if( !$_token instanceof OAuthToken )
                throw new AccessDeniedHttpException('This action needs a valid token');
            
            // @var \FOS\OAuthServerBundle\Model\AccessToken $token
            $token = $this->fosOauthTokenStorage->getAccessToken($_token->getToken());
           
            // @var \FOS\OAuthServerBundle\Model\ClientInterface 
            $client = $token->getClient();
           
        }*/
    }
}