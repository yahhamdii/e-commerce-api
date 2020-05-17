<?php

namespace Sogedial\ApiBundle\Controller;

use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Controller\Annotations\QueryParam;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Sogedial\ApiBundle\Entity\Category;
use Sogedial\ApiBundle\Entity\Item;
use Sogedial\ApiBundle\Exception\EntityNotFoundException;
use Sogedial\ApiBundle\Exception\ParametersException;
use Sogedial\OAuthBundle\Entity\UserCustomer;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

/**
 * Item controller.
 *
 * @Rest\Route(path="/api/item")
 */
class ItemController extends Controller {

    const LIMIT = 60;

    /**
     * Lists all Item entities.
     *
     * @Rest\Get("", name="get_all_item")
     * @Rest\View(StatusCode = 200, serializerEnableMaxDepthChecks=true, serializerGroups={"list"})
     * @QueryParam(name="offset", requirements="\d+", default="0", description="Index de début de la pagination")
     * @QueryParam(name="limit", requirements="\d+", default="60", description="Nombre d'éléments à afficher")
     * @QueryParam(name="orderBy", default=null, description="nom de champ de l'ordre")
     * @QueryParam(name="orderByDesc", default=null, description="nom de champ de l'ordre descendant")
     * @QueryParam(name="filter", default=null, description="filtre sur les champs")
     * @QueryParam(name="showManufacturers", default= false, description="retourne les manufacturers groupés")
     * @QueryParam(name="limitManufacturers", default= true, description="to controle limit number manufacturer returned")
     * @param null $orderBy
     * @param null $orderByDesc
     * @param int $limit
     * @param int $offset
     * @param null $filter
     * @param bool $showManufacturers
     * @param bool $limitManufacturers
     * @return
     * @throws EntityNotFoundException
     * @throws ParametersException
     */
    public function getAllAction($orderBy = null, $orderByDesc = null, $limit = self::LIMIT, $offset = 0, $filter = null, $showManufacturers = false, $limitManufacturers = true)
    {
        $order = 'asc';
        $repoInjector = $this->get('sogedial.repository_injecter');

        if ($orderBy == null) {
            //$meta = $em->getClassMetadata(Item::class);
            //$orderBy = $meta->getSingleIdentifierFieldName();
            $orderBy = "categories.name";
        }

        if ($orderByDesc != null) {
            $orderBy = $orderByDesc;
            $order = 'desc';
        }

        $filter = ($filter != null) ? json_decode($filter, true) : [];
        if ($this->getUser() instanceof UserCustomer) {
            if (!isset($filter['platform'])) {
                throw new ParametersException(array('platform'));
            }
        }

        $res = $repoInjector->getRepository(Item::class)->findBy($filter, [$orderBy => $order], $limit, $offset, $showManufacturers, $limitManufacturers);
       
        return $res;
    }



    /**
     * Count Item available after filter
     *
     * @Rest\Get("/count", name="count_item")
     * @Rest\View(StatusCode = 200)
     * @QueryParam(name="filter", default=null, description="filtre sur les champs")
     * @param null $filter
     * @return
     * @throws ParametersException
     */
    public function countAction($filter = null) {
        $repoInjector = $this->get('sogedial.repository_injecter');

        $filter = ($filter != null) ? json_decode($filter, true) : [];
        if($this->getUser() instanceof UserCustomer){
            if(!isset($filter['platform'])){
                throw new ParametersException(array('platform'));
            }
        }
        
        return $repoInjector->getRepository('SogedialApiBundle:Item')->getCount($filter);
    }

    /**
     * Finds and displays a Item entity.
     *
     * @Rest\Get("/{id}", name="get_item")
     * @Rest\View(statusCode = 200, serializerEnableMaxDepthChecks=true, serializerGroups={"detail"})
     * @param Item|null $item
     * @param $id
     * @return Item
     * @throws EntityNotFoundException
     */
    public function getAction(Item $item = null, $id) {
        if (empty($item)) {
            throw new EntityNotFoundException('item with id : ' . $id . ' was not found.');
        }

        return $item;
    }

    /**
     * create a new Item entity.
     *
     * @Security("has_role('ROLE_SUPER_ADMIN')")
     * @Rest\Post("/add", name="add_item")
     * @Rest\View(StatusCode = 201, serializerEnableMaxDepthChecks=true, serializerGroups={"add"})
     * @ParamConverter("item", converter="fos_rest.request_body")
     * @param Item $item
     * @return Item
     */
    public function addAction(Item $item) {
        $em = $this->getDoctrine()->getManager();
        $em->persist($item);
        $em->flush();

        return $item;
    }

    /**
     * Displays a form to edit an existing Item entity.
     *
     * @Security("has_role('ROLE_SUPER_ADMIN')")
     * @Rest\Put("/update", name="update_item")
     * @Rest\View(StatusCode = 200, serializerEnableMaxDepthChecks=true, serializerGroups={"update"})
     * @ParamConverter("item", converter="fos_rest.request_body")
     * @param Item $item
     * @return Item
     */
    public function updateAction(Item $item) {
        $em = $this->getDoctrine()->getManager();
        $em->merge($item);
        $em->flush();

        return $item;
    }

    /**
     * Delete Item by id
     *
     * @Security("has_role('ROLE_SUPER_ADMIN')")
     * @Rest\Delete("/delete/{id}", name="delete_item")
     * @Rest\View(StatusCode = 200)
     * @param Item|null $item
     * @param $id
     * @return JsonResponse
     * @throws EntityNotFoundException
     */
    public function deleteAction(Item $item = null, $id) {
        if (empty($item)) {
            throw new EntityNotFoundException('item with id : ' . $id . ' was not found.');
        }

        $em = $this->getDoctrine()->getManager();
        $em->remove($item);
        $em->flush();

        return new JsonResponse(sprintf("item with id: %s  was removed.", $id), 200);
    }

    /**
     * upload moq
     *
     * @Security("has_role('ROLE_SUPER_ADMIN')")
     * @Rest\POST(name="update_moq", path="/moq/upload")
     * @Rest\View(StatusCode = 200)
     */
    public function uploadMoqAction(Request $request)
    {
        if(!$request->request->get('platform')){
            throw new ParametersException(array('platform'));
        }

        $platform = $request->request->get('platform');

        $file = $request->files->get('file');
        $em = $this->getDoctrine()->getManager();
        $eans = [];

        if ($file && is_readable($file)) {
            $handle = fopen($file, 'r');
            while (($data = fgetcsv($handle)) !== false) {
                if(!empty( trim($data[0]) )){
                    $eans[trim($data[0])] = trim($data[1]);
                }
            }
            fclose($handle);
        }
        /* Should be optimize with a preorder check on request */
        $items = $em->getRepository(Item::class)->findBy(array('ean13' => array_keys($eans), 'platform' => $platform));
        foreach ($items as $item) {
            if ($item->getIsPreorder()) {
                $item->setMoq($eans[$item->getEan13()]);
                $em->merge($item);
            }
        }
        $em->flush();

        return;
    }
}
