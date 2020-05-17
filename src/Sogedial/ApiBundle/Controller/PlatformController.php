<?php

namespace Sogedial\ApiBundle\Controller;

use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Controller\Annotations\QueryParam;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Sogedial\ApiBundle\Entity\Attribut;
use Sogedial\ApiBundle\Entity\Platform;
use Sogedial\ApiBundle\Exception\EntityNotFoundException;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;

/**
 * Platform controller.
 *
 * @Rest\Route(path="/api/platform")
 */
class PlatformController extends Controller {

    const LIMIT = 10;

    /**
     * Lists all Platform entities.
     *
     * @Rest\Get("", name="get_all_platform")
     * @Rest\View(StatusCode = 200, serializerEnableMaxDepthChecks=true)
     * @QueryParam(name="offset", requirements="\d+", default="0", description="Index de début de la pagination")
     * @QueryParam(name="limit", requirements="\d+", default="10", description="Nombre d'éléments à afficher")
     * @QueryParam(name="orderBy", default=null, description="nom de champ de l'ordre")
     * @QueryParam(name="orderByDesc", default=null, description="nom de champ de l'ordre descendant")
     * @QueryParam(name="filter", default=null, description="filtre sur les champs")
     */
    public function getAllAction($orderBy = null, $orderByDesc = null, $limit = self::LIMIT, $offset = 0, $filter = null) {
        $order = 'asc';
        $em = $this->getDoctrine()->getManager();

        if ($orderBy == null) {
            $meta = $em->getClassMetadata(Platform::class);
            $orderBy = $meta->getSingleIdentifierFieldName();
        }

        if ($orderByDesc != null) {
            $orderBy = $orderByDesc;
            $order = 'desc';
        }

        $filter = ($filter != null) ? json_decode($filter, true) : [];
        $repo = $this->get('sogedial.repository_injecter')->getRepository('SogedialApiBundle:Platform');
        return $repo->findBy($filter, [$orderBy => $order], $limit, $offset);
    }

    /**
     * Count Platform available after filter
     *     
     * @Rest\Get("/count", name="count_platform")
     * @Rest\View(StatusCode = 200)
     * @QueryParam(name="filter", default=null, description="filtre sur les champs")
     */
    public function countAction($filter = null) {
        $filter = ($filter != null) ? json_decode($filter, true) : [];

        return $this->get('sogedial.repository_injecter')->getRepository('SogedialApiBundle:Platform')->getCount($filter);
    }

    /**
     * Finds and displays a Platform entity.
     * 
     * @Security("has_role('ROLE_SUPER_ADMIN')")
     * @Rest\Get("/{id}", name="get_platform")
     * @Rest\View(statusCode = 200, serializerEnableMaxDepthChecks=true)
     */
    public function getAction(Platform $platform = null, $id) {
        if (empty($platform)) {
            throw new EntityNotFoundException('platform with id : ' . $id . ' was not found.');
        }

        return $platform;
    }

    /**
     * create a new Platform entity.
     * 
     * @Security("has_role('ROLE_SUPER_ADMIN')")
     * @Rest\Post("/add", name="add_platform")
     * @Rest\View(StatusCode = 201, serializerEnableMaxDepthChecks=true)
     * @ParamConverter("platform", converter="fos_rest.request_body")
     */
    public function addAction(Platform $platform) {
        $em = $this->getDoctrine()->getManager();
        $em->persist($platform);
        $em->flush();

        return $platform;
    }

    /**
     * Displays a form to edit an existing Platform entity.
     *
     * @Security("has_role('ROLE_SUPER_ADMIN') or has_role('ROLE_ADMIN')")
     * @Rest\Put("/update", name="update_platform")
     * @Rest\View(StatusCode = 200, serializerEnableMaxDepthChecks=true)
     * @ParamConverter("platform", converter="fos_rest.request_body")
     */
    public function updateAction(Platform $platform) {

       $datas = $platform->getAttributs();
       $em = $this->getDoctrine()->getManager();
       
       foreach ($datas as $data){
            $attribut = $platform->getAttributByKey($data->getKey());
            if(is_null($attribut->getId())){
                $attribut->setName($data->getKey());
                $attribut->setPlatform($platform);
            }
       }
       
       $em->merge($platform);
       $em->flush();

       return $platform;
    }

    /**
     * Delete Platform by id
     *
     * @Security("has_role('ROLE_SUPER_ADMIN')")
     * @Rest\Delete("/delete/{id}", name="delete_platform")
     * @Rest\View(StatusCode = 200)
     */
    public function deleteAction(Platform $platform = null, $id) {
        if (empty($platform)) {
            throw new EntityNotFoundException('platform with id : ' . $id . ' was not found.');
        }

        $em = $this->getDoctrine()->getManager();
        $em->remove($platform);
        $em->flush();

        return new JsonResponse(sprintf("platform with id: %s  was removed.", $id), 200);
    }

    /**
     * add attribut to platform
     *
     * @Security("has_role('ROLE_SUPER_ADMIN')")
     * @Rest\Put(path="/{id}/attribut/add/{id_attribut}", name="add_attribut_to_platform")
     * @ParamConverter("attribut", options={"mapping": {"id_attribut" : "id"}})
     * @Rest\View(statusCode=200)
     */
    public function addAttributAction(Platform $platform, Attribut $attribut)
    {
        $platform->addAttribut($attribut);
        $em = $this->getDoctrine()->getManager();
        $em->flush();

        return $platform;
    }


    /**
     * delete attribut from platfrom
     *
     * @Security("has_role('ROLE_SUPER_ADMIN')")
     * @Rest\Delete(path="/{id}/attribut/delete/{id_attribut}", name="delete_attribut_from_platform")
     * @ParamConverter("attribut", options={"mapping": {"id_attribut":"id"}})
     * @Rest\View(statusCode=200)
     */
    public function deleteAttributAction(Platform $platform, Attribut $attribut)
    {
        $platform->removeAttribut($attribut);

        $em = $this->getDoctrine()->getManager();
        $em->flush();

        return $platform;
    }

}
