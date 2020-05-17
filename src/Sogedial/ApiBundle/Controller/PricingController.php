<?php

namespace Sogedial\ApiBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Sogedial\ApiBundle\Entity\Pricing;
use Sogedial\ApiBundle\Exception\EntityNotFoundException;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use FOS\RestBundle\Controller\Annotations as Rest;


/**
 * Pricing controller.
 *
 * @Rest\Route(path="/api/pricing")
 * @Security("has_role('ROLE_SUPER_ADMIN')")
 */
class PricingController extends Controller
{
    const LIMIT = 60;

    /**
     * Lists all Pricing entities.
     *
     * @Rest\Get("", name="get_all_pricing")
     * @Rest\View(StatusCode = 200, serializerEnableMaxDepthChecks=true, serializerGroups={"list_pricing"})
     * @Rest\QueryParam(name="offset", requirements="\d+", default="0", description="Index de début de la pagination")
     * @Rest\QueryParam(name="limit", requirements="\d+", default="10", description="Nombre d'éléments à afficher")
     * @Rest\QueryParam(name="orderBy", default=null, description="nom de champ de l'ordre")
     * @Rest\QueryParam(name="orderByDesc", default=null, description="nom de champ de l'ordre descendant")
     * @Rest\QueryParam(name="filter", default=null, description="filtre sur les champs")
     */
    public function getAllAction($orderBy = null, $orderByDesc = null, $limit = self::LIMIT, $offset = 0, $filter = null)
    {
        $order = 'asc';
        $em = $this->getDoctrine()->getManager();

        if ($orderBy == null) {
            $meta = $em->getClassMetadata(Pricing::class);
            $orderBy = $meta->getSingleIdentifierFieldName();
        }

        if ($orderByDesc != null) {
            $orderBy = $orderByDesc;
            $order = 'desc';
        }

        $filter = ($filter != null) ? json_decode($filter, true) : [];

        $repo = $this->get('sogedial.repository_injecter')->getRepository(Pricing::class);
        return $repo->findBy($filter, [$orderBy => $order], $limit, $offset);
    }

    /**
     * Count Princing available after filter
     *
     * @Rest\Get("/count", name="count_pricing")
     * @Rest\View(StatusCode = 200)
     * @Rest\QueryParam(name="filter", default=null, description="filtre sur les champs")
     */
    public function countAction($filter = null)
    {
        $repoInjector = $this->get('sogedial.repository_injecter');
        $filter = ($filter != null) ? json_decode($filter, true) : [];

        return $repoInjector->getRepository('SogedialApiBundle:Pricing')->getCount($filter);
    }

    /**
     * Finds and displays a Pricing entity.
     *
     * @Rest\Get("/{id}", name="get_pricing")
     * @Rest\View(statusCode = 200, serializerEnableMaxDepthChecks=true, serializerGroups={"list_pricing"})
     */
    public function getAction(Pricing $pricing = null, $id)
    {
        if (empty($pricing)) {
            throw new EntityNotFoundException('pricing with id : ' . $id . ' was not found.');
        }

        return $pricing;
    }


    /**
     * create a new Pricing entity.
     *
     * @Rest\Post("/add", name="add_pricing")
     * @Rest\View(StatusCode = 201, serializerEnableMaxDepthChecks=true, serializerGroups={"list_pricing"})
     * @ParamConverter("pricing", converter="fos_rest.request_body")
     */
    public function addAction(Pricing $pricing)
    {
        $em = $this->getDoctrine()->getManager();
        $em->persist($pricing);
        $em->flush();

        return $pricing;
    }


    /**
     * Update pricing entity
     *
     * @Rest\Put("/update", name="update_pricing")
     * @Rest\View(StatusCode = 200, serializerEnableMaxDepthChecks=true, serializerGroups={"list_pricing"})
     * @ParamConverter("pricing", converter="fos_rest.request_body")
     */
    public function updateAction(Pricing $pricing)
    {
        $em = $this->getDoctrine()->getManager();
        $em->merge($pricing);
        $em->flush();

        return $pricing;
    }


    /**
     * Delete Pricing by id
     *
     * @Rest\Delete("/delete/{id}", name="delete_pricing")
     * @Rest\View(StatusCode = 200)
     */
    public function deleteAction(Pricing $pricing = null, $id)
    {
        if (empty($pricing)) {
            throw new EntityNotFoundException('credit with id : ' . $id . ' was not found.');
        }

        $em = $this->getDoctrine()->getManager();
        $em->remove($pricing);
        $em->flush();

        return new JsonResponse(sprintf("pricing with id: %s  was removed.", $id), 200);
    }

}