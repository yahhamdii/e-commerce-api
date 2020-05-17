<?php

namespace Sogedial\ApiBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Sogedial\ApiBundle\Exception\EntityNotFoundException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use FOS\RestBundle\Controller\Annotations as Rest;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use FOS\RestBundle\Controller\Annotations\QueryParam;
use Sogedial\ApiBundle\Entity\Manufacturer;

/**
 * Manufacturer controller.
 *
 * @Security("has_role('ROLE_SUPER_ADMIN')")
 * @Rest\Route(path="/api/manufacturer")
 */
class ManufacturerController extends Controller
{
    const LIMIT = 10;

    /**
     * Lists all Manufacturer entities.
     *
     * @Rest\Get("", name="get_all_manufacturer")
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
            $meta = $em->getClassMetadata(Manufacturer::class);
            $orderBy = $meta->getSingleIdentifierFieldName();
        }

        if ($orderByDesc != null) {
            $orderBy = $orderByDesc;
            $order = 'desc';
        }

        $filter = ($filter != null) ? json_decode($filter, true) : [];

        return $em->getRepository('SogedialApiBundle:Manufacturer')->findBy($filter, [$orderBy => $order], $limit, $offset);
    }

    /**
     * Count Manufacturer available after filter
     *
     * @Rest\Get("/count", name="count_manufacturer")
     * @Rest\View(StatusCode = 200)
     * @QueryParam(name="filter", default=null, description="filtre sur les champs")
     */
    public function countAction($filter = null)
    {
        $filter = ($filter != null) ? json_decode($filter, true) : [];

        return $this->getDoctrine()->getManager()->getRepository('SogedialApiBundle:Manufacturer')->getCount($filter);
    }

    /**
     * Finds and displays a Manufacturer entity.
     *
     * @Rest\Get("/{id}", name="get_manufacturer")
     * @Rest\View(statusCode = 200, serializerEnableMaxDepthChecks=true)
     */
    public function getAction(Manufacturer $manufacturer = null, $id)
    {
        if (empty($manufacturer)) {
            throw new EntityNotFoundException('manufacturer with id : '. $id . ' was not found.');
        }

        return $manufacturer;
    }

    /**
     * create a new Manufacturer entity.
     *
     * @Rest\Post("/add", name="add_manufacturer")
     * @Rest\View(StatusCode = 201, serializerEnableMaxDepthChecks=true)
     * @ParamConverter("manufacturer", converter="fos_rest.request_body")
     */
    public function addAction(Manufacturer $manufacturer)
    {
        $em = $this->getDoctrine()->getManager();
        $em->persist($manufacturer);
        $em->flush();

        return $manufacturer;
    }

    /**
     * Displays a form to edit an existing Manufacturer entity.
     *
     * @Rest\Put("/update", name="update_manufacturer")
     * @Rest\View(StatusCode = 200, serializerEnableMaxDepthChecks=true)
     * @ParamConverter("manufacturer", converter="fos_rest.request_body")
     */
    public function updateAction(Manufacturer $manufacturer)
    {
        $em = $this->getDoctrine()->getManager();
        $em->merge($manufacturer);
        $em->flush();

        return $manufacturer;
    }

    /**
     * Delete Manufacturer by id
     *
     * @Rest\Delete("/delete/{id}", name="delete_manufacturer")
     * @Rest\View(StatusCode = 200)
     */
    public function deleteAction(Manufacturer $manufacturer = null, $id)
    {
        if (empty($manufacturer)) {
            throw new EntityNotFoundException('manufacturer with id : '. $id . ' was not found.');
        }

        $em = $this->getDoctrine()->getManager();
        $em->remove($manufacturer);
        $em->flush();

        return new JsonResponse(sprintf("manufacturer with id: %s  was removed.", $id), 200);
    }

}
