<?php

namespace Sogedial\ApiBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Sogedial\ApiBundle\Exception\EntityNotFoundException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use FOS\RestBundle\Controller\Annotations as Rest;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use FOS\RestBundle\Controller\Annotations\QueryParam;
use Sogedial\ApiBundle\Entity\DayOff;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;

/**
 * DayOff controller.
 *
 *
 * @Rest\Route(path="/api/dayoff")
 */
class DayOffController extends Controller
{
    const LIMIT = 10;

    /**
     * Lists all DayOff entities.
     *
     * @Security("has_role('ROLE_ADMIN') or has_role('ROLE_CUSTOMER')")
     * @Rest\Get("", name="get_all_dayoff")
     * @Rest\View(StatusCode = 200, serializerEnableMaxDepthChecks=true, serializerGroups={"list"})
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
            $meta = $em->getClassMetadata(DayOff::class);
            $orderBy = $meta->getSingleIdentifierFieldName();
        }

        if ($orderByDesc != null) {
            $orderBy = $orderByDesc;
            $order = 'desc';
        }

        $filter = ($filter != null) ? json_decode($filter, true) : [];
        
        return $this->get('sogedial.repository_injecter')->getRepository('SogedialApiBundle:DayOff')->findBy($filter, [$orderBy => $order], $limit, $offset);
    }

    /**
     * Count DayOff available after filter
     *
     * @Security("has_role('ROLE_ADMIN') or has_role('ROLE_CUSTOMER')")
     * @Rest\Get("/count", name="count_dayoff")
     * @Rest\View(StatusCode = 200)
     * @QueryParam(name="filter", default=null, description="filtre sur les champs")
     */
    public function countAction($filter = null)
    {
        $filter = ($filter != null) ? json_decode($filter, true) : [];        

        return $this->get('sogedial.repository_injecter')->getRepository('SogedialApiBundle:DayOff')->getCount($filter);
    }

    /**
     * Finds and displays a DayOff entity.
     *
     * @Security("has_role('ROLE_SUPER_ADMIN')")
     * @Rest\Get("/{id}", name="get_dayoff")
     * @Rest\View(statusCode = 200, serializerEnableMaxDepthChecks=true, serializerGroups={"detail"})
     */
    public function getAction(DayOff $dayOff = null, $id)
    {
        if (empty($dayOff)) {
            throw new EntityNotFoundException('DayOff with id : '. $id . ' was not found.');
        }

        return $dayOff;
    }

    /**
     * create a new DayOff entity.
     *
     * @Security("has_role('ROLE_ADMIN')")
     * @Rest\Post("/add", name="add_dayoff")
     * @Rest\View(StatusCode = 201, serializerEnableMaxDepthChecks=true, serializerGroups={"add"})
     * @ParamConverter("dayOff", converter="fos_rest.request_body")
     */
    public function addAction(DayOff $dayOff)
    {
        $em = $this->getDoctrine()->getManager();
        $em->persist($dayOff);
        $em->flush();

        return $dayOff;
    }

    /**
     * Displays a form to edit an existing DayOff entity.
     *
     * @Security("has_role('ROLE_ADMIN')")
     * @Rest\Put("/update", name="update_dayoff")
     * @Rest\View(StatusCode = 200, serializerEnableMaxDepthChecks=true, serializerGroups={"update"})
     * @ParamConverter("dayOff", converter="fos_rest.request_body")
     */
    public function updateAction(DayOff $dayOff)
    {
        $em = $this->getDoctrine()->getManager();
        $em->merge($dayOff);
        $em->flush();

        return $dayOff;
    }

    /**
     * Delete DayOff by id
     *
     * @Security("has_role('ROLE_ADMIN')")
     * @Rest\Delete("/delete/{id}", name="delete_dayoff")
     * @Method({"DELETE", "OPTIONS"})
     * @Rest\View(StatusCode = 200)
     */
    public function deleteAction(DayOff $dayOff = null, $id)
    {
        if (empty($dayOff)) {
            throw new EntityNotFoundException('client with id : ' . $id . ' was not found.');
        }

        $em = $this->getDoctrine()->getManager();
        $em->remove($dayOff);
        $em->flush();

        return new JsonResponse(sprintf("dayOff with id: %s  was removed.", $id), 200);
    }

}
