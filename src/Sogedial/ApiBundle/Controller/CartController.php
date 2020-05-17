<?php

namespace Sogedial\ApiBundle\Controller;

use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Controller\Annotations\QueryParam;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Sogedial\ApiBundle\Entity\Cart;
use Sogedial\ApiBundle\Entity\CartItem;
use Sogedial\ApiBundle\Exception\EntityNotFoundException;
use Sogedial\ApiBundle\Exception\ForbiddenException;
use Sogedial\ApiBundle\Exception\ParametersException;
use Sogedial\OAuthBundle\Entity\UserCustomer;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;

/**
 * Cart controller.
 *
 * @Rest\Route(path="/api/cart")
 */
class CartController extends Controller
{

    const LIMIT = 10;

    /**
     * Lists all Cart entities.
     *
     * @Security("has_role('ROLE_CUSTOMER') or has_role('ROLE_ADMIN') or has_role('ROLE_COMMERCIAL')")
     * @Rest\Get("", name="get_all_cart")
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
            throw  new ParametersException(array('platform'));
        }

        $order = 'asc';
        $em = $this->getDoctrine()->getManager();

        if ($orderBy == null) {
            $meta = $em->getClassMetadata(Cart::class);
            $orderBy = $meta->getSingleIdentifierFieldName();
        }

        if ($orderByDesc != null) {
            $orderBy = $orderByDesc;
            $order = 'desc';
        }


        $repo = $this->get('sogedial.repository_injecter')->getRepository(Cart::class);
        $res = $repo->findBy($filter, [$orderBy => $order], $limit, $offset);

        $this->purgeCart($res);

        return $res;
    }

    /**
     * Count Cart available after filter
     *
     * @Security("has_role('ROLE_CUSTOMER') or has_role('ROLE_ADMIN') or has_role('ROLE_COMMERCIAL')")
     * @Rest\Get("/count", name="count_cart")
     * @Rest\View(StatusCode = 200)
     * @QueryParam(name="filter", default=null, description="filtre sur les champs")
     */
    public function countAction($filter = null)
    {
        $filter = ($filter != null) ? json_decode($filter, true) : [];

        return $this->get('sogedial.repository_injecter')->getRepository(Cart::class)->getCount($filter);
    }

    /**
     * NOTE: let this action before getAction to avoid error
     *
     * @Security("has_role('ROLE_ADMIN') or has_role('ROLE_COMMERCIAL')")
     * @Rest\Get(path="/moq" ,name="get_moq_to_validate")
     * @Rest\View(statusCode= 200, serializerGroups={"moq"})
     */
    public function getMoqToValidate()
    {
        $cartItems = $this->get('sogedial.repository_injecter')->getRepository(CartItem::class)->findBy(array('status' => CartItem::STATUS_MOQ_PREORDER), array('item' => 'asc'));
        $data = array();
        foreach ($cartItems as $cartItem) {
            /** @var CartItem $cartItem */
            $item = $cartItem->getItem();
            if (!array_key_exists($item->getId(), $data)) {
                $data[$item->getId()]['item'] = $item;
            }
            $data[$item->getId()]['cartItems'][] = $cartItem;
        }
        $data = array_values($data);

        return $data;
    }

    /**
     * Finds and displays a Cart entity.
     *
     * @Security("has_role('ROLE_CUSTOMER') or has_role('ROLE_ADMIN') or has_role('ROLE_COMMERCIAL')")
     * @Rest\Get("/{id}", name="get_cart")
     * @Rest\View(statusCode = 200, serializerEnableMaxDepthChecks=true)
     * @QueryParam(name="filter", default=null, description="filtre sur les champs")
     */
    public function getAction(Cart $cart = null, $id, $filter = null)
    {
        if ($id == "current" || $id == "current_preorder" ) {
            $filter = ($filter != null) ? json_decode($filter, true) : [];

            if (!isset($filter['platform']))
                throw new ParametersException(['platform']);

            $filter['user'] = $this->getUser()->getId();

            if ($id == "current") {
                $filter['status'] = Cart::STATUS_CURRENT;
            } elseif ($id == "current_preorder") {
                $filter['status'] = Cart::STATUS_CURRENT_PREORDER;
            }

            $cart = $this->get('sogedial.repository_injecter')->getRepository(Cart::class)->findBy($filter);
            if ($cart) $cart = $cart[0];
        }

        if (empty($cart)) {
            throw new EntityNotFoundException('cart with id : ' . $id . ' was not found.');
        }

        $this->purgeCart(array($cart));

        return $cart;
    }

