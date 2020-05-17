<?php

namespace Sogedial\ApiBundle\Controller;

use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Controller\Annotations\QueryParam;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Sogedial\ApiBundle\Entity\Brand;
use Sogedial\ApiBundle\Entity\Category;
use Sogedial\ApiBundle\Entity\Client;
use Sogedial\ApiBundle\Entity\ClientStatus;
use Sogedial\ApiBundle\Entity\Item;
use Sogedial\ApiBundle\Exception\EntityNotFoundException;
use Sogedial\OAuthBundle\Controller\UserController;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;

/**
 * Client controller.
 *
 * @Rest\Route(path="/api/client")
 */
class ClientController extends Controller {

    const LIMIT = 60;

    /**
     * Lists all Client entities.
     *
     * @Security("has_role('ROLE_ADMIN') or has_role('ROLE_COMMERCIAL')")
     * @Rest\Get("", name="get_all_client")
     * @Rest\View(StatusCode = 200, serializerEnableMaxDepthChecks=true)
     * @QueryParam(name="offset", requirements="\d+", default="0", description="Index de début de la pagination")
     * @QueryParam(name="limit", requirements="\d+", default="60", description="Nombre d'éléments à afficher")
     * @QueryParam(name="orderBy", default=null, description="nom de champ de l'ordre")
     * @QueryParam(name="orderByDesc", default=null, description="nom de champ de l'ordre descendant")
     * @QueryParam(name="filter", default=null, description="filtre sur les champs")
     */
    public function getAllAction($orderBy = null, $orderByDesc = null, $limit = self::LIMIT, $offset = 0, $filter = null) {
        $order = 'asc';
        $em = $this->getDoctrine()->getManager();

        if ($orderBy == null) {
            $meta = $em->getClassMetadata(Client::class);
            $orderBy = $meta->getSingleIdentifierFieldName();
        }

        if ($orderByDesc != null) {
            $orderBy = $orderByDesc;
            $order = 'desc';
        }

        $filter = ($filter != null) ? json_decode($filter, true) : [];
        $repo = $this->get('sogedial.repository_injecter')->getRepository(Client::class);
        return $repo->findBy($filter, [$orderBy => $order], $limit, $offset);
    }

    /**
     * Count Client available after filter
     *
     * @Security("has_role('ROLE_ADMIN') or has_role('ROLE_COMMERCIAL')")
     * @Rest\Get("/count", name="count_client")
     * @Rest\View(StatusCode = 200)
     * @QueryParam(name="filter", default=null, description="filtre sur les champs")
     */
    public function countAction($filter = null) {
        $filter = ($filter != null) ? json_decode($filter, true) : [];

        return $this->get('sogedial.repository_injecter')->getRepository('SogedialApiBundle:Client')->getCount($filter);
    }

    /**
     * Finds and displays a Client entity.
     *
     * @Security("has_role('ROLE_SUPER_ADMIN')")
     * @Rest\Get("/{id}", name="get_client")
     * @Rest\View(statusCode = 200, serializerEnableMaxDepthChecks=true)
     */
    public function getAction(Client $client = null, $id) {
        if (empty($client)) {
            throw new EntityNotFoundException('client with id : ' . $id . ' was not found.');
        }

        return $client;
    }

    /**
     * create a new Client entity.
     *
     * @Security("has_role('ROLE_SUPER_ADMIN')")
     * @Rest\Post("/add", name="add_client")
     * @Rest\View(StatusCode = 201, serializerEnableMaxDepthChecks=true)
     * @ParamConverter("client", converter="fos_rest.request_body")
     */
    public function addAction(Client $client) {
        $em = $this->getDoctrine()->getManager();
        $em->persist($client);
        $em->flush();

        return $client;
    }

    /**
     * Displays a form to edit an existing Client entity.
     *
     * @Security("has_role('ROLE_ADMIN') or has_role('ROLE_COMMERCIAL')")
     * @Rest\Put("/update", name="update_client")
     * @Rest\View(StatusCode = 200, serializerEnableMaxDepthChecks=true)
     * @QueryParam(name="activate", default="0", description="Envoyer le mail d'activation si besoin")
     * @ParamConverter("client", converter="fos_rest.request_body")
     */
    public function updateAction(Client $client, $activate=false)
    {
        $em = $this->getDoctrine()->getManager();
        $em->merge($client);
        $em->flush();
        
        if($activate){
            $platform = $this->getUser()->getPlatform();        
            $this->get('sogedial_client_activation')->activate($client, $platform);                
        }

        return $client;
    }

    /**
     * Delete Client by id
     * @Security("has_role('ROLE_SUPER_ADMIN')")
     * @Rest\Delete("/delete/{id}", name="delete_client")
     * @Rest\View(StatusCode = 200)
     */
    public function deleteAction(Client $client = null, $id) {
        if (empty($client)) {
            throw new EntityNotFoundException('client with id : ' . $id . ' was not found.');
        }

        $em = $this->getDoctrine()->getManager();
        $em->remove($client);
        $em->flush();

        return new JsonResponse(sprintf("client with id: %s  was removed.", $id), 200);
    }


