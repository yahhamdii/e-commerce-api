<?php

namespace Sogedial\ApiBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Sogedial\ApiBundle\Exception\EntityNotFoundException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use FOS\RestBundle\Controller\Annotations as Rest;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use FOS\RestBundle\Controller\Annotations\QueryParam;
use Sogedial\ApiBundle\Entity\Brand;

/**
 * Brand controller.
 *
 * @Security("has_role('ROLE_SUPER_ADMIN')")
 * @Rest\Route(path="/api/brand")
 */
class BrandController extends Controller
{
    const LIMIT = 10;

    /**
     * Lists all Brand entities.
     *
     * @Rest\Get("", name="get_all_brand")
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
            $meta = $em->getClassMetadata(Brand::class);
            $orderBy = $meta->getSingleIdentifierFieldName();
        }

        if ($orderByDesc != null) {
            $orderBy = $orderByDesc;
            $order = 'desc';
        }

        $filter = ($filter != null) ? json_decode($filter, true) : [];

        return $em->getRepository('SogedialApiBundle:Brand')->findBy($filter, [$orderBy => $order], $limit, $offset);
    }

    /**
     * Count Brand available after filter
     *
     * @Rest\Get("/count", name="count_brand")
     * @Rest\View(StatusCode = 200)
     * @QueryParam(name="filter", default=null, description="filtre sur les champs")
     */
    public function countAction($filter = null)
    {
        $filter = ($filter != null) ? json_decode($filter, true) : [];

        return $this->getDoctrine()->getManager()->getRepository('SogedialApiBundle:Brand')->getCount($filter);
    }

    /**
     * Finds and displays a Brand entity.
     *
     * @Rest\Get("/{id}", name="get_brand")
     * @Rest\View(statusCode = 200, serializerEnableMaxDepthChecks=true)
     */
    public function getAction(Brand $brand = null, $id)
    {
        if (empty($brand)) {
            throw new EntityNotFoundException('brand with id : ' . $id . ' was not found.');
        }

        return $brand;
    }

    /**
     * create a new Brand entity.
     *
     * @Rest\Post("/add", name="add_brand")
     * @Rest\View(StatusCode = 201, serializerEnableMaxDepthChecks=true)
     * @ParamConverter("brand", converter="fos_rest.request_body")
     */
    public function addAction(Brand $brand)
    {
        $em = $this->getDoctrine()->getManager();
        $em->persist($brand);
        $em->flush();

        return $brand;
    }

    /**
     * Displays a form to edit an existing Brand entity.
     *
     * @Rest\Put("/update", name="update_brand")
     * @Rest\View(StatusCode = 200, serializerEnableMaxDepthChecks=true)
     * @ParamConverter("brand", converter="fos_rest.request_body")
     */
    public function updateAction(Brand $brand)
    {
        $em = $this->getDoctrine()->getManager();
        $em->merge($brand);
        $em->flush();

        return $brand;
    }

    /**
     * Delete Brand by id
     *
     * @Rest\Delete("/delete/{id}", name="delete_brand")
     * @Rest\View(StatusCode = 200)
     */
    public function deleteAction(Brand $brand = null, $id)
    {
        if (empty($brand)) {
            throw new EntityNotFoundException('brand with id : ' . $id . ' was not found.');
        }

        $em = $this->getDoctrine()->getManager();
        $em->remove($brand);
        $em->flush();

        return new JsonResponse(sprintf("brand with id: %s  was removed.", $id), 200);
    }

}
