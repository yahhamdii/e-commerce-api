<?php

namespace Sogedial\ApiBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Sogedial\ApiBundle\Exception\EntityNotFoundException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use FOS\RestBundle\Controller\Annotations as Rest;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use FOS\RestBundle\Controller\Annotations\QueryParam;
use Sogedial\ApiBundle\Entity\DeliveryMode;

/**
 * DeliveryMode controller.
 *
 * @Security("has_role('ROLE_SUPER_ADMIN')")
 * @Rest\Route(path="/api/delivery_mode")
 */
class DeliveryModeController extends Controller
{
    const LIMIT = 10;

    /**
     * Lists all DeliveryMode entities.
     *
     * @Rest\Get("", name="get_all_delivery_mode")
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
            $meta = $em->getClassMetadata(DeliveryMode::class);
            $orderBy = $meta->getSingleIdentifierFieldName();
        }

        if ($orderByDesc != null) {
            $orderBy = $orderByDesc;
            $order = 'desc';
        }

        $filter = ($filter != null) ? json_decode($filter, true) : [];

        return $em->getRepository('SogedialApiBundle:DeliveryMode')->findBy($filter, [$orderBy => $order], $limit, $offset);
    }

    /**
     * Count DeliveryMode available after filter
     *
     * @Rest\Get("/count", name="count_delivery_mode")
     * @Rest\View(StatusCode = 200)
     * @QueryParam(name="filter", default=null, description="filtre sur les champs")
     */
    public function countAction($filter = null)
    {
        $filter = ($filter != null) ? json_decode($filter, true) : [];

        return $this->getDoctrine()->getManager()->getRepository('SogedialApiBundle:DeliveryMode')->getCount($filter);
    }

    /**
     * Finds and displays a DeliveryMode entity.
     *
     * @Rest\Get("/{id}", name="get_delivery_mode")
     * @Rest\View(statusCode = 200, serializerEnableMaxDepthChecks=true)
     */
    public function getAction(DeliveryMode $deliveryMode = null, $id)
    {
        if (empty($deliveryMode)) {
            throw new EntityNotFoundException('deliveryMode with id : '. $id . ' was not found.');
        }

        return $deliveryMode;
    }

    /**
     * create a new DeliveryMode entity.
     *
     * @Rest\Post("/add", name="add_delivery_mode")
     * @Rest\View(StatusCode = 201, serializerEnableMaxDepthChecks=true)
     * @ParamConverter("deliveryMode", converter="fos_rest.request_body")
     */
    public function addAction(DeliveryMode $deliveryMode)
    {
        $em = $this->getDoctrine()->getManager();
        $em->persist($deliveryMode);
        $em->flush();

        return $deliveryMode;
    }

    /**
     * Displays a form to edit an existing DeliveryMode entity.
     *
     * @Rest\Put("/update", name="update_delivery_mode")
     * @Rest\View(StatusCode = 200, serializerEnableMaxDepthChecks=true)
     * @ParamConverter("deliveryMode", converter="fos_rest.request_body")
     */
    public function updateAction(DeliveryMode $deliveryMode)
    {
        $em = $this->getDoctrine()->getManager();
        $em->merge($deliveryMode);
        $em->flush();

        return $deliveryMode;
    }

    /**
     * Delete DeliveryMode by id
     *
     * @Rest\Delete("/delete/{id}", name="delete_delivery_mode")
     * @Rest\View(StatusCode = 200)
     */
    public function deleteAction(DeliveryMode $deliveryMode = null, $id)
    {
        if (empty($deliveryMode)) {
            throw new EntityNotFoundException('deliveryMode with id : '. $id . ' was not found.');
        }

        $em = $this->getDoctrine()->getManager();
        $em->remove($deliveryMode);
        $em->flush();

        return new JsonResponse(sprintf("deliveryMode with id: %s  was removed.", $id), 200);
    }

}
