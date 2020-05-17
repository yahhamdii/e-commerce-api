<?php

namespace Sogedial\ApiBundle\Controller;

use Sogedial\ApiBundle\Exception\EntityNotFoundException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use FOS\RestBundle\Controller\Annotations as Rest;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use FOS\RestBundle\Controller\Annotations\QueryParam;
use Sogedial\ApiBundle\Entity\GroupAttribut;

/**
 * GroupAttribut controller.
 *
 * @Rest\Route(path="/api/group_attribut")
 */
class GroupAttributController extends Controller
{
    const LIMIT = 10;

    /**
     * Lists all GroupAttribut entities.
     *
     * @Rest\Get("", name="get_all_group_attribut")
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
            $meta = $em->getClassMetadata(GroupAttribut::class);
            $orderBy = $meta->getSingleIdentifierFieldName();
        }

        if ($orderByDesc != null) {
            $orderBy = $orderByDesc;
            $order = 'desc';
        }

        $filter = ($filter != null) ? json_decode($filter, true) : [];

        return $em->getRepository('SogedialApiBundle:GroupAttribut')->findBy($filter, [$orderBy => $order], $limit, $offset);
    }

    /**
     * Count GroupAttribut available after filter
     *
     * @Rest\Get("/count", name="count_group_attribut")
     * @Rest\View(StatusCode = 200)
     * @QueryParam(name="filter", default=null, description="filtre sur les champs")
     */
    public function countAction($filter = null)
    {
        $filter = ($filter != null) ? json_decode($filter, true) : [];

        return $this->getDoctrine()->getManager()->getRepository('SogedialApiBundle:GroupAttribut')->getCount($filter);
    }

    /**
     * Finds and displays a GroupAttribut entity.
     *
     * @Rest\Get("/{id}", name="get_group_attribut")
     * @Rest\View(statusCode = 200, serializerEnableMaxDepthChecks=true)
     */
    public function getAction(GroupAttribut $groupAttribut = null, $id)
    {
        if (empty($groupAttribut)) {
            throw new EntityNotFoundException('groupAttribut with id : '. $id . ' was not found.');
        }

        return $groupAttribut;
    }

    /**
     * create a new GroupAttribut entity.
     *
     * @Rest\Post("/add", name="add_group_attribut")
     * @Rest\View(StatusCode = 201, serializerEnableMaxDepthChecks=true)
     * @ParamConverter("groupAttribut", converter="fos_rest.request_body")
     */
    public function addAction(GroupAttribut $groupAttribut)
    {
        $em = $this->getDoctrine()->getManager();
        $em->persist($groupAttribut);
        $em->flush();

        return $groupAttribut;
    }

    /**
     * Displays a form to edit an existing GroupAttribut entity.
     *
     * @Rest\Put("/update", name="update_group_attribut")
     * @Rest\View(StatusCode = 200, serializerEnableMaxDepthChecks=true)
     * @ParamConverter("groupAttribut", converter="fos_rest.request_body")
     */
    public function updateAction(GroupAttribut $groupAttribut)
    {
        $em = $this->getDoctrine()->getManager();
        $em->merge($groupAttribut);
        $em->flush();

        return $groupAttribut;
    }

    /**
     * Delete GroupAttribut by id
     *
     * @Rest\Delete("/delete/{id}", name="delete_group_attribut")
     * @Rest\View(StatusCode = 200)
     */
    public function deleteAction(GroupAttribut $groupAttribut = null, $id)
    {
        if (empty($groupAttribut)) {
            throw new EntityNotFoundException('groupAttribut with id : '. $id . ' was not found.');
        }

        $em = $this->getDoctrine()->getManager();
        $em->remove($groupAttribut);
        $em->flush();

        return new JsonResponse(sprintf("groupAttribut with id: %s  was removed.", $id), 200);
    }

}
