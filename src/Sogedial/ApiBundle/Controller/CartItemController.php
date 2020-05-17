<?php

namespace Sogedial\ApiBundle\Controller;

use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Controller\Annotations\QueryParam;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Sogedial\ApiBundle\Entity\Cart;
use Sogedial\ApiBundle\Entity\CartItem;
use Sogedial\ApiBundle\Entity\Item;
use Sogedial\ApiBundle\Entity\Attribut;
use Sogedial\ApiBundle\Exception\BadRequestException;
use Sogedial\ApiBundle\Exception\EntityNotFoundException;
use Sogedial\ApiBundle\Exception\Exception;
use Sogedial\ApiBundle\Exception\ParametersException;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use function GuzzleHttp\json_decode;

/**
 * CartItem controller.
 *
 * @Rest\Route(path="/api")
 */
class CartItemController extends Controller {

    const LIMIT = 60;

    /**
     * Lists all CartItem entities.
     *
     * @Security("has_role('ROLE_CUSTOMER') or has_role('ROLE_ADMIN') or has_role('ROLE_COMMERCIAL')")
     * @Rest\Get("/cart/{cart}/item", name="get_all_cart_item")
     * @Rest\View(StatusCode = 200, serializerEnableMaxDepthChecks=true)
     * @QueryParam(name="offset", requirements="\d+", default="0", description="Index de début de la pagination")
     * @QueryParam(name="limit", requirements="\d+", default="60", description="Nombre d'éléments à afficher")
     * @QueryParam(name="orderBy", default=null, description="nom de champ de l'ordre")
     * @QueryParam(name="orderByDesc", default=null, description="nom de champ de l'ordre descendant")
     * @QueryParam(name="filter", default=null, description="filtre sur les champs")
     *
     */
    public function getAllAction(String $cart, $orderBy = null, $orderByDesc = null, $limit = self::LIMIT, $offset = 0, $filter = null) {
        $order = 'asc';
        $em = $this->getDoctrine()->getManager();
        $repoInjector = $this->get('sogedial.repository_injecter');

        if ($orderBy == null) {
            //$meta = $em->getClassMetadata(CartItem::class);
            //$orderBy = $meta->getSingleIdentifierFieldName();
            $orderBy = "categories.name";
        }

        if ($orderByDesc != null) {
            $orderBy = $orderByDesc;
            $order = 'desc';
        }

        $filter = ($filter != null) ? json_decode($filter, true) : [];

        $filter['cart'] = $cart;

        return $repoInjector->getRepository('SogedialApiBundle:CartItem')->findBy($filter, [$orderBy => $order], $limit, $offset);
    }

    /**
     * create a new CartItem entity.
     * @Security("has_role('ROLE_CUSTOMER')")
     * @Rest\Post("/cart/{cart}/item/add/{item}", name="add_cart_item")
     * @Rest\View(StatusCode = 201, serializerEnableMaxDepthChecks=true)
     * @ParamConverter("cartItem", converter="fos_rest.request_body")
     */
    public function addAction(Cart $cart, Item $item = null, CartItem $cartItem) {
        $em = $this->getDoctrine()->getManager();

        if (!$item || $item == null)
            throw new ParametersException(['item']);

        if ($cart->getIsPreorder() != $item->getIsPreorder()) {
            throw new BadRequestException('you can not add this item to cart');
        }

        /* Check if this item has Already been added to this cart */
        $addCheck = true;
        if ($item !== null) {
            $dbCartItem = $cart->getCartItemByItem($item);
            if ($dbCartItem) {
                $dbCartItem->setQuantity($cartItem->getQuantity());
                $cartItem = $dbCartItem;
                $addCheck = false;
            } else {
                $cartItem->setItem($item);
                $cart->addCartItem($cartItem);
            }
        }

        $cartItem->setCart($cart);

        //il faut faire la verification ici apres l'attribution du item et cart
        if($cart->getStatus() != Cart::STATUS_CUSTOM && $cart->getStatus() != Cart::STATUS_CURRENT_PREORDER) {
            if (!$cartItem->allowedToOrder()) {
                throw new Exception('insufficient stock !');
            }
        }
        if($addCheck) {
            $this->get('sogedial.container_manager')->loadContainerCart($cartItem);
        }else{
            $this->get('sogedial.container_manager')->updateQuantity($cartItem);
        }


        $em->persist($cartItem);
        $em->flush();


        /* Reload to have cart amounts updates */
        return $em->getRepository('SogedialApiBundle:CartItem')->find($cartItem->getId());
    }

    /**
     * Count CartItem available after filter
     *
     * @Security("has_role('ROLE_CUSTOMER') or has_role('ROLE_ADMIN') or has_role('ROLE_COMMERCIAL')")
     * @Rest\Get("/cart/{cart}/item/count", name="count_cart_item")
     * @Rest\View(StatusCode = 200)
     * @QueryParam(name="filter", default=null, description="filtre sur les champs")
     */
    public function countAction($filter = null, String $cart) {
        $repoInjector = $this->get('sogedial.repository_injecter');
        $filter = ($filter != null) ? json_decode($filter, true) : [];
        
        $filter['cart'] = $cart;

        return $repoInjector->getRepository('SogedialApiBundle:CartItem')->getCount($filter);
    }

    /**
     * Finds and displays a CartItem entity.
     * @Security("has_role('ROLE_SUPER_ADMIN')")
     * @Rest\Get("/cart/item/{id}", name="get_cart_item")
     * @Rest\View(statusCode = 200, serializerEnableMaxDepthChecks=true)
     */
    public function getAction(CartItem $cartItem = null, $id) {
        if (empty($cartItem)) {
            throw new EntityNotFoundException('cartItem with id : ' . $id . ' was not found.');
        }

        return $cartItem;
    }

    /**
     * Displays a form to edit an existing CartItem entity.
     * @Security("has_role('ROLE_CUSTOMER')")
     * @Rest\Put("/cart/item/update", name="update_cart_item")
     * @Rest\View(StatusCode = 200, serializerEnableMaxDepthChecks=true)
     * @ParamConverter("cartItem", converter="fos_rest.request_body")
     */
    public function updateAction(CartItem $cartItem) {

        if (!$cartItem->allowedToOrder()) {
            throw new Exception('insufficient stock !');
        }

        $em = $this->getDoctrine()->getManager();
        $this->get('sogedial.container_manager')->updateQuantity($cartItem);

        $em->merge($cartItem);
        $em->flush();

        /* Reload to have cart amounts updates */
        return $em->getRepository('SogedialApiBundle:CartItem')->find($cartItem->getId());
    }

    /**
     * Delete CartItem by id
     * @Security("has_role('ROLE_CUSTOMER')")
     * @Rest\Delete("/cart/item/delete/{id}", name="delete_cart_item")
     * @Rest\View(StatusCode = 200, serializerEnableMaxDepthChecks= true)
     */
    public function deleteAction(CartItem $cartItem = null, $id) {
        if (empty($cartItem)) {
            throw new EntityNotFoundException('cartItem with id : ' . $id . ' was not found.');
        }
        $em = $this->getDoctrine()->getManager();

        $cart = $cartItem->getCart();
        $cart->removeCartItem($cartItem);
        $em->persist($cart);
        $em->flush();
        $em->refresh($cart);

        $this->get('sogedial.container_manager')->deloadContainer($cartItem);
        $em->remove($cartItem);
        $em->flush();

        return $cart;
    }

}
