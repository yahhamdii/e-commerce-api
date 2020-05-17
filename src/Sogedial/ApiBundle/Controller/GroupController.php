<?php

namespace Sogedial\ApiBundle\Controller;

use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Controller\Annotations\Delete;
use FOS\RestBundle\Controller\Annotations\QueryParam;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Sogedial\ApiBundle\Entity\Category;
use Sogedial\ApiBundle\Entity\Client;
use Sogedial\ApiBundle\Entity\Group;
use Sogedial\ApiBundle\Entity\GroupClient;
use Sogedial\ApiBundle\Entity\GroupItem;
use Sogedial\ApiBundle\Entity\Platform;
use Sogedial\ApiBundle\Exception\BadRequestException;
use Sogedial\ApiBundle\Exception\Exception;
use Sogedial\ApiBundle\Helper\EntityHelper;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;

/**
 * Group controller.
 *
 * @Rest\Route(path="/api/group")
 */
class GroupController extends Controller {

    const LIMIT = 10;


    /**
     * get GroupItems with Categories
     *
     * @Rest\Get("/categories", name="get_group_categories")
     * @Rest\View(StatusCode = 200,serializerEnableMaxDepthChecks=true, serializerGroups={"listitems"})
     * @QueryParam(name="platform", default=null, description="plateform du client")
     * @QueryParam(name="client", default=null, description="client sur lequel effectuer la recherche")
     * @QueryParam(name="showItems", default=false, description="doit renvoyer les items")
     * @QueryParam(name="status", default="", description="status concerné")
     * @ParamConverter("platform", class="Sogedial\ApiBundle\Entity\Platform")
     * @ParamConverter("client", class="Sogedial\ApiBundle\Entity\Client")     
     */
    public function getGroupCategoriesAction(Platform $platform, Client $client = null, $showItems = false, $status = '') {
        $repo = $this->get('sogedial.repository_injecter')->getRepository(Category::class);
        $categories = $repo->getUserAllCategories($platform, $client, $showItems, $status);
        return $categories;
    }


    /**
     * Lists all Group entities.
     *
     * @Rest\Get("/{type}", name="get_all_group")
     * @Rest\View(serializerEnableMaxDepthChecks=true, statusCode= 200, serializerGroups={"list"})
     * @QueryParam(name="offset", requirements="\d+", default="0", description="Index de dÃ©but de la pagination")
     * @QueryParam(name="limit", requirements="\d+", default="10", description="Nombre d'Ã©lÃ©ments Ã  afficher")
     * @QueryParam(name="orderBy", default=null, description="nom de champ de l'ordre")
     * @QueryParam(name="orderByDesc", default=null, description="nom de champ de l'ordre descendant")
     * @QueryParam(name="filter", default=null, description="filtre sur les champs")
     */
    public function getAllAction($orderBy = null, $orderByDesc = null, $limit = self::LIMIT, $offset = 0, $filter = null, $type = null) {
        $order = 'asc';

        $em = $this->getDoctrine()->getManager();
        if ($orderBy == null) {
            $meta = $em->getClassMetadata(Group::class);
            $orderBy = $meta->getSingleIdentifierFieldName();
        }
        if ($orderByDesc != null) {
            $orderBy = $orderByDesc;
            $order = 'desc';
        }

        $filter = ($filter != null) ? json_decode($filter, true) : [];

        return $this->get('sogedial.repository_injecter')->getRepository(Group::class)->findBy($filter, [$orderBy => $order], $limit, $offset, $type);
    }

