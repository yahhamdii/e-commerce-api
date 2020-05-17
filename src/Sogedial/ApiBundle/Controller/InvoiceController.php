<?php

namespace Sogedial\ApiBundle\Controller;

use Sogedial\ApiBundle\Exception\EntityNotFoundException;
use Sogedial\ApiBundle\Exception\ParametersException;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use FOS\RestBundle\Controller\Annotations as Rest;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use FOS\RestBundle\Controller\Annotations\QueryParam;
use Sogedial\ApiBundle\Entity\Invoice;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sogedial\OAuthBundle\Entity\UserCustomer;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

/**
 * Invoice controller.
 *
 * @Rest\Route(path="/api/invoice")
 */
class InvoiceController extends Controller
{
    const LIMIT = 10;

    /**
     * Lists all Invoice entities.
     *
     * @Rest\Get("", name="get_all_invoice")
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

        $filter = ($filter != null) ? json_decode($filter, true) : [];
        if($this->getUser() instanceof UserCustomer && !isset($filter['platform'])){
            throw new ParametersException(array('platform'));
        }
        
        if ($orderBy == null) {
            $meta = $em->getClassMetadata(Invoice::class);
            $orderBy = $meta->getSingleIdentifierFieldName();
        }

        if ($orderByDesc != null) {
            $orderBy = $orderByDesc;
            $order = 'desc';
        }
        
        return $this->get('sogedial.repository_injecter')->getRepository('SogedialApiBundle:Invoice')->findBy($filter, [$orderBy => $order], $limit, $offset);
    }

    /**
     * Count Invoice available after filter
     *
     * @Rest\Get("/count", name="count_invoice")
     * @Rest\View(StatusCode = 200)
     * @QueryParam(name="filter", default=null, description="filtre sur les champs")
     */
    public function countAction($filter = null)
    {
        $filter = ($filter != null) ? json_decode($filter, true) : [];
        if($this->getUser() instanceof UserCustomer && !isset($filter['platform'])){
            throw new ParametersException(array('platform'));
        } 

        return $this->get('sogedial.repository_injecter')->getRepository('SogedialApiBundle:Invoice')->getCount($filter);
    }

    /**
     * Finds and displays a Invoice entity.
     * @Security("has_role('ROLE_SUPER_ADMIN')")
     * @Rest\Get("/{id}", name="get_invoice")
     * @Rest\View(statusCode = 200, serializerEnableMaxDepthChecks=true, serializerGroups={"detail"})
     */
    public function getAction(Invoice $invoice = null, $id)
    {
        if (empty($invoice)) {
            throw new EntityNotFoundException('Invoice with id : '. $id . ' was not found.');
        }

        return $invoice;
    }

    /**
     * create a new Invoice entity.
     *
     * @Security("has_role('ROLE_SUPER_ADMIN')")
     * @Rest\Post("/add", name="add_invoice")
     * @Rest\View(StatusCode = 201, serializerEnableMaxDepthChecks=true, serializerGroups={"add"})
     * @ParamConverter("invoice", converter="fos_rest.request_body")
     */
    public function addAction(Invoice $invoice)
    {
        $em = $this->getDoctrine()->getManager();
        $em->persist($invoice);
        $em->flush();

        return $invoice;
    }

    /**
     * Displays a form to edit an existing Invoice entity.
     *
     * @Security("has_role('ROLE_SUPER_ADMIN')")
     * @Rest\Put("/update", name="update_invoice")
     * @Rest\View(StatusCode = 200, serializerEnableMaxDepthChecks=true, serializerGroups={"update"})
     * @ParamConverter("invoice", converter="fos_rest.request_body")
     */
    public function updateAction(Invoice $invoice)
    {
        $em = $this->getDoctrine()->getManager();
        $em->merge($invoice);
        $em->flush();

        return $invoice;
    }

    /**
     * Delete Invoice by id
     *
     * @Security("has_role('ROLE_SUPER_ADMIN')")
     * @Rest\Delete("/delete/{id}", name="delete_invoice")
     * @Method({"DELETE", "OPTIONS"})
     * @Rest\View(StatusCode = 200)
     */
    public function deleteAction(Invoice $invoice = null, $id)
    {
        if (empty($invoice)) {
            throw new EntityNotFoundException('client with id : ' . $id . ' was not found.');
        }

        $em = $this->getDoctrine()->getManager();
        $em->remove($invoice);
        $em->flush();

        return new JsonResponse(sprintf("invoice with id: %s  was removed.", $id), 200);
    }

}
