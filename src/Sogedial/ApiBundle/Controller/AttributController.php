<?php

namespace Sogedial\ApiBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Sogedial\ApiBundle\Exception\EntityNotFoundException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use FOS\RestBundle\Controller\Annotations as Rest;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use FOS\RestBundle\Controller\Annotations\QueryParam;
use Sogedial\ApiBundle\Entity\Attribut;

/**
 * Attribut controller.
 *
 * @Rest\Route(path="/api/attribut")
 */
class AttributController extends Controller
{
    const LIMIT = 10;

    /**
     * Lists all Attribut entities.
     *
     * @Rest\Get("", name="get_all_attribut")
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
            $meta = $em->getClassMetadata(Attribut::class);
            $orderBy = $meta->getSingleIdentifierFieldName();
        }

        if ($orderByDesc != null) {
            $orderBy = $orderByDesc;
            $order = 'desc';
        }

        $filter = ($filter != null) ? json_decode($filter, true) : [];

        return $em->getRepository('SogedialApiBundle:Attribut')->findBy($filter, [$orderBy => $order], $limit, $offset);
    }

    /**
     * Count Attribut available after filter
     *
     * @Rest\Get("/count", name="count_attribut")
     * @Rest\View(StatusCode = 200)
     * @QueryParam(name="filter", default=null, description="filtre sur les champs")
     */
    public function countAction($filter = null)
    {
        $filter = ($filter != null) ? json_decode($filter, true) : [];

        return $this->getDoctrine()->getManager()->getRepository('SogedialApiBundle:Attribut')->getCount($filter);
    }

    /**
     * Finds and displays a Attribut entity.
     *
     * @Rest\Get("/{id}", name="get_attribut")
     * @Rest\View(statusCode = 200, serializerEnableMaxDepthChecks=true)
     */
    public function getAction(Attribut $attribut = null, $id)
    {
        if (empty($attribut)) {
            throw new EntityNotFoundException('attribut with id : ' . $id . ' was not found.');
        }

        return $attribut;
    }

    /**
     * create a new Attribut entity.
     *
     * @Rest\Post("/add", name="add_attribut")
     * @Rest\View(StatusCode = 201, serializerEnableMaxDepthChecks=true)
     * @ParamConverter("attribut", converter="fos_rest.request_body")
     */
    public function addAction(Attribut $attribut)
    {
        $em = $this->getDoctrine()->getManager();
        $em->persist($attribut);
        $em->flush();

        return $attribut;
    }

    /**
     * Displays a form to edit an existing Attribut entity.
     *
     * @Rest\Put("/update", name="update_attribut")
     * @Rest\View(StatusCode = 200, serializerEnableMaxDepthChecks=true)
     * @ParamConverter("attribut", converter="fos_rest.request_body")
     */
    public function updateAction(Attribut $attribut)
    {
        $em = $this->getDoctrine()->getManager();
        $em->merge($attribut);
        $em->flush();

        return $attribut;
    }

    /**
     * Delete Attribut by id
     *
     * @Rest\Delete("/delete/{id}", name="delete_attribut")
     * @Rest\View(StatusCode = 200)
     */
    public function deleteAction(Attribut $attribut = null, $id)
    {
        if (empty($attribut)) {
            throw new EntityNotFoundException('attribut with id : ' . $id . ' was not found.');
        }

        $em = $this->getDoctrine()->getManager();
        $em->remove($attribut);
        $em->flush();

        return new JsonResponse(sprintf("attribut with id: %s  was removed.", $id), 200);
    }
    

}
