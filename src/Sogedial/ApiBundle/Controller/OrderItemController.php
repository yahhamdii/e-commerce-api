<?php

namespace Sogedial\ApiBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Sogedial\ApiBundle\Exception\EntityNotFoundException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use FOS\RestBundle\Controller\Annotations as Rest;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use FOS\RestBundle\Controller\Annotations\QueryParam;
use Sogedial\ApiBundle\Entity\OrderItem;

/**
 * OrderItem controller.
 *
 * @Rest\Route(path="/api/order_item")
 */
class OrderItemController extends Controller
{
    const LIMIT = 60;

    /**
     * Lists all OrderItem entities.
     *
     * @Rest\Get("", name="get_all_order_item")
     * @Rest\View(StatusCode = 200, serializerEnableMaxDepthChecks=true)
     * @QueryParam(name="offset", requirements="\d+", default="0", description="Index de début de la pagination")
     * @QueryParam(name="limit", requirements="\d+", default="60", description="Nombre d'éléments à afficher")
     * @QueryParam(name="orderBy", default=null, description="nom de champ de l'ordre")
     * @QueryParam(name="orderByDesc", default=null, description="nom de champ de l'ordre descendant")
     * @QueryParam(name="filter", default=null, description="filtre sur les champs")
     */
    public function getAllAction($orderBy = null, $orderByDesc = null, $limit = self::LIMIT, $offset = 0, $filter = null)
    {
        $order = 'asc';
        $em = $this->getDoctrine()->getManager();
        $repo = $this->get('sogedial.repository_injecter');

        if ($orderBy == null) {
            $meta = $em->getClassMetadata(OrderItem::class);
            $orderBy = $meta->getSingleIdentifierFieldName();
        }

        if ($orderByDesc != null) {
            $orderBy = $orderByDesc;
            $order = 'desc';
        }

        $filter = ($filter != null) ? json_decode($filter, true) : [];

        return $repo->getRepository(OrderItem::class)->findBy($filter, [$orderBy => $order], $limit, $offset);
    }

    /**
     * Count OrderItem available after filter
     *
     * @Rest\Get("/count", name="count_order_item")
     * @Rest\View(StatusCode = 200)
     * @QueryParam(name="filter", default=null, description="filtre sur les champs")
     */
    public function countAction($filter = null)
    {
        $filter = ($filter != null) ? json_decode($filter, true) : [];
        $repo = $this->get('sogedial.repository_injecter');

        return $repo->getRepository(OrderItem::class)->getCount($filter);
    }

    /**
     * Finds and displays a OrderItem entity.
     *
     * @Security("has_role('ROLE_SUPER_ADMIN')")
     * @Rest\Get("/{id}", name="get_order_item")
     * @Rest\View(statusCode = 200, serializerEnableMaxDepthChecks=true)
     */
    public function getAction(OrderItem $orderItem = null, $id)
    {
        if (empty($orderItem)) {
            throw new EntityNotFoundException('orderItem with id : '. $id . ' was not found.');
        }

        return $orderItem;
    }

    /**
     * create a new OrderItem entity.
     *
     * @Security("has_role('ROLE_SUPER_ADMIN')")
     * @Rest\Post("/add", name="add_order_item")
     * @Rest\View(StatusCode = 201, serializerEnableMaxDepthChecks=true)
     * @ParamConverter("orderItem", converter="fos_rest.request_body")
     */
    public function addAction(OrderItem $orderItem)
    {
        $em = $this->getDoctrine()->getManager();
        $em->persist($orderItem);
        $em->flush();

        return $orderItem;
    }

    /**
     * Displays a form to edit an existing OrderItem entity.
     *
     * @Security("has_role('ROLE_SUPER_ADMIN')")
     * @Rest\Put("/update", name="update_order_item")
     * @Rest\View(StatusCode = 200, serializerEnableMaxDepthChecks=true)
     * @ParamConverter("orderItem", converter="fos_rest.request_body")
     */
    public function updateAction(OrderItem $orderItem)
    {
        $em = $this->getDoctrine()->getManager();
        $em->merge($orderItem);
        $em->flush();

        return $orderItem;
    }

    /**
     * Delete OrderItem by id
     *
     * @Security("has_role('ROLE_SUPER_ADMIN')")
     * @Rest\Delete("/delete/{id}", name="delete_order_item")
     * @Rest\View(StatusCode = 200)
     */
    public function deleteAction(OrderItem $orderItem = null, $id)
    {
        if (empty($orderItem)) {
            throw new EntityNotFoundException('orderItem with id : '. $id . ' was not found.');
        }

        $em = $this->getDoctrine()->getManager();
        $em->remove($orderItem);
        $em->flush();

        return new JsonResponse(sprintf("orderItem with id: %s  was removed.", $id), 200);
    }

}
