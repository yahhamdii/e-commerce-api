<?php

namespace Sogedial\ApiBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Sogedial\ApiBundle\Exception\EntityNotFoundException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use FOS\RestBundle\Controller\Annotations as Rest;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use FOS\RestBundle\Controller\Annotations\QueryParam;
use Sogedial\ApiBundle\Entity\DeliveryPlanningDay;

/**
 * DeliveryPlanningDay controller.
 *
 * @Security("has_role('ROLE_SUPER_ADMIN')")
 * @Rest\Route(path="/api/delivery_planning_day")
 */
class DeliveryPlanningDayController extends Controller
{
    const LIMIT = 10;

    /**
     * Lists all DeliveryPlanningDay entities.
     *
     * @Rest\Get("", name="get_all_delivery_planning_day")
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
            $meta = $em->getClassMetadata(DeliveryPlanningDay::class);
            $orderBy = $meta->getSingleIdentifierFieldName();
        }

        if ($orderByDesc != null) {
            $orderBy = $orderByDesc;
            $order = 'desc';
        }

        $filter = ($filter != null) ? json_decode($filter, true) : [];

        return $em->getRepository('SogedialApiBundle:DeliveryPlanningDay')->findBy($filter, [$orderBy => $order], $limit, $offset);
    }

    /**
     * Count DeliveryPlanningDay available after filter
     *
     * @Rest\Get("/count", name="count_delivery_planning_day")
     * @Rest\View(StatusCode = 200)
     * @QueryParam(name="filter", default=null, description="filtre sur les champs")
     */
    public function countAction($filter = null)
    {
        $filter = ($filter != null) ? json_decode($filter, true) : [];

        return $this->getDoctrine()->getManager()->getRepository('SogedialApiBundle:DeliveryPlanningDay')->getCount($filter);
    }

    /**
     * Finds and displays a DeliveryPlanningDay entity.
     *
     * @Rest\Get("/{id}", name="get_delivery_planning_day")
     * @Rest\View(statusCode = 200, serializerEnableMaxDepthChecks=true)
     */
    public function getAction(DeliveryPlanningDay $deliveryPlanningDay = null, $id)
    {
        if (empty($deliveryPlanningDay)) {
            throw new EntityNotFoundException('deliveryPlanningDay with id : '. $id . ' was not found.');
        }

        return $deliveryPlanningDay;
    }

    /**
     * create a new DeliveryPlanningDay entity.
     *
     * @Rest\Post("/add", name="add_delivery_planning_day")
     * @Rest\View(StatusCode = 201, serializerEnableMaxDepthChecks=true)
     * @ParamConverter("deliveryPlanningDay", converter="fos_rest.request_body")
     */
    public function addAction(DeliveryPlanningDay $deliveryPlanningDay)
    {
        $em = $this->getDoctrine()->getManager();
        $em->persist($deliveryPlanningDay);
        $em->flush();

        return $deliveryPlanningDay;
    }

    /**
     * Displays a form to edit an existing DeliveryPlanningDay entity.
     *
     * @Rest\Put("/update", name="update_delivery_planning_day")
     * @Rest\View(StatusCode = 200, serializerEnableMaxDepthChecks=true)
     * @ParamConverter("deliveryPlanningDay", converter="fos_rest.request_body")
     */
    public function updateAction(DeliveryPlanningDay $deliveryPlanningDay)
    {
        $em = $this->getDoctrine()->getManager();
        $em->merge($deliveryPlanningDay);
        $em->flush();

        return $deliveryPlanningDay;
    }

    /**
     * Delete DeliveryPlanningDay by id
     *
     * @Rest\Delete("/delete/{id}", name="delete_delivery_planning_day")
     * @Rest\View(StatusCode = 200)
     */
    public function deleteAction(DeliveryPlanningDay $deliveryPlanningDay = null, $id)
    {
        if (empty($deliveryPlanningDay)) {
            throw new EntityNotFoundException('deliveryPlanningDay with id : '. $id . ' was not found.');
        }

        $em = $this->getDoctrine()->getManager();
        $em->remove($deliveryPlanningDay);
        $em->flush();

        return new JsonResponse(sprintf("deliveryPlanningDay with id: %s  was removed.", $id), 200);
    }

}
