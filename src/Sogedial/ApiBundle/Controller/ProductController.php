<?php

namespace Sogedial\ApiBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use FOS\RestBundle\Controller\Annotations as Rest;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sogedial\ApiBundle\Exception\Exception;
use FOS\RestBundle\Controller\Annotations\QueryParam;
use Sogedial\ApiBundle\Entity\Product;

/**
 * Product controller.
 *
 * @Rest\Route(path="/api/product")
 */
class ProductController extends Controller {

    const LIMIT = 10;

    /**
     * Lists all Product entities.
     *
     * @Rest\Get("", name="get_all_product")
     * @Rest\View(statusCode = 200, serializerEnableMaxDepthChecks=true)
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
            $meta = $em->getClassMetadata(Product::class);
            $orderBy = $meta->getSingleIdentifierFieldName();
        }
        if ($orderByDesc != null) {
            $orderBy = $orderByDesc;
            $order = 'desc';
        }
        $filter = ($filter != null) ? json_decode($filter, true) : [];
        $repo = $this->get('sogedial.repository_injecter')->getRepository(Product::class);
        return $repo->findBy($filter, [$orderBy => $order], $limit, $offset);
    }

    /**
     * create a new Product entity.
     *
     * @Rest\Post("/add", name="add_product")
     * @Rest\View(StatusCode = 201)
     * @ParamConverter("product", converter="fos_rest.request_body")
     */
    public function addAction(Product $product) {
        try {
            $em = $this->getDoctrine()->getManager();
            $em->persist($product);
            $em->flush();
            $em->refresh($product);
        } catch (Exception $exc) {
            throw new \Sogedial\ApiBundle\Exception\InsertEntityException;
        }
        return $product;
    }


    /**
     * Count Product available after filter
     *
     * @Rest\Get("/count", name="count_product")
     * @Rest\View(StatusCode = 200)
     * @QueryParam(name="filter", default=null, description="filtre sur les champs")
     */
    public function countAction($filter = null) {
        $filter = ($filter != null) ? json_decode($filter, true) : [];

        return $this->get('sogedial.repository_injecter')->getRepository('SogedialApiBundle:Product')->getCount($filter);
    }


    /**
     * Finds and displays a Product entity.
     *
     * @Rest\Get("/{id}", name="get_product")
     * @Rest\View(statusCode = 200, serializerEnableMaxDepthChecks=true)
     */
    public function getAction($id) {
        try {
            $em = $this->getDoctrine()->getManager();
            $product = $em->getRepository('SogedialApiBundle:Product')->find($id);
//            dump($product);
//            die;
        } catch (Exception $exc) {
            throw new \Sogedial\ApiBundle\Exception\EntityNotFoundException;
        }

        return $product;
    }

    /**
     * Displays a form to edit an existing Product entity.
     *
     * @Rest\Put("/update", name="update_product")
     * @Rest\View(StatusCode = 200)
     * @ParamConverter("product", converter="fos_rest.request_body")
     */
    public function updateAction(Product $product) {
        try {
            $em = $this->getDoctrine()->getManager();
            $em->merge($product);
            $em->flush();
        } catch (Exception $exc) {
            throw new \Sogedial\ApiBundle\Exception\UpdateEntityException;
        }
        return $product;
    }

    /**
     * Delete Product by id
     *
     * @Rest\Delete("/delete/{id}", name="delete_product")
     * @Rest\View(StatusCode = 200)
     */
    public function deleteAction(Product $product, Request $request) {
        $em = $this->getDoctrine()->getManager();
        $id = $request->attributes->get('id');
        try {
            $em->remove($product);
            $em->flush();
            $this->get('session')->getFlashBag()->add('success', 'The Product was deleted successfully');
        } catch (Exception $exc) {
            throw new \Sogedial\ApiBundle\Exception\DeleteEntityException;
        }

        return "entity $id was removed";
    }

}