    /**
     * create a new Group entity.
     *
     * @Security("has_role('ROLE_SUPER_ADMIN') or has_role('ROLE_ADMIN') or has_role('ROLE_COMMERCIAL')")
     * @Rest\Post("/add/{type}", name="add_group")
     * @Rest\View(StatusCode = 201,serializerEnableMaxDepthChecks=true, serializerGroups={"add"})
     * @ParamConverter("group",class="array", converter="fos_rest.request_body")
     */
    public function addAction(array $group, $type) {
        
        $em = $this->getDoctrine()->getManager();
        $group['type']  = $type;

        if($group['type'] == 'item'){
            $entity = new GroupItem();

            /* Adding New Group Client */
            $groupClient = new GroupClient();                        
            $client = $em->getRepository('SogedialApiBundle:Client')->find($group['client']['id']);
            $pricing = $client->getGroupClient($this->getUser()->getPlatform())->getPricing();

            $groupClient->addClient($client)
                ->setPlatform($this->getUser()->getPlatform())
                ->setName($group['label'])
                ->setLabel($group['label'])
                ->setPricing($pricing)
                ->setCode($this->getUser()->getPlatform()->getExtCode().'-'.$pricing->getLabel())
                ->setStatus(GroupClient::STATUS_SELECTION);

            $em->persist($groupClient);
            $em->flush();
            $em->refresh($groupClient);

            $client->addGroupClient($groupClient);
            $em->persist($client);
            $em->flush();
            $em->refresh($client);

            $entity->addGroupClient($groupClient);
           
            foreach ($group['items'] as $item){
                $item_add =  $em->getRepository('SogedialApiBundle:Item')->find($item);
                if($item_add){
                    $entity->addItem($item_add);
                }
            }

            $entity->setPricing($pricing)
            ->setCode($this->getUser()->getPlatform()->getExtCode().'-'.$pricing->getLabel());

            unset($group['items']);
            unset($group['client']);
            //$entity->setEnabled(1);
        }
        else if($group['type'] == 'client'){
            $entity = new GroupClient(); 
        }

        if(isset($group['date_end'])){
            $group['date_end'] = new \DateTime($group['date_end']);
        }

        EntityHelper::updateDatas($entity, $group, $em);

        $entity->setPlatform($this->getUser()->getPlatform());
      
        try {
            $em->persist($entity);
            $em->flush();
            $em->refresh($entity);
        } catch (Exception $exc) {
            throw new \Sogedial\ApiBundle\Exception\InsertEntityException;
        }
        return $entity;
    }


    /**
     * Finds and displays a Group entity.
     *
     * @Security("has_role('ROLE_SUPER_ADMIN') or has_role('ROLE_ADMIN') or has_role('ROLE_ADMIN')")
     * @Rest\Get("/detail/{id}", name="get_group")
     * @Rest\View(statusCode= 200, serializerEnableMaxDepthChecks=true, serializerGroups={"detail"})
     */
    public function getAction($id) {
        try {
            $em = $this->getDoctrine()->getManager();
            $group = $em->getRepository('SogedialApiBundle:Group')->find($id);
        } catch (Exception $exc) {
            throw new \Sogedial\ApiBundle\Exception\EntityNotFoundException;
        }

        return $group;
    }

    /**
     * Displays a form to edit an existing Group entity.
     *
     * @Security("has_role('ROLE_SUPER_ADMIN') or has_role('ROLE_ADMIN') or has_role('ROLE_COMMERCIAL')")
     * @Rest\Put("/update", name="update_group")
     * @Rest\View(StatusCode = 200,serializerEnableMaxDepthChecks=true, serializerGroups={"update"})
     * @ParamConverter("group",class="array",converter="fos_rest.request_body")
     */
    public function updateAction(array $group) {

        $em = $this->getDoctrine()->getManager();

        $id = $group['id'];
        if (!isset($id))
            throw new \Sogedial\ApiBundle\Exception\ParametersException(['id']);

        $entity = $em->getRepository('SogedialApiBundle:Group')->find($id);
        if (!$entity)
            throw new \Sogedial\ApiBundle\Exception\EntityNotFoundException('The group with the id : ' . $id . ' does not exists');

        if(isset($group['date_end'])){
            $group['date_end'] = new \DateTime($group['date_end']);
        }    

        EntityHelper::updateDatas($entity, $group, $em);

        if(isset($group['client'])){
            $client = $em->getRepository('SogedialApiBundle:Client')->find($group['client']['id']);
            $groupClient = $entity->getGroupClients();
            $groupClient = $groupClient[0];
    
            if(!$groupClient->getClients()->contains($client)){
                $groupClient->addClient($client);
                $em->persist($groupClient);
                $em->flush();
                $em->refresh($groupClient);
        
                $client->addGroupClient($groupClient);
                $em->persist($client);
                $em->flush();
                $em->refresh($client);            
            }
        }        
       
        if(isset($group['items'])){
            foreach ($group['items'] as $item){
                $item_add =  $em->getRepository('SogedialApiBundle:Item')->find($item);
                if($item_add && !$entity->getItems()->contains($item_add)){
                    $entity->addItem($item_add);
                }
            }
        }

        try {
            $em->merge($entity);
            $em->flush();
        } catch (Exception $exc) {
            throw new \Sogedial\ApiBundle\Exception\UpdateEntityException;
        }

       return $entity;
    }

