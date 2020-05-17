<?php

namespace Sogedial\ApiBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Sogedial\ApiBundle\Exception\EntityNotFoundException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use FOS\RestBundle\Controller\Annotations as Rest;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use FOS\RestBundle\Controller\Annotations\QueryParam;
use Sogedial\ApiBundle\Entity\Promotion;

/**
 * Promotion controller.
 * 
 * @Rest\Route(path="/api/promotion")
 */
class PromotionController extends Controller
{
    const LIMIT = 10;

    /**
     * Lists all Promotion entities.
     *
     * @Security("has_role('ROLE_SUPER_ADMIN')")
     * @Rest\Get("", name="get_all_promotion")
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
            $meta = $em->getClassMetadata(Promotion::class);
            $orderBy = $meta->getSingleIdentifierFieldName();
        }

        if ($orderByDesc != null) {
            $orderBy = $orderByDesc;
            $order = 'desc';
        }

        $filter = ($filter != null) ? json_decode($filter, true) : [];

        return $em->getRepository('SogedialApiBundle:Promotion')->findBy($filter, [$orderBy => $order], $limit, $offset);
    }

    /**
     * Count Promotion available after filter
     *
     * @Security("has_role('ROLE_SUPER_ADMIN')")
     * @Rest\Get("/count", name="count_promotion")
     * @Rest\View(StatusCode = 200)
     * @QueryParam(name="filter", default=null, description="filtre sur les champs")
     */
    public function countAction($filter = null)
    {
        $filter = ($filter != null) ? json_decode($filter, true) : [];

        return $this->getDoctrine()->getManager()->getRepository('SogedialApiBundle:Promotion')->getCount($filter);
    }

    /**
     * Finds and displays a Promotion entity.
     *
     * @Security("has_role('ROLE_SUPER_ADMIN')")
     * @Rest\Get("/{id}", name="get_promotion")
     * @Rest\View(statusCode = 200, serializerEnableMaxDepthChecks=true, serializerGroups={"detail"})
     */
    public function getAction(Promotion $promotion = null, $id)
    {
        if (empty($promotion)) {
            throw new EntityNotFoundException('promotion with id : '. $id . ' was not found.');
        }

        return $promotion;
    }

    /**
     * create a new Promotion entity.
     *
     * @Security("has_role('ROLE_SUPER_ADMIN')")
     * @Rest\Post("/add", name="add_promotion")
     * @Rest\View(StatusCode = 201, serializerEnableMaxDepthChecks=true, serializerGroups={"add"})
     * @ParamConverter("promotion", converter="fos_rest.request_body")
     */
    public function addAction(Promotion $promotion)
    {
        $em = $this->getDoctrine()->getManager();
        $em->persist($promotion);
        $em->flush();

        return $promotion;
    }

    /**
     * Displays a form to edit an existing Promotion entity.
     *
     * @Security("has_role('ROLE_SUPER_ADMIN') or has_role('ROLE_CUSTOMER') ")
     * @Rest\Put("/update", name="update_promotion")
     * @Rest\View(StatusCode = 200, serializerEnableMaxDepthChecks=true, serializerGroups={"update"})
     * @ParamConverter("promotion", converter="fos_rest.request_body")
     */
    public function updateAction(Promotion $promotion)
    {
        $em = $this->getDoctrine()->getManager();

        if($promotion->getStockCommitmentRequest() > 0 && !$promotion->getHasRequestedCommitment()){
            /* Send Email */
            $this->sendPromotionNotification($promotion);
            $promotion->setHasRequestedCommitment(true);
        }

        $em->merge($promotion);
        $em->flush();

        return $promotion;
    }

    private function sendPromotionNotification(Promotion $promotion): void
    {
        $emailCommitment = ($promotion->getPlatform())?$promotion->getPlatform()->getEmailCommitment():null;
        $this->get('sogedial.oauth.mailer')->sendPromotionAlert($promotion, $emailCommitment);        
    }

    /**
     * Delete Promotion by id
     *
     * @Security("has_role('ROLE_SUPER_ADMIN')")
     * @Rest\Delete("/delete/{id}", name="delete_promotion")
     * @Rest\View(StatusCode = 200)
     */
    public function deleteAction(Promotion $promotion = null, $id)
    {
        if (empty($promotion)) {
            throw new EntityNotFoundException('promotion with id : '. $id . ' was not found.');
        }

        $em = $this->getDoctrine()->getManager();
        $em->remove($promotion);
        $em->flush();

        return new JsonResponse(sprintf("promotion with id: %s  was removed.", $id), 200);
    }

}
