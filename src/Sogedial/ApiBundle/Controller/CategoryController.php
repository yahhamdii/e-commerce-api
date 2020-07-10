<?php

namespace Sogedial\ApiBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Sogedial\ApiBundle\Exception\EntityNotFoundException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use FOS\RestBundle\Controller\Annotations as Rest;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use FOS\RestBundle\Controller\Annotations\QueryParam;
use Sogedial\ApiBundle\Entity\Category;
use Sogedial\ApiBundle\Entity\GroupItem;
use Sogedial\ApiBundle\Entity\Brand;
use Sogedial\ApiBundle\Exception\ParametersException;
use Sogedial\OAuthBundle\Entity\UserCustomer;

/**
 * Category controller.
 *
 * @Rest\Route(path="/api/category")
 */
class CategoryController extends Controller {

    const LIMIT = 10;

    /**
     * Lists all Category entities.
     *
     * @Rest\Get("", name="get_all_category")
     * @Rest\View(StatusCode = 200, serializerEnableMaxDepthChecks=true, serializerGroups={"list"})
     * @QueryParam(name="offset", requirements="\d+", default="0", description="Index de début de la pagination")
     * @QueryParam(name="limit", requirements="\d+", default="10", description="Nombre d'éléments à afficher")
     * @QueryParam(name="orderBy", default=null, description="nom de champ de l'ordre")
     * @QueryParam(name="orderByDesc", default=null, description="nom de champ de l'ordre descendant")
     * @QueryParam(name="filter", default=null, description="filtre sur les champs")
     */
    public function getAllAction($orderBy = null, $orderByDesc = null, $limit = self::LIMIT, $offset = 0, $filter = null) {
        $order = 'asc';
        $em = $this->getDoctrine()->getManager();

        if ($orderBy == null) {
            $meta = $em->getClassMetadata(Category::class);
            $orderBy = $meta->getSingleIdentifierFieldName();
        }

        if ($orderByDesc != null) {
            $orderBy = $orderByDesc;
            $order = 'desc';
        }

        $filter = ($filter != null) ? json_decode($filter, true) : [];
        if($this->getUser() instanceof UserCustomer && !isset($filter['platform'])){
            throw new ParametersException(array('platform'));
        } 

        $repo = $this->get('sogedial.repository_injecter')->getRepository('SogedialApiBundle:Category');
        return $repo->findBy($filter, [$orderBy => $order], $limit, $offset);
    }

    /**
     * Count Category available after filter
     *
     * @Rest\Get("/count", name="count_category")
     * @Rest\View(StatusCode = 200)
     * @QueryParam(name="filter", default=null, description="filtre sur les champs")
     */
    public function countAction($filter = null) {
        $filter = ($filter != null) ? json_decode($filter, true) : [];
        if($this->getUser() instanceof UserCustomer && !isset($filter['platform'])){
            throw new ParametersException(array('platform'));
        }

        $repo = $this->get('sogedial.repository_injecter')->getRepository('SogedialApiBundle:Category');
        return $repo->getCount($filter);
    }

    /**
     * Finds and displays a Category entity.
     *
     * @Rest\Get("/{id}", name="get_category")
     * @Rest\View(statusCode = 200, serializerEnableMaxDepthChecks=true, serializerGroups={"detail"})
     */
    public function getAction(Category $category = null, $id) {
        if (empty($category)) {
            throw new EntityNotFoundException('category with id : ' . $id . ' was not found.');
        }

        return $category;
    }

    /**
     * create a new Category entity.
     *
     * @Rest\Post("/add", name="add_category")
     * @Rest\View(StatusCode = 201, serializerEnableMaxDepthChecks=true, serializerGroups={"add"})
     * @ParamConverter("category", converter="fos_rest.request_body")
     */
    public function addAction(Category $category) {
        $em = $this->getDoctrine()->getManager();
        $em->persist($category);
        $em->flush();

        return $category;
    }

    /**
     * Displays a form to edit an existing Category entity.
     *
     * @Rest\Put("/update", name="update_category")
     * @Rest\View(StatusCode = 200, serializerEnableMaxDepthChecks=true, serializerGroups={"update"})
     * @ParamConverter("category", converter="fos_rest.request_body")
     */
    public function updateAction(Category $category) {
        $em = $this->getDoctrine()->getManager();
        $em->merge($category);
        $em->flush();

        return $category;
    }

    /**
     * Delete Category by id
     *
     * @Rest\Delete("/delete/{id}", name="delete_category")
     * @Rest\View(StatusCode = 200)
     */
    public function deleteAction(Category $category = null, $id) {
        if (empty($category)) {
            throw new EntityNotFoundException('category with id : ' . $id . ' was not found.');
        }

        $em = $this->getDoctrine()->getManager();
        $em->remove($category);
        $em->flush();

        return new JsonResponse(sprintf("category with id: %s  was removed.", $id), 200);
    }

     /**
     * get Categories by Brand (DEPRECATED)
     *
     * @Rest\Get("/brand/{brand}", name="get_all_by_brand")     
     * @Rest\View(StatusCode = 200,serializerEnableMaxDepthChecks=true, serializerGroups={"listitems"})
     * @QueryParam(name="status", default=null, description="Filter uniquement les groupes selon le status renseigné")     
     */
    public function brandAction(Brand $brand, $status = '') {

        $groupItemsRepo = $this->get('sogedial.repository_injecter')->getRepository(GroupItem::class);
        $groupItems = $groupItemsRepo->getByBrand($brand, $status);

        $repo = $this->get('sogedial.repository_injecter')->getRepository(Category::class);

        $categories = $repo->getCategoriesByGroupItems($groupItems, true, true);
        return $categories;
    }
}