    /**
     * Delete Group by id
     *
     * @Security("has_role('ROLE_SUPER_ADMIN') or has_role('ROLE_ADMIN') or has_role('ROLE_COMMERCIAL')")
     * @Delete("/delete/{id}", name="delete_group")
     * @Rest\View(StatusCode = 200)
     */
    public function deleteAction(Group $group) {
        $em = $this->getDoctrine()->getManager();
        $id = $group->getId();
        $groupItem = $em->getRepository('SogedialApiBundle:GroupItem')->find($id);
        try {
            if($groupItem){
                $groupItem->removeGroupClients();
                $groupItem->removeItems();
            }
            $em->remove($group);
            $em->flush();
            $this->get('session')->getFlashBag()->add('success', 'The Group was deleted successfully');
        } catch (Exception $exc) {
            throw new \Sogedial\ApiBundle\Exception\DeleteEntityException;
        }

        return "Entity $id was removed";
    }


    /**
     * create a selection groupItem
     *
     * @Security("has_role('ROLE_SUPER_ADMIN') or has_role('ROLE_ADMIN') or has_role('ROLE_COMMERCIAL')")
     * @Rest\Post(name="create_selection", path="/selection/create")
     * @Rest\RequestParam(name="client", nullable = false)
     * @Rest\RequestParam(name="dateEnd", nullable = true)
     * @Rest\RequestParam(name = "label", nullable = false)
     * @Rest\RequestParam(name = "items", nullable = true)
     * @Rest\View(StatusCode = 200)
     */
    public function createSelection(Request $request, Client $client, \DateTime $dateEnd, $label, $items = [])
    {

        /** @var UploadedFile $file */
        $file = $request->files->get('file');
        $ext = $file->getClientOriginalExtension();
        if (is_readable($file) && ($ext == 'xls' || $ext == 'xlsx')) {
            $em = $this->getDoctrine()->getManager();
            $platform = $this->getUser()->getPlatform();
            $pricing = $client->getGroupClient($platform)->getPricing();
            $spreadsheet = IOFactory::load($file);
            $data = $spreadsheet->getActiveSheet()->toArray();
            $validated = [];
            $errors = [];
            $rep = $this->get('sogedial.repository_injecter')->getRepository('SogedialApiBundle:Item');

            foreach ($data as $value) {
                $itemCode = $platform->getExtCode() . '-' . trim($value[0]) . '-' . $pricing->getLabel();
                $item = $rep->findOneBy(array('code' => $itemCode, 'platform' => $platform));
                if ($item) {
                    $items[$itemCode] = $item;
                    $validated[] = $item->getId();
                } else {
                    $errors[] = $value[0];
                }
            }

            if (!empty($items)) {
                $groupClient = new GroupClient();
                $groupClient->addClient($client)
                    ->setPlatform($platform)
                    ->setName($label)
                    ->setPricing($pricing)
                    ->setCode($platform->getExtCode() . '-' . $pricing->getLabel())
                    ->setStatus(GroupClient::STATUS_SELECTION);
                $client->addGroupClient($groupClient);
                $em->persist($groupClient);

                $groupItem = new GroupItem();
                $groupItem->setPlatform($platform)
                    ->setPricing($pricing)
                    ->setStatus(GroupItem::STATUS_SELECTION);
                $groupItem->setLabel($label);
                $groupItem->setDateEnd($dateEnd)
                    ->setCode($platform->getExtCode() . '-' . $pricing->getLabel())
                    ->setName($label);

                $groupClient->addGroupItem($groupItem);
                $groupItem->addGroupClient($groupClient);

                //affectation item to groupItem selection, on annulant s'il ya des doublons
                foreach ($items as $item) {
                    if (!$groupItem->getItems()->contains($item)) {
                        $groupItem->addItem($item);
                        $item->addGroupItem($groupItem);
                    }
                }

                $em->persist($groupItem);
                $em->flush();
            }

            return [
                'validated' => $validated,
                'errors' => $errors,
                'groupItem' => (!empty($validated)) ? $groupItem : null,
            ];
        }

        throw new BadRequestException("file is invalid, please select an excel file !");
    }
}

