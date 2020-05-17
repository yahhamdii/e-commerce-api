<?php

namespace Sogedial\ApiBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Sogedial\ApiBundle\Exception\EntityNotFoundException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use FOS\RestBundle\Controller\Annotations as Rest;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use FOS\RestBundle\Controller\Annotations\QueryParam;
use Sogedial\ApiBundle\Entity\Status;

/**
 * Status controller.
 *
 * @Security("has_role('ROLE_SUPER_ADMIN')")
 * @Rest\Route(path="/api/status")
 */
class StatusController extends Controller
{
    const LIMIT = 10;

    /**
     * Lists all Status entities.
     *
     * @Rest\Get("", name="get_all_status")
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
            $meta = $em->getClassMetadata(Status::class);
            $orderBy = $meta->getSingleIdentifierFieldName();
        }

        if ($orderByDesc != null) {
            $orderBy = $orderByDesc;
            $order = 'desc';
        }

        $filter = ($filter != null) ? json_decode($filter, true) : [];

        return $em->getRepository('SogedialApiBundle:Status')->findBy($filter, [$orderBy => $order], $limit, $offset);
    }

    /**
     * Count Status available after filter
     *
     * @Rest\Get("/count", name="count_status")
     * @Rest\View(StatusCode = 200)
     * @QueryParam(name="filter", default=null, description="filtre sur les champs")
     */
    public function countAction($filter = null)
    {
        $filter = ($filter != null) ? json_decode($filter, true) : [];

        return $this->getDoctrine()->getManager()->getRepository('SogedialApiBundle:Status')->getCount($filter);
    }

    /**
     * Finds and displays a Status entity.
     *
     * @Rest\Get("/{id}", name="get_status")
     * @Rest\View(statusCode = 200, serializerEnableMaxDepthChecks=true)
     */
    public function getAction(Status $status = null, $id)
    {
        if (empty($status)) {
            throw new EntityNotFoundException('status with id : '. $id . ' was not found.');
        }

        return $status;
    }

    /**
     * create a new Status entity.
     *
     * @Rest\Post("/add", name="add_status")
     * @Rest\View(StatusCode = 201, serializerEnableMaxDepthChecks=true)
     * @ParamConverter("status", converter="fos_rest.request_body")
     */
    public function addAction(Status $status)
    {
        $em = $this->getDoctrine()->getManager();
        $em->persist($status);
        $em->flush();

        return $status;
    }

    /**
     * Displays a form to edit an existing Status entity.
     *
     * @Rest\Put("/update", name="update_status")
     * @Rest\View(StatusCode = 200, serializerEnableMaxDepthChecks=true)
     * @ParamConverter("status", converter="fos_rest.request_body")
     */
    public function updateAction(Status $status)
    {
        $em = $this->getDoctrine()->getManager();
        $em->merge($status);
        $em->flush();

        return $status;
    }

    /**
     * Delete Status by id
     *
     * @Rest\Delete("/delete/{id}", name="delete_status")
     * @Rest\View(StatusCode = 200)
     */
    public function deleteAction(Status $status = null, $id)
    {
        if (empty($status)) {
            throw new EntityNotFoundException('status with id : '. $id . ' was not found.');
        }

        $em = $this->getDoctrine()->getManager();
        $em->remove($status);
        $em->flush();

        return new JsonResponse(sprintf("status with id: %s  was removed.", $id), 200);
    }

}
