<?php

namespace Sogedial\ApiBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use FOS\RestBundle\Controller\Annotations\Route as Route;

class DefaultController extends Controller
{

     /**
     * @Route("/health")
     */
    public function healthAction()
    {
        return $this->render('SogedialApiBundle:Default:health.html.twig');
    }

}