    /**
     * create a new Cart entity.
     *
     * @Security("has_role('ROLE_CUSTOMER')")
     * @Rest\Post("/add", name="add_cart")
     * @Rest\View(StatusCode = 201, serializerEnableMaxDepthChecks=true)
     * @ParamConverter("cart", converter="fos_rest.request_body")
     */
    public function addAction(Cart $cart)
    {
        $em = $this->getDoctrine()->getManager();
        $cart->setUser($this->getUser());
        $cart->setOrderer($this->get('sogedial.cart_order_converter')->getOriginalUser());

        if (
            $cart->getStatus() !== Cart::STATUS_CURRENT &&
            $cart->getStatus() !== Cart::STATUS_CUSTOM &&
            $cart->getStatus() !== Cart::STATUS_TRANSFORMED &&
            $cart->getStatus() !== Cart::STATUS_CURRENT_PREORDER &&
            $cart->getStatus() !== Cart::STATUS_CUSTOM_PREORDER

        ) throw new ParametersException(['status']);

        $cartStatus = $cart->getStatus();        
        if ($cartStatus === Cart::STATUS_CURRENT || $cartStatus === Cart::STATUS_CURRENT_PREORDER) {            
            $dbCart = $this->get('sogedial.repository_injecter')->getRepository(Cart::class)->findBy(array('status' => $cartStatus, 'user' => $this->getUser(), 'platform' => $cart->getPlatform()->getId()));            
            if ($dbCart) {
                return $dbCart[0];
            }
        }

        $em->persist($cart);
        $em->flush();

        return $cart;
    }

    /**
     * Displays a form to edit an existing Cart entity.
     *
     * @Security("has_role('ROLE_CUSTOMER') or has_role('ROLE_ADMIN') or has_role('ROLE_COMMERCIAL')")
     * @Rest\Put("/update", name="update_cart")
     * @Rest\QueryParam(name="reset", nullable=true, default= false ,description="to reset quantity of all cartItems")
     * @Rest\View(StatusCode = 200, serializerEnableMaxDepthChecks=true)
     * @ParamConverter("cart", converter="fos_rest.request_body")
     */
    public function updateAction(Cart $cart, $reset)
    {
        $em = $this->getDoctrine()->getManager();

        if($reset == true){
            $cartItems = $cart->getCartItems();
            foreach ($cartItems as $cartItem){
                /** @var CartItem $cartItem */
                $cartItem->setQuantity(0);
            }
        }
        
        $em->merge($cart);
        $em->flush();

        return $cart;
    }

    /**
     * Delete Cart by id
     *
     * @Security("has_role('ROLE_CUSTOMER')")
     * @Rest\Delete("/delete/{id}", name="delete_cart")
     * @Rest\View(StatusCode = 200)
     */
    public function deleteAction(Cart $cart = null, $id)
    {
        if (empty($cart)) {
            throw new EntityNotFoundException('cart with id : ' . $id . ' was not found.');
        }

        $em = $this->getDoctrine()->getManager();
        $em->remove($cart);
        $em->flush();

        return new JsonResponse(sprintf("cart with id: %s  was removed.", $id), 200);
    }

    /**
     *
     * @Security("has_role('ROLE_CUSTOMER')")
     * @Rest\Post(path="/copy/{cartToCopy}", name="copy_cart")
     * @Rest\View(serializerEnableMaxDepthChecks=true, statusCode=201)
     * @ParamConverter("cartToSave", class="array", converter="fos_rest.request_body")
     */
    public function copyAction(array $cartToSave, Cart $cartToCopy)
    {
        if (!isset($cartToSave["status"])) {
            throw new ParametersException(['status']);
        }

        /**
         * Check if the cartType is yours
         */
        if ($this->getUser() !== $cartToCopy->getUser()) {
            throw new ForbiddenException('Forbidden to use this Cart!');
        }

        $newStatus = $cartToSave["status"];
        $newName = (isset($cartToSave["name"])) ? $cartToSave["name"] : "";

        $em = $this->getDoctrine()->getManager();

        $copyCart = clone $cartToCopy;
        $copyCart->setDateCreate(new \DateTime());

        foreach ($copyCart->getCartItems() as $cartItem) {
            if(!$cartItem->allowedToOrder()){
                $cartItem->setQuantity(0);
            }
            $cartItem->setCart($copyCart);
            $em->persist($cartItem);
            $em->flush();
        }

        //traitment specifique pour le cas ou le parametre newStatus est CURRENT
        if ($newStatus == Cart::STATUS_CURRENT
            || $newStatus == Cart::STATUS_CURRENT_PREORDER) {
            $copyCart->setName('');
            //verifier s il existe autre cart CURRENT appartient au meme user et au meme platform
            $currentCarts = $this->get('sogedial.repository_injecter')->getRepository(Cart::class)
            ->findBy(array('status' => $newStatus, 'user' => $cartToCopy->getUser(), 'platform' => $cartToCopy->getPlatform()->getId()));
            
            if ($currentCarts) {
                foreach ($currentCarts as $currentCart) {
                    $currentCart->setStatus(Cart::STATUS_ARCHIVED);
                    $em->persist($currentCart);
                    $em->flush();
                }
            }
        } else {
            $copyCart->setName($newName);
        }
        $copyCart->setStatus($newStatus);
        //ajout si il ya des cas specifique pour d autre cas de status demandé

        $em->persist($copyCart);
        $em->flush();

        return $copyCart;
    }


