<?php

namespace Sogedial\ApiBundle\Controller;

use FOS\RestBundle\Controller\Annotations as Rest;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Sogedial\ApiBundle\Entity\Cart;
use Sogedial\ApiBundle\Entity\Container;
use Sogedial\ApiBundle\Exception\EntityNotFoundException;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;

/**
 * Containers controller.
 *
 * @Security("has_role('ROLE_CUSTOMER')")
 * @Rest\Route(path="/api")
 */
class ContainerController extends Controller {

    const LIMIT = 60;

    /**
     * Lists all Container entities.
     *
     * @Rest\Get("/container", name="get_all_container")
     * @Rest\View(StatusCode = 200, serializerEnableMaxDepthChecks=true)
     * @Rest\QueryParam(name="offset", requirements="\d+", default="0", description="Index de début de la pagination")
     * @Rest\QueryParam(name="limit", requirements="\d+", default="10", description="Nombre d'éléments à afficher")
     * @Rest\QueryParam(name="orderBy", default=null, description="nom de champ de l'ordre")
     * @Rest\QueryParam(name="orderByDesc", default=null, description="nom de champ de l'ordre descendant")
     * @Rest\QueryParam(name="filter", default=null, description="filtre sur les champs")
     *
     */
    public function getAllAction($orderBy = null, $orderByDesc = null, $limit = self::LIMIT, $offset = 0, $filter = null) {
        $order = 'asc';
        $em = $this->getDoctrine()->getManager();
        $repoInjector = $this->get('sogedial.repository_injecter');

        if ($orderBy == null) {
            $meta = $em->getClassMetadata(Container::class);
            $orderBy = $meta->getSingleIdentifierFieldName();
        }

        if ($orderByDesc != null) {
            $orderBy = $orderByDesc;
            $order = 'desc';
        }

        $filter = ($filter != null) ? json_decode($filter, true) : [];

        return $repoInjector->getRepository('SogedialApiBundle:Container')->findBy($filter, [$orderBy => $order], $limit, $offset);
    }

    /**
     * create a new Containers entity.
     *
     * @Rest\Post("/container/add", name="add_container")
     * @Rest\View(StatusCode = 201, serializerEnableMaxDepthChecks=true)
     * @ParamConverter("container", converter="fos_rest.request_body")
     */
    public function addAction(Container $container) {

           $em = $this->getDoctrine()->getManager();
           $em->persist($container);
           $em->flush();

        return $em->getRepository('SogedialApiBundle:Container')->find($container->getId());
    }

    /**
     * Count Containers available after filter
     *
     * @Rest\Get("/container/count", name="count_container")
     * @Rest\View(StatusCode = 200)
     * @Rest\QueryParam(name="filter", default=null, description="filtre sur les champs")
     */
    public function countAction($filter = null) {
        $repoInjector = $this->get('sogedial.repository_injecter');
        $filter = ($filter != null) ? json_decode($filter, true) : [];

        return $repoInjector->getRepository('SogedialApiBundle:Container')->getCount($filter);
    }

    /**
     * Finds and displays a Container entity.
     *
     * @Security("has_role('ROLE_SUPER_ADMIN')")
     * @Rest\Get("/container/{id}", name="get_container")
     * @Rest\View(statusCode = 200, serializerEnableMaxDepthChecks=true)
     */
    public function getAction(Container $container = null, $id) {
        if (empty($container)) {
            throw new EntityNotFoundException('container with id : ' . $id . ' was not found.');
        }

        return $container;
    }

    /**
     *
     * @Rest\Put("/container/update", name="update_container")
     * @Rest\View(StatusCode = 200, serializerEnableMaxDepthChecks=true)
     * @ParamConverter("container", converter="fos_rest.request_body")
     */
    public function updateAction(Container $container) {


        $em = $this->getDoctrine()->getManager();
        $em->merge($container);
        $em->flush();

        $em->refresh($container);
        $this->get('sogedial.container_manager')->refreshContainer($container);
        $em->flush();

        return $container;
    }

    /**
     * Delete Containers by id
     *
     * @Rest\Delete("/container/{id}", name="delete_container")
     * @Rest\View(StatusCode = 200)
     */
    public function deleteAction(Container $container = null, $id) {
        if (empty($container)) {
            throw new EntityNotFoundException('container with id : ' . $id . ' was not found.');
        }
        $em = $this->getDoctrine()->getManager();

        $em->remove($container);
        $em->flush();

        return new JsonResponse(sprintf("container with id: %s  was removed.", $id), 200);
    }

}
