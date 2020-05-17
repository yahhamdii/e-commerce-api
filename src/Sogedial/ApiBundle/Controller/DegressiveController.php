<?php

namespace Sogedial\ApiBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Sogedial\ApiBundle\Exception\EntityNotFoundException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use FOS\RestBundle\Controller\Annotations as Rest;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use FOS\RestBundle\Controller\Annotations\QueryParam;
use Sogedial\ApiBundle\Entity\Degressive;

/**
 * Degressive controller.
 *
 * @Security("has_role('ROLE_SUPER_ADMIN')")
 * @Rest\Route(path="/api/degressive")
 */
class DegressiveController extends Controller
{
    const LIMIT = 10;

    /**
     * Lists all Degressive entities.
     *
     * @Rest\Get("", name="get_all_degressive")
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
            $meta = $em->getClassMetadata(Degressive::class);
            $orderBy = $meta->getSingleIdentifierFieldName();
        }

        if ($orderByDesc != null) {
            $orderBy = $orderByDesc;
            $order = 'desc';
        }

        $filter = ($filter != null) ? json_decode($filter, true) : [];

        return $em->getRepository('SogedialApiBundle:Degressive')->findBy($filter, [$orderBy => $order], $limit, $offset);
    }

    /**
     * Count Degressive available after filter
     *
     * @Rest\Get("/count", name="count_degressive")
     * @Rest\View(StatusCode = 200)
     * @QueryParam(name="filter", default=null, description="filtre sur les champs")
     */
    public function countAction($filter = null)
    {
        $filter = ($filter != null) ? json_decode($filter, true) : [];

        return $this->getDoctrine()->getManager()->getRepository('SogedialApiBundle:Degressive')->getCount($filter);
    }

    /**
     * Finds and displays a Degressive entity.
     *
     * @Rest\Get("/{id}", name="get_degressive")
     * @Rest\View(statusCode = 200, serializerEnableMaxDepthChecks=true)
     */
    public function getAction(Degressive $degressive = null, $id)
    {
        if (empty($degressive)) {
            throw new EntityNotFoundException('degressive with id : '. $id . ' was not found.');
        }

        return $degressive;
    }

    /**
     * create a new Degressive entity.
     *
     * @Rest\Post("/add", name="add_degressive")
     * @Rest\View(StatusCode = 201, serializerEnableMaxDepthChecks=true)
     * @ParamConverter("degressive", converter="fos_rest.request_body")
     */
    public function addAction(Degressive $degressive)
    {
        $em = $this->getDoctrine()->getManager();
        $em->persist($degressive);
        $em->flush();

        return $degressive;
    }

    /**
     * Displays a form to edit an existing Degressive entity.
     *
     * @Rest\Put("/update", name="update_degressive")
     * @Rest\View(StatusCode = 200, serializerEnableMaxDepthChecks=true)
     * @ParamConverter("degressive", converter="fos_rest.request_body")
     */
    public function updateAction(Degressive $degressive)
    {
        $em = $this->getDoctrine()->getManager();
        $em->merge($degressive);
        $em->flush();

        return $degressive;
    }

    /**
     * Delete Degressive by id
     *
     * @Rest\Delete("/delete/{id}", name="delete_degressive")
     * @Rest\View(StatusCode = 200)
     */
    public function deleteAction(Degressive $degressive = null, $id)
    {
        if (empty($degressive)) {
            throw new EntityNotFoundException('degressive with id : '. $id . ' was not found.');
        }

        $em = $this->getDoctrine()->getManager();
        $em->remove($degressive);
        $em->flush();

        return new JsonResponse(sprintf("degressive with id: %s  was removed.", $id), 200);
    }

}
