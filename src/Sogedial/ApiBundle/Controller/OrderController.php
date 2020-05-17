<?php

namespace Sogedial\ApiBundle\Controller;

use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Controller\Annotations\QueryParam;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Entity;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Sogedial\ApiBundle\Entity\Attribut;
use Sogedial\ApiBundle\Entity\Cart;
use Sogedial\ApiBundle\Entity\CartItem;
use Sogedial\ApiBundle\Entity\Item;
use Sogedial\ApiBundle\Entity\Order;
use Sogedial\ApiBundle\Entity\Stock;
use Sogedial\ApiBundle\Exception\EntityNotFoundException;
use Sogedial\ApiBundle\Exception\ForbiddenException;
use Sogedial\ApiBundle\Exception\ParametersException;
use Sogedial\OAuthBundle\Entity\UserCustomer;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;

/**
 * Order controller.
 *
 * @Rest\Route(path="/api/order")
 */
class OrderController extends Controller
{
    const LIMIT = 10;

    /**
     * Lists all Order entities.
     *
     * @Rest\Get("", name="get_all_order")
     * @Rest\View(StatusCode = 200, serializerEnableMaxDepthChecks=true)
     * @QueryParam(name="offset", requirements="\d+", default="0", description="Index de début de la pagination")
     * @QueryParam(name="limit", requirements="\d+", default="10", description="Nombre d'éléments à afficher")
     * @QueryParam(name="orderBy", default=null, description="nom de champ de l'ordre")
     * @QueryParam(name="orderByDesc", default=null, description="nom de champ de l'ordre descendant")
     * @QueryParam(name="filter", default=null, description="filtre sur les champs")
     */
    public function getAllAction($orderBy = null, $orderByDesc = null, $limit = self::LIMIT, $offset = 0, $filter = null)
    {
        $filter = ($filter != null) ? json_decode($filter, true) : [];
        if($this->getUser() instanceof UserCustomer && !isset($filter['platform'])){
            throw new ParametersException(array('platform'));
        }

        $order = 'asc';
        $em = $this->getDoctrine()->getManager();

        if ($orderBy == null) {
            $meta = $em->getClassMetadata(Order::class);
            $orderBy = $meta->getSingleIdentifierFieldName();
        }

        if ($orderByDesc != null) {
            $orderBy = $orderByDesc;
            $order = 'desc';
        }



        $rep = $this->get('sogedial.repository_injecter')->getRepository('SogedialApiBundle:Order');
        return $rep->findBy($filter, [$orderBy => $order], $limit, $offset);
    }

    /**
     * Count Order available after filter
     *
     * @Rest\Get("/count", name="count_order")
     * @Rest\View(StatusCode = 200)
     * @QueryParam(name="filter", default=null, description="filtre sur les champs")
     */
    public function countAction($filter = null)
    {
        $filter = ($filter != null) ? json_decode($filter, true) : [];

        return $this->get('sogedial.repository_injecter')->getRepository('SogedialApiBundle:Order')->getCount($filter);
    }

    /**
     * Finds and displays a Order entity.
     *
     * @Security("has_role('ROLE_SUPER_ADMIN')")
     * @Rest\Get("/{id}", name="get_order")
     * @Rest\View(statusCode = 200, serializerEnableMaxDepthChecks=true)
     */
    public function getAction(Order $order = null, $id)
    {
        if (empty($order)) {
            throw new EntityNotFoundException('order with id : '. $id . ' was not found.');
        }

        return $order;
    }

    /**
     * create a new Order entity.
     *
     * @Security("has_role('ROLE_SUPER_ADMIN')")
     * @Rest\Post("/add", name="add_order")
     * @Rest\View(StatusCode = 201, serializerEnableMaxDepthChecks=true)
     * @ParamConverter("order", converter="fos_rest.request_body")
     */
    public function addAction(Order $order)
    {
        $em = $this->getDoctrine()->getManager();
        $em->persist($order);
        $em->flush();

        return $order;
    }

