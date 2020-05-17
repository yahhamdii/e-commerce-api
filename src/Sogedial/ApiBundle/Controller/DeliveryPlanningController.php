<?php

namespace Sogedial\ApiBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Sogedial\ApiBundle\Entity\Attribut;
use Sogedial\ApiBundle\Exception\BadRequestException;
use Sogedial\ApiBundle\Exception\EntityNotFoundException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use FOS\RestBundle\Controller\Annotations as Rest;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use FOS\RestBundle\Controller\Annotations\QueryParam;
use Sogedial\ApiBundle\Entity\DeliveryPlanning;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;

/**
 * DeliveryPlanning controller.
 *
 * @Rest\Route(path="/api/delivery_planning")
 */
class DeliveryPlanningController extends Controller
{
    const LIMIT = 10;

    /**
     * Lists all DeliveryPlanning entities.
     *
     * @Security("has_role('ROLE_ADMIN') or has_role('ROLE_CUSTOMER')")
     * @Rest\Get("", name="get_all_delivery_planning")
     * @Rest\View(StatusCode = 200, serializerEnableMaxDepthChecks=true, serializerGroups={"list"})
     * @QueryParam(name="offset", requirements="\d+", default="0", description="Index de début de la pagination")
     * @QueryParam(name="limit", requirements="\d+", default="10", description="Nombre d'éléments à afficher")
     * @QueryParam(name="orderBy", default=null, description="nom de champ de l'ordre")
     * @QueryParam(name="orderByDesc", default=null, description="nom de champ de l'ordre descendant")
     * @QueryParam(name="filter", default=null, description="filtre sur les champs")
     */
    public function getAllAction($orderBy = null, $orderByDesc = null, $limit = self::LIMIT, $offset = 0, $filter = null)
    {
        /*
            Filter example to have only null parents
            /api/delivery_planning?filter={"and": [["dp.parent","isNull", null]]}
        */

        $order = 'asc';
        $em = $this->getDoctrine()->getManager();
        
        if ($orderBy == null) {
            $meta = $em->getClassMetadata(DeliveryPlanning::class);
            $orderBy = $meta->getSingleIdentifierFieldName();
        }

        if ($orderByDesc != null) {
            $orderBy = $orderByDesc;
            $order = 'desc';
        }

        $filter = ($filter != null) ? json_decode($filter, true) : [];
        
        return $this->get('sogedial.repository_injecter')->getRepository('SogedialApiBundle:DeliveryPlanning')->findBy($filter, [$orderBy => $order], $limit, $offset);
    }

    /**
     * Count DeliveryPlanning available after filter
     *
     * @Security("has_role('ROLE_ADMIN') or has_role('ROLE_CUSTOMER')")
     * @Rest\Get("/count", name="count_delivery_planning")
     * @Rest\View(StatusCode = 200)
     * @QueryParam(name="filter", default=null, description="filtre sur les champs")
     */
    public function countAction($filter = null)
    {
        $filter = ($filter != null) ? json_decode($filter, true) : [];        

        return $this->get('sogedial.repository_injecter')->getRepository('SogedialApiBundle:DeliveryPlanning')->getCount($filter);
    }

    /**
     * Finds and displays a DeliveryPlanning entity.
     *
     * @Security("has_role('ROLE_SUPER_ADMIN')")
     * @Rest\Get("/{id}", name="get_delivery_planning")
     * @Rest\View(statusCode = 200, serializerEnableMaxDepthChecks=true, serializerGroups={"detail"})
     */
    public function getAction(DeliveryPlanning $deliveryPlanning = null, $id)
    {
        if (empty($deliveryPlanning)) {
            throw new EntityNotFoundException('deliveryPlanning with id : '. $id . ' was not found.');
        }

        return $deliveryPlanning;
    }

