<?php


namespace Sogedial\ApiBundle\Listener;

use Psr\Log\LoggerInterface;
use Symfony\Component\DependencyInjection\ContainerInterface as Container;
use Symfony\Component\HttpKernel\Event\FilterResponseEvent;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\HttpKernel\Event\GetResponseForExceptionEvent;

class LogListener  
{
  protected $container;
  private $logger;

  public function __construct(Container $container,LoggerInterface  $logger)
  {
    $this->container = $container;
    $this->logger = $logger;
  }
  
  public function logStarting(GetResponseEvent $event)
  {
    $microtime = $this->microtimeFloat();
    if (!$event->isMasterRequest()) {
        return;
      }
    $request=$event->getRequest();
    $context = $request->attributes->get('_controller');
    if ($this->container->getParameter('kernel.environment')=="dev"){
        $parameters = $request->query->all();
    }else{
        $parameters = $request->query->keys();

    }
    if (preg_match("/ApiBundle/", $context)){
        $this->logger->info('API_CALL_STARTING',array('context'=>$context,'microtime'=>$microtime,'parameters'=>$parameters, 'url' => $request->getUri()));    
      }
    }
    public function logEnding(FilterResponseEvent $event)
    {
        $microtime = $this->microtimeFloat();
        $response=$event->getResponse();
        $request = $event->getRequest();
        $context = $request->attributes->get('_controller');
        $identifiant = $request->attributes->get('id');
        $nbresult = $response->getcontent();
        
        if( is_array(json_decode($nbresult))&& preg_match("/ApiBundle/", $context)  ){
            $nb = count(json_decode($nbresult));
            $this->logger->info('API_CALL_ENDING',array('context'=>$context,'microtime'=>$microtime,'url' => $request->getUri(), 'id'=>$identifiant, 'count'=>$nb));   
        }else if (preg_match("/ApiBundle/", $context)) {
            $this->logger->info('API_CALL_ENDING',array('context'=>$context,'microtime'=>$microtime,'url' => $request->getUri(), 'id'=>$identifiant));  
        }
    }
  
    public function logError(GetResponseForExceptionEvent $event)
    {
        $microtime = $this->microtimeFloat();
        if (!$event->isMasterRequest()) {
            return;
        }
        
        $request=$event->getRequest();
        $context = $request->attributes->get('_controller');
        
        if ($this->container->getParameter('kernel.environment')=="dev"){
            $parameters = $request->query->all();
        }else{
            $parameters = $request->query->keys();
        }

        $this->logger->error('API_CALL_ERROR', array(
            'context' => $context,
            'microtime' => $microtime,
            'parameters' => $parameters,
            'url' => $request->getUri(),
            'description' => $event->getException()->getMessage(),
            'username' => $request->get('username')));

    }
    
    private function microtimeFloat()
    {
        list($usec, $sec) = explode(" ", microtime());
        return ((float)$usec + (float)$sec);
    }
}