    /**
     * Displays a form to edit an existing Order entity.
     *
     * @Security("has_role('ROLE_SUPER_ADMIN')")
     * @Rest\Put("/update", name="update_order")
     * @Rest\View(StatusCode = 200, serializerEnableMaxDepthChecks=true)
     * @ParamConverter("order", converter="fos_rest.request_body")
     */
    public function updateAction(Order $order)
    {
        $em = $this->getDoctrine()->getManager();
        $em->merge($order);
        $em->flush();

        return $order;
    }

    /**
     * Delete Order by id
     *
     * @Security("has_role('ROLE_SUPER_ADMIN')")
     * @Rest\Delete("/delete/{id}", name="delete_order")
     * @Rest\View(StatusCode = 200)
     */
    public function deleteAction(Order $order = null, $id)
    {
        if (empty($order)) {
            throw new EntityNotFoundException('order with id : '. $id . ' was not found.');
        }

        $em = $this->getDoctrine()->getManager();
        $em->remove($order);
        $em->flush();

        return new JsonResponse(sprintf("order with id: %s  was removed.", $id), 200);
    }


    /**
     * @Security("has_role('ROLE_SUPER_ADMIN') or has_role('ROLE_CUSTOMER')")
     * @Rest\Put("/renew/{id}", name="renew_order")
     * @Rest\View(statusCode=201, serializerEnableMaxDepthChecks=true)
     */
    public function renewAction(Order $order)
    {
        //check if the order is yours
        if ($this->getUser() instanceof UserCustomer && $this->getUser() !== $order->getUser()) {
            throw new ForbiddenException('Forbidden to use this order!');
        }

        //set newStatus of cart depending on type of order
        $newStatus = ($order->getIsPreorder()) ? Cart::STATUS_CURRENT_PREORDER : Cart::STATUS_CURRENT;

        //get cart by newStatus
        $rep = $this->get('sogedial.repository_injecter')->getRepository('SogedialApiBundle:Cart');
        $oldCart = $rep->findBy(array('status' => $newStatus, 'user' => $order->getUser(), 'platform' => $order->getPlatform()->getId()));

        if($oldCart){
            $oldCart[0]->setStatus(Cart::STATUS_ARCHIVED);
        }

        $newCart = new Cart();
        $newCart->setPlatform($order->getPlatform());
        $newCart->setUser($order->getUser());
        $newCart->setStatus($newStatus);

        $em = $this->getDoctrine()->getManager();

        //recuperer orderItrem selon status
        $orderItems = $order->getOrderItems()->filter(function ($elem) use ($newStatus) {
            if ($newStatus == Cart::STATUS_CURRENT_PREORDER) {
                if ($elem->getItem()->getIsPreorder() == true) {
                    return true;
                }
            } elseif ($newStatus == Cart::STATUS_CURRENT) {
                if ($elem->getItem()->getIsPreorder() == false) {
                    return true;
                }
            }
            return false;
        });

        foreach ($orderItems as $orderItem){
            $item = $orderItem->getItem();
            /** @var Item $item */
            if($item !== null && $item->getActive() == 1){
                $cartItem = new CartItem();
                $cartItem->setItem($item);
                $cartItem->setCart($newCart);
                $newCart->addCartItem($cartItem);
                $cartItem->setQuantity($orderItem->getQuantity());
                if(!$cartItem->allowedToOrder()){
                    $orderByAttribut = $order->getPlatform()->getAttributByKey(Attribut::KEY_ORDERED_BY);
                    $orderByValue = 1;
                    if ($orderByAttribut) {
                        $orderByValue = $orderByAttribut->getValue();
                    }

                    /** @var Stock $stock */
                    $stock = $item->getStock();
                    if($stock){
                        if($orderByValue == 1){
                            $cartItem->setQuantity($stock->getValuePacking());
                        }else{
                            $cartItem->setQuantity($stock->getValueCu());
                        }
                    }else{
                        $cartItem->setQuantity(0);
                    }
                }

                $em->persist($cartItem);
            }
        }

        $em->persist($newCart);
        $em->flush();

        return $newCart;
    }


    /**
     * generate a pdf file for the order
     *
     * @Rest\Get(path="/pdf/{id}" ,name="order_pdf")
     * @param Order $order
     */
    public function orderPdfAction(Order $order)
    {
        $toPdfService = $this->get('sogedial.generate_pdf_order');

        return $toPdfService->orderToPdf($order);
    }

}
