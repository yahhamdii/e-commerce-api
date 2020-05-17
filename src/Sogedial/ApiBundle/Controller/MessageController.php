<?php

namespace Sogedial\ApiBundle\Controller;

use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Controller\Annotations\QueryParam;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Sogedial\ApiBundle\Entity\Message;
use Sogedial\ApiBundle\Exception\BadRequestException;
use Sogedial\ApiBundle\Exception\EntityNotFoundException;
use Sogedial\OAuthBundle\Entity\UserCustomer;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;

/**
 * Message controller.
 *
 * @Rest\Route(path="/api/message")
 */
class MessageController extends Controller
{
    const LIMIT = 10;

    /**
     * Lists all Message entities.
     *
     * @Security("has_role('ROLE_CUSTOMER') or has_role('ROLE_ADMIN')")
     * @Rest\Get("", name="get_all_message")
     * @Rest\View(StatusCode = 200, serializerEnableMaxDepthChecks=true)
     * @QueryParam(name="offset", requirements="\d+", default="0", description="Index de début de la pagination")
     * @QueryParam(name="limit", requirements="\d+", default="10", description="Nombre d'éléments à afficher")
     * @QueryParam(name="orderBy", default=null, description="nom de champ de l'ordre")
     * @QueryParam(name="orderByDesc", default=null, description="nom de champ de l'ordre descendant")
     * @QueryParam(name="filter", default=null, description="filtre sur les champs")
     */
    public function getAllAction($orderBy = null, $orderByDesc = null, $limit = self::LIMIT, $offset = 0, $filter = null)
    {
        $em = $this->getDoctrine()->getManager();
        $filter = ($filter != null) ? json_decode($filter, true) : [];

        if ($this->getUser() instanceof UserCustomer) {
            $platformsClient = $this->getUser()->getClient()->getPlatforms();
            if(!$platformsClient->contains($em->getRepository('SogedialApiBundle:Platform')->find($filter['platform']))){
                throw new BadRequestException(sprintf('given platform  %s is not allowed !', $filter['platform']));
            }
        }

        $order = 'asc';

        if ($orderBy == null) {
            $meta = $em->getClassMetadata(Message::class);
            $orderBy = $meta->getSingleIdentifierFieldName();
        }

        if ($orderByDesc != null) {
            $orderBy = $orderByDesc;
            $order = 'desc';
        }

        $repo = $this->get('sogedial.repository_injecter');
        return $repo->getRepository('SogedialApiBundle:Message')->findBy($filter, [$orderBy => $order], $limit, $offset);
    }

    /**
     * Count Message available after filter
     *
     * @Security("has_role('ROLE_CUSTOMER') or has_role('ROLE_ADMIN')")
     * @Rest\Get("/count", name="count_message")
     * @Rest\View(StatusCode = 200)
     * @QueryParam(name="filter", default=null, description="filtre sur les champs")
     */
    public function countAction($filter = null)
    {
        $filter = ($filter != null) ? json_decode($filter, true) : [];

        return $this->getDoctrine()->getManager()->getRepository('SogedialApiBundle:Message')->getCount($filter);
    }

    /**
     * Finds and displays a Message entity.
     *
     *
     * @Security("has_role('ROLE_SUPER_ADMIN')")
     * @Rest\Get("/{id}", name="get_message")
     * @Rest\View(statusCode = 200, serializerEnableMaxDepthChecks=true)
     */
    public function getAction(Message $message = null, $id)
    {
        if (empty($message)) {
            throw new EntityNotFoundException('message with id : ' . $id . ' was not found.');
        }

        return $message;
    }

    /**
     * create a new Message entity.
     *
     * @Security("has_role('ROLE_ADMIN')")
     * @Rest\Post("/add", name="add_message")
     * @Rest\View(StatusCode = 201, serializerEnableMaxDepthChecks=true)
     * @ParamConverter("message", converter="fos_rest.request_body")
     */
    public function addAction(Message $message)
    {
        $message->setPlatform($this->getUser()->getPlatform());
        $em = $this->getDoctrine()->getManager();
        $em->persist($message);
        $em->flush();

        return $message;
    }

    /**
     * Displays a form to edit an existing Message entity.
     *
     * @Security("has_role('ROLE_ADMIN')")
     * @Rest\Put("/update", name="update_message")
     * @Rest\View(StatusCode = 200, serializerEnableMaxDepthChecks=true)
     * @ParamConverter("message", converter="fos_rest.request_body")
     */
    public function updateAction(Message $message)
    {
        $em = $this->getDoctrine()->getManager();
        $em->merge($message);
        $em->flush();

        return $message;
    }

    /**
     * Delete Message by id
     *
     * @Security("has_role('ROLE_ADMIN')")
     * @Rest\Delete("/delete/{id}", name="delete_message")
     * @Rest\View(StatusCode = 200)
     */
    public function deleteAction(Message $message = null, $id)
    {
        if (empty($message)) {
            throw new EntityNotFoundException('message with id : ' . $id . ' was not found.');
        }

        $em = $this->getDoctrine()->getManager();
        $em->remove($message);
        $em->flush();

        return new JsonResponse(sprintf("message with id: %s  was removed.", $id), 200);
    }

}
