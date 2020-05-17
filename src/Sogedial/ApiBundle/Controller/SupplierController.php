<?php

namespace Sogedial\ApiBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Sogedial\ApiBundle\Exception\EntityNotFoundException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use FOS\RestBundle\Controller\Annotations as Rest;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use FOS\RestBundle\Controller\Annotations\QueryParam;
use Sogedial\ApiBundle\Entity\Supplier;

/**
 * Supplier controller.
 *
 * @Security("has_role('ROLE_SUPER_ADMIN')")
 * @Rest\Route(path="/api/supplier")
 */
class SupplierController extends Controller
{
    const LIMIT = 10;

    /**
     * Lists all Supplier entities.
     *
     * @Rest\Get("", name="get_all_supplier")
     * @Rest\View(StatusCode = 200, serializerEnableMaxDepthChecks=true)
     * @QueryParam(name="offset", requirements="\d+", default="0", description="Index de début de la pagination")
     * @QueryParam(name="limit", requirements="\d+", default="10", description="Nombre d'éléments à afficher")
     * @QueryParam(name="orderBy", default=null, description="nom de champ de l'ordre")
     * @QueryParam(name="orderByDesc", default=null, description="nom de champ de l'ordre descendant")
     * @QueryParam(name="filter", default=null, description="filtre sur les champs")
     */
    public function getAllAction($orderBy = null, $orderByDesc = null, $limit = self::LIMIT, $offset = 0, $filter = null)
    {
        $order = 'asc';
        $em = $this->getDoctrine()->getManager();

        if ($orderBy == null) {
            $meta = $em->getClassMetadata(Supplier::class);
            $orderBy = $meta->getSingleIdentifierFieldName();
        }

        if ($orderByDesc != null) {
            $orderBy = $orderByDesc;
            $order = 'desc';
        }

        $filter = ($filter != null) ? json_decode($filter, true) : [];

        return $em->getRepository('SogedialApiBundle:Supplier')->findBy($filter, [$orderBy => $order], $limit, $offset);
    }

    /**
     * Count Supplier available after filter
     *
     * @Rest\Get("/count", name="count_supplier")
     * @Rest\View(StatusCode = 200)
     * @QueryParam(name="filter", default=null, description="filtre sur les champs")
     */
    public function countAction($filter = null)
    {
        $filter = ($filter != null) ? json_decode($filter, true) : [];

        return $this->getDoctrine()->getManager()->getRepository('SogedialApiBundle:Supplier')->getCount($filter);
    }

    /**
     * Finds and displays a Supplier entity.
     *
     * @Rest\Get("/{id}", name="get_supplier")
     * @Rest\View(statusCode = 200, serializerEnableMaxDepthChecks=true)
     */
    public function getAction(Supplier $supplier = null, $id)
    {
        if (empty($supplier)) {
            throw new EntityNotFoundException('supplier with id : '. $id . ' was not found.');
        }

        return $supplier;
    }

    /**
     * create a new Supplier entity.
     *
     * @Rest\Post("/add", name="add_supplier")
     * @Rest\View(StatusCode = 201, serializerEnableMaxDepthChecks=true)
     * @ParamConverter("supplier", converter="fos_rest.request_body")
     */
    public function addAction(Supplier $supplier)
    {
        $em = $this->getDoctrine()->getManager();
        $em->persist($supplier);
        $em->flush();

        return $supplier;
    }

    /**
     * Displays a form to edit an existing Supplier entity.
     *
     * @Rest\Put("/update", name="update_supplier")
     * @Rest\View(StatusCode = 200, serializerEnableMaxDepthChecks=true)
     * @ParamConverter("supplier", converter="fos_rest.request_body")
     */
    public function updateAction(Supplier $supplier)
    {
        $em = $this->getDoctrine()->getManager();
        $em->merge($supplier);
        $em->flush();

        return $supplier;
    }

    /**
     * Delete Supplier by id
     *
     * @Rest\Delete("/delete/{id}", name="delete_supplier")
     * @Rest\View(StatusCode = 200)
     */
    public function deleteAction(Supplier $supplier = null, $id)
    {
        if (empty($supplier)) {
            throw new EntityNotFoundException('supplier with id : '. $id . ' was not found.');
        }

        $em = $this->getDoctrine()->getManager();
        $em->remove($supplier);
        $em->flush();

        return new JsonResponse(sprintf("supplier with id: %s  was removed.", $id), 200);
    }

}