    /**
     *
     * @Security("has_role('ROLE_CUSTOMER') or has_role('ROLE_ADMIN') or has_role('ROLE_COMMERCIAL')")
     * @Rest\Put(path="/validate", name="validate_cart")
     * @QueryParam(name="checkStockDisponibility", nullable= true, description="avoid checking the availability of stock items")
     * @ParamConverter("cart", converter="fos_rest.request_body")
     * @Rest\View(serializerEnableMaxDepthChecks=true, statusCode=201)
     */
    public function validateCart(Cart $cart, $checkStockDisponibility = null)
    {
        return $this->get('sogedial.cart_order_converter')->validateCart($cart, $checkStockDisponibility);
    }


    /**
     * @Security("has_role('ROLE_ADMIN') or has_role('ROLE_COMMERCIAL')")
     * @Rest\Put(name="validate_moq", path="/moq/validate")
     * @ParamConverter(name="data", class="array", converter= "fos_rest.request_body")
     * @Rest\View(statusCode= 200)
     */
    public function validateMoq($data)
    {
        $em = $this->getDoctrine()->getManager();
        /**
         * $key => id du cartItem
         * $value => quantity
         */
        foreach ($data as $key => $value) {
            $cartItem = $em->getRepository('SogedialApiBundle:CartItem')->find($key);            
            if($cartItem && $cartItem->getStatus() == CartItem::STATUS_MOQ_PREORDER){
                $cartItem->setQuantity($value);
                $cartItem->setStatus(CartItem::STATUS_STAND_BY_VALIDATION_PREORDER);
                $em->merge($cartItem);

                //get Cart pour verifier si tous ses cartItems sont validés ou nn , si oui passer la cart vers status standByValidationPreorder
                /** @var Cart $cart */
                $cart = $cartItem->getCart();
                $countNotValidatedCartItem = $cart->countNotValidatedCartItems();
                if($countNotValidatedCartItem == 0 ){
                    $cart->setStatus(Cart::STATUS_STAND_BY_VALIDATION_PREORDER);
                    $em->merge($cart);
                }
            }
        }

        $em->flush();
    }

    private function purgeCart(array $carts)
    {
        if(!$this->getUser() instanceof UserCustomer){
            $arrayStatusPreorder = array(
                Cart::STATUS_MOQ_PREORDER,
                Cart::STATUS_STAND_BY_VALIDATION_PREORDER
            );
            $arrayStatusOrder = array();
        }else{
            $arrayStatusPreorder = array(
                Cart::STATUS_CUSTOM_PREORDER,
                Cart::STATUS_CURRENT_PREORDER,
            );
            $arrayStatusOrder = array(Cart::STATUS_CUSTOM, Cart::STATUS_CURRENT);
        }

        $this->filterItem($carts, $arrayStatusOrder, $arrayStatusPreorder);
    }

    /**
     * @param $carts
     * @param $arrayStatusOrder
     * @param $arrayStatusPreorder
     */
    private function filterItem($carts, $arrayStatusOrder, $arrayStatusPreorder): void
    {
        $em = $this->getDoctrine()->getManager();
        foreach ($carts as $cart) {
            /** @var Cart $cart */
            $status = $cart->getStatus();
            //recuperer les cartItems non adequates
            $cartItemsTopurge = $cart->getCartItems()->filter(
                function ($elem) use ($status, $arrayStatusOrder, $arrayStatusPreorder) {
                    if (in_array($status, $arrayStatusOrder)) {
                        //get cartItem with item preorder
                        if ($elem->getItem()->getIsPreorder() == true) {
                            return true;
                        }
                    } elseif (in_array($status, $arrayStatusPreorder)) {
                        //get cartItem with item current
                        if ($elem->getItem()->getIsPreorder() == false) {
                            return true;
                        }
                    }
                    return false;
                }
            );

            //on a les cartItems to delete or deplacer
            if (!$cartItemsTopurge->isEmpty()) {
                //on deplace les cartItem to adequat cart
                if($status == Cart::STATUS_CURRENT_PREORDER || $status == Cart::STATUS_CURRENT ){
                    $statusCartRecepient = ($status == Cart::STATUS_CURRENT) ? Cart::STATUS_CURRENT_PREORDER : Cart::STATUS_CURRENT;
                    $cartRecipient = $em->getRepository('SogedialApiBundle:Cart')->findOneBy(array('user' => $this->getUser(), 'platform' => $cart->getPlatform(), 'status' => $statusCartRecepient));
                    if (!is_null($cartRecipient)) {
                        /** @var CartItem $cartItem */
                        foreach ($cartItemsTopurge as $cartItem) {
                            $cart->removeCartItem($cartItem);
                            $cartRecipient->addCartItem($cartItem);
                            $cartItem->setCart($cartRecipient);
                        }
                    }else{
                        //si cartRecipient n existe pas alors on delete cartItem
                        foreach ($cartItemsTopurge as $cartItem) {
                            $em->remove($cartItem);
                        }
                    }
                }else{
                    foreach ($cartItemsTopurge as $cartItem) {
                        $em->remove($cartItem);
                    }
                }

                $em->flush();
            }
        }
    }


}
