<?php

namespace Sogedial\ApiBundle\Controller;

use Sogedial\ApiBundle\Exception\EntityNotFoundException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use FOS\RestBundle\Controller\Annotations as Rest;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use FOS\RestBundle\Controller\Annotations\QueryParam;
use Sogedial\ApiBundle\Entity\InvoiceItem;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

/**
 * InvoiceItem controller.
 *
 * @Rest\Route(path="/api/invoice_item")
 */
class InvoiceItemController extends Controller
{
    const LIMIT = 60;

    /**
     * Lists all InvoiceItem entities.
     *
     * @Rest\Get("", name="get_all_invoice_item")
     * @Rest\View(StatusCode = 200, serializerEnableMaxDepthChecks=true, serializerGroups={"list", "list_credit"})
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
            $meta = $em->getClassMetadata(InvoiceItem::class);
            $orderBy = $meta->getSingleIdentifierFieldName();
        }

        if ($orderByDesc != null) {
            $orderBy = $orderByDesc;
            $order = 'desc';
        }

        $filter = ($filter != null) ? json_decode($filter, true) : [];

        return $repo->getRepository('SogedialApiBundle:InvoiceItem')->findBy($filter, [$orderBy => $order], $limit, $offset);
    }

    /**
     * Count InvoiceItem available after filter
     *
     * @Rest\Get("/count", name="count_invoice_item")
     * @Rest\View(StatusCode = 200)
     * @QueryParam(name="filter", default=null, description="filtre sur les champs")
     */
    public function countAction($filter = null)
    {
        $filter = ($filter != null) ? json_decode($filter, true) : [];
        $repo = $this->get("sogedial.repository_injecter");

        return $repo->getRepository('SogedialApiBundle:InvoiceItem')->getCount($filter);
    }

    /**
     * Finds and displays a InvoiceItem entity.
     *
     * @Security("has_role('ROLE_SUPER_ADMIN')")
     * @Rest\Get("/{id}", name="get_invoice_item")
     * @Rest\View(statusCode = 200, serializerEnableMaxDepthChecks=true, serializerGroups={"detail"})
     */
    public function getAction(InvoiceItem $invoiceItem = null, $id)
    {
        if (empty($invoiceItem)) {
            throw new EntityNotFoundException('invoiceItem with id : '. $id . ' was not found.');
        }

        return $invoiceItem;
    }

    /**
     * create a new InvoiceItem entity.
     *
     * @Security("has_role('ROLE_SUPER_ADMIN')")
     * @Rest\Post("/add", name="add_invoice_item")
     * @Rest\View(StatusCode = 201, serializerEnableMaxDepthChecks=true, serializerGroups={"add"})
     * @ParamConverter("invoiceItem", converter="fos_rest.request_body")
     */
    public function addAction(InvoiceItem $invoiceItem)
    {
        $em = $this->getDoctrine()->getManager();
        $em->persist($invoiceItem);
        $em->flush();

        return $invoiceItem;
    }

    /**
     * Displays a form to edit an existing InvoiceItem entity.
     *
     * @Security("has_role('ROLE_SUPER_ADMIN')")
     * @Rest\Put("/update", name="update_invoice_item")
     * @Rest\View(StatusCode = 200, serializerEnableMaxDepthChecks=true, serializerGroups={"update"})
     * @ParamConverter("invoiceItem", converter="fos_rest.request_body")
     */
    public function updateAction(InvoiceItem $invoiceItem)
    {
        $em = $this->getDoctrine()->getManager();
        $em->merge($invoiceItem);
        $em->flush();

        return $invoiceItem;
    }

    /**
     * Delete InvoiceItem by id
     *
     * @Security("has_role('ROLE_SUPER_ADMIN')")
     * @Rest\Delete("/delete/{id}", name="delete_invoice_item")
     * @Rest\View(StatusCode = 200)
     */
    public function deleteAction(InvoiceItem $invoiceItem = null, $id)
    {
        if (empty($invoiceItem)) {
            throw new EntityNotFoundException('invoiceItem with id : '. $id . ' was not found.');
        }

        $em = $this->getDoctrine()->getManager();
        $em->remove($invoiceItem);
        $em->flush();

        return new JsonResponse(sprintf("invoiceItem with id: %s  was removed.", $id), 200);
    }

}
