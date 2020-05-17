<?php

namespace Sogedial\ApiBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Sogedial\ApiBundle\Exception\EntityNotFoundException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use FOS\RestBundle\Controller\Annotations as Rest;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use FOS\RestBundle\Controller\Annotations\QueryParam;
use Sogedial\ApiBundle\Entity\Stock;

/**
 * Stock controller.
 *
 * @Security("has_role('ROLE_SUPER_ADMIN')")
 * @Rest\Route(path="/api/stock")
 */
class StockController extends Controller
{
    const LIMIT = 10;

    /**
     * Lists all Stock entities.
     *
     * @Rest\Get("", name="get_all_stock")
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
            $meta = $em->getClassMetadata(Stock::class);
            $orderBy = $meta->getSingleIdentifierFieldName();
        }

        if ($orderByDesc != null) {
            $orderBy = $orderByDesc;
            $order = 'desc';
        }

        $filter = ($filter != null) ? json_decode($filter, true) : [];

        return $em->getRepository('SogedialApiBundle:Stock')->findBy($filter, [$orderBy => $order], $limit, $offset);
    }

    /**
     * Count Stock available after filter
     *
     * @Rest\Get("/count", name="count_stock")
     * @Rest\View(StatusCode = 200)
     * @QueryParam(name="filter", default=null, description="filtre sur les champs")
     */
    public function countAction($filter = null)
    {
        $filter = ($filter != null) ? json_decode($filter, true) : [];

        return $this->getDoctrine()->getManager()->getRepository('SogedialApiBundle:Stock')->getCount($filter);
    }

    /**
     * Finds and displays a Stock entity.
     *
     * @Rest\Get("/{id}", name="get_stock")
     * @Rest\View(statusCode = 200, serializerEnableMaxDepthChecks=true)
     */
    public function getAction(Stock $stock = null, $id)
    {
        if (empty($stock)) {
            throw new EntityNotFoundException('stock with id : '. $id . ' was not found.');
        }

        return $stock;
    }

    /**
     * create a new Stock entity.
     *
     * @Rest\Post("/add", name="add_stock")
     * @Rest\View(StatusCode = 201, serializerEnableMaxDepthChecks=true)
     * @ParamConverter("stock", converter="fos_rest.request_body")
     */
    public function addAction(Stock $stock)
    {
        $em = $this->getDoctrine()->getManager();
        $em->persist($stock);
        $em->flush();

        return $stock;
    }

    /**
     * Displays a form to edit an existing Stock entity.
     *
     * @Rest\Put("/update", name="update_stock")
     * @Rest\View(StatusCode = 200, serializerEnableMaxDepthChecks=true)
     * @ParamConverter("stock", converter="fos_rest.request_body")
     */
    public function updateAction(Stock $stock)
    {
        $em = $this->getDoctrine()->getManager();
        $em->merge($stock);
        $em->flush();

        return $stock;
    }

    /**
     * Delete Stock by id
     *
     * @Rest\Delete("/delete/{id}", name="delete_stock")
     * @Rest\View(StatusCode = 200)
     */
    public function deleteAction(Stock $stock = null, $id)
    {
        if (empty($stock)) {
            throw new EntityNotFoundException('stock with id : '. $id . ' was not found.');
        }

        $em = $this->getDoctrine()->getManager();
        $em->remove($stock);
        $em->flush();

        return new JsonResponse(sprintf("stock with id: %s  was removed.", $id), 200);
    }

}