    /**
     * create a new DeliveryPlanning entity.
     *
     * @Security("has_role('ROLE_ADMIN')")
     * @Rest\Post("/add", name="add_delivery_planning")
     * @Rest\View(StatusCode = 201, serializerEnableMaxDepthChecks=true, serializerGroups={"add"})
     * @ParamConverter("deliveryPlanning", converter="fos_rest.request_body")
     */
    public function addAction(DeliveryPlanning $deliveryPlanning)
    {
        $em = $this->getDoctrine()->getManager();        
        $em->persist($deliveryPlanning);
        $em->flush();

        if($deliveryPlanning->getDays()){
            foreach($deliveryPlanning->getDays() as $day){
                $day->setDeliveryPlanning($deliveryPlanning);
                $em->persist($day);
            }
        }

        $em->flush();

        return $deliveryPlanning;
    }

    /**
     * Displays a form to edit an existing DeliveryPlanning entity.
     *
     * @Security("has_role('ROLE_ADMIN')")
     * @Rest\Put("/update", name="update_delivery_planning")
     * @Rest\View(StatusCode = 200, serializerEnableMaxDepthChecks=true, serializerGroups={"update"})
     * @ParamConverter("deliveryPlanning", converter="fos_rest.request_body")
     */
    public function updateAction(DeliveryPlanning $deliveryPlanning)
    {
        $em = $this->getDoctrine()->getManager();
        $em->clear();

        $entity = $em->getRepository('SogedialApiBundle:DeliveryPlanning')->find($deliveryPlanning->getId());

        $deliveryModeAttribut = $entity->getPlatform()->getAttributByKey(Attribut::KEY_DELIVERY_MODE);
        if(!$deliveryModeAttribut->getValue()){
            if($deliveryPlanning->getTemperature() !== $entity->getTemperature()){
                $newTemperature = $deliveryPlanning->getTemperature();

                if (!in_array($newTemperature, array(DeliveryPlanning::TEMPERATURE_DRY, DeliveryPlanning::TEMPERATURE_FRESH, DeliveryPlanning::TEMPERATURE_FROZEN))) {
                    throw new BadRequestException(sprintf("temperature '%s' not allowed", $newTemperature));
                }

                $children = $entity->getChildren();
                if(!$children->isEmpty()){
                    /** @var DeliveryPlanning $child */
                    foreach ($children as $child){
                        $child->setTemperature($newTemperature);
                    }
                }
            }
        }

        $days = $entity->getDays();

        foreach($days as $day){            
            $day->setDeliveryPlanning(null);
            $em->remove($day);
            $em->flush();
        }

        $em->merge($deliveryPlanning);
        $em->flush();

        return $deliveryPlanning; 
    }

    /**
     * Delete DeliveryPlanning by id
     *
     * @Security("has_role('ROLE_ADMIN')")
     * @Rest\Delete("/delete/{id}", name="delete_delivery_planning")
     * @Method({"DELETE", "OPTIONS"})
     * @Rest\View(StatusCode = 200)
     */
    public function deleteAction(DeliveryPlanning $deliveryPlanning = null, $id)
    {
        if (empty($deliveryPlanning)) {
            throw new EntityNotFoundException('deliveryPlanning with id : '. $id . ' was not found.');
        }

        $em = $this->getDoctrine()->getManager();

        $children = $deliveryPlanning->getChildren();
        if($children){
            foreach($children as $child){
                $deliveryPlanning->removeChild($child);
                $child->setParent(null);
                $em->persist($child);
                $em->persist($deliveryPlanning);
                $em->flush();            
            }
        }

        $parent = $deliveryPlanning->getParent();
        if($parent){
            $parent->removeChild($deliveryPlanning);            
            $deliveryPlanning->setParent(null);   
            $em->persist($parent);
            $em->persist($deliveryPlanning);
            $em->flush();     
        }
        
        $days = $deliveryPlanning->getDays();
        foreach($days as $day){
            $deliveryPlanning->removeDay($day);
            $em->remove($day);
            $em->flush();
        }
        
        $em->remove($deliveryPlanning);
        $em->flush();

        return new JsonResponse(sprintf("deliveryPlanning with id: %s  was removed.", $id), 200);
    }

}