    /*
     * create a new Prospect.
     *
     * @Security("has_role('ROLE_ADMIN') or has_role('ROLE_COMMERCIAL')")
     * @Rest\Post("/prospect/add", name="add_prospect")
     * @Rest\RequestParam(name="lastname")
     * @Rest\RequestParam(name="firstname")
     * @Rest\RequestParam(name="username")
     * @Rest\RequestParam(name="faxUtilisateur")
     * @Rest\RequestParam(name="plainPassword")
     * @Rest\RequestParam(name="plainPasswordConfirm")
     * @Rest\RequestParam(name="email")
     * @Rest\RequestParam(name="telNumber1")
     * @Rest\RequestParam(name="typology")
     * @Rest\RequestParam(name="brand")
     * @Rest\RequestParam(name="assortmentItems")
     * @Rest\RequestParam(name="assortmentCategories")
     * @Rest\View(StatusCode = 201, serializerEnableMaxDepthChecks=true)
     * @ParamConverter("brand", converter="Sogedial\ApiBundle\Entity\Brand")
     */
    public function addProspectAction($lastname, $firstname, $username, $faxUtilisateur, $plainPassword, $plainPasswordConfirm, $email, $telNumber1, $typology, Brand $brand, array $assortmentItems, array $assortmentCategories)
    {
        $passwordUpdater = $this->get('sogedial.oauth.utils.password_updater');
        $user = UserController::createUserProspect($lastname, $firstname, $username, $faxUtilisateur, $plainPassword, $plainPasswordConfirm , $email, $telNumber1, $passwordUpdater);

        $em = $this->getDoctrine()->getManager();
        
        $groupClient = $em->getRepository('SogedialApiBundle:GroupClient')->find($client['group_client']['id']);
        $cloneGroupClient = clone $groupClient;

        $clientProspect = $this->createClientProspect($client, $cloneGroupClient, $user, $typology);

        $groupProductGeneral = GroupController::createGroupProduct($this->getUser()->getPlatform());
        $groupProductPreCommand = GroupController::createGroupProduct($this->getUser()->getPlatform(), true);

        foreach ($assortmentItems as $value) {
            $item = $em->getRepository(Item::class)->find($value);
            GroupController::addProduct($item, $groupProductPreCommand, $groupProductGeneral);
        }

        foreach ($assortmentCategories as $value) {
            $category = $em->getRepository(Category::class)->find($value);
            $items = $category->getItems();
            foreach ($items as $item) {
                GroupController::addProduct($item, $groupProductPreCommand, $groupProductGeneral);
            }
        }

        GroupController::persistGroupProduct($groupProductGeneral, $cloneGroupClient, $em);
        GroupController::persistGroupProduct($groupProductPreCommand, $cloneGroupClient, $em);

        $em->persist($clientProspect);
        $em->flush();

        return $clientProspect;
    }



    /*private function createClientProspect($client, GroupClient $cloneGroupClient, UserCustomer $user, $typology): Client
    {
        $clientProspect = new Client();
        $clientProspect->setName($client['name'])
            ->setGroupClient($cloneGroupClient)
            ->setStatus(Client::STATUS_PROSPECT)
            ->setSiret(uniqid())
            ->setTypology($typology)
            ->setValidityBeginDate(DateTime::createFromFormat('Y-m-d', $client['validityBeginDate']))
            ->setValidityEndDate(DateTime::createFromFormat('Y-m-d', $client['validityEndDate']));

        //liaison entre client et customerProspect
        $clientProspect->addCustomer($user);
        $user->setClient($clientProspect);

        //liaison entre client et commercial
        /**@var UserCommercial $userConnected */
        /*$userConnected = $this->getUser();
        $clientProspect->addCommercial($userConnected);
        $userConnected->addClient($clientProspect);

        return $clientProspect;
    }*/


    /**
     * @Security("has_role('ROLE_ADMIN')")
     * @Rest\Put(path="/activate" , name="activation_client")
     * @Rest\View(statusCode= 200, serializerEnableMaxDepthChecks= true)
     * @ParamConverter("client", converter="fos_rest.request_body")
     */
    public function activateAction(Client $client)
    {
        $platform = $this->getUser()->getPlatform();
        return $this->get('sogedial_client_activation')->activate($client, $platform);
    }

    /**
     * @Rest\Get("/client/getip", name="ipclient")
     * @Rest\View(statusCode= 200, serializerEnableMaxDepthChecks= true)
     */
    public function getClientIp()
    {
        $ip = getenv('HTTP_CLIENT_IP')?:
        getenv('HTTP_X_FORWARDED_FOR')?:
        getenv('HTTP_X_FORWARDED')?:
        getenv('HTTP_FORWARDED_FOR')?:
        getenv('HTTP_FORWARDED')?:
        getenv('REMOTE_ADDR');
   
         return $ip;
    }

}
