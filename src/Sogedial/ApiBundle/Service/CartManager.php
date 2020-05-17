<?php

namespace Sogedial\ApiBundle\Service;

use DateTime;
use Doctrine\ORM\EntityManager;
use Sogedial\ApiBundle\Entity\Attribut;
use Sogedial\ApiBundle\Entity\Cart;
use Sogedial\ApiBundle\Entity\CartItem;
use Sogedial\ApiBundle\Entity\Historique;
use Sogedial\ApiBundle\Entity\Item;
use Sogedial\ApiBundle\Entity\Order;
use Sogedial\ApiBundle\Entity\OrderItem;
use Sogedial\ApiBundle\Exception\BadRequestException;
use Sogedial\ApiBundle\Exception\Exception;
use Sogedial\OAuthBundle\Mailer\Mailer;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorage;
use Symfony\Component\Security\Core\Authorization\AccessDecisionManagerInterface;
use Symfony\Component\Security\Core\Role\SwitchUserRole;

class CartManager {

    /**
     * @var TokenStorage
     */
    private $tokenStorage;

    /**
     * @var EntityManager
     */
    private $em;

    /**
     * @var AccessDecisionManagerInterface
     */
    private $decisionManager;

    /**
     * @var GenerateFileManager
     */
    private $generateFileManager;

    /**
     * @var mailer
     */
    private $mailer;

    /**
     * CartManager constructor.
     * @param TokenStorage $tokenStorage
     * @param EntityManager $em
     * @param AccessDecisionManagerInterface $accessDecision
     * @param GenerateFileManager $generateFileManager
     */
    public function __construct(TokenStorage $tokenStorage, EntityManager $em, AccessDecisionManagerInterface $accessDecision, GenerateFileManager $generateFileManager, Mailer $mailer) {
        $this->tokenStorage = $tokenStorage;
        $this->em = $em;
        $this->decisionManager = $accessDecision;
        $this->generateFileManager = $generateFileManager;
        $this->mailer = $mailer;
    }

    /**
     * @param Cart $cart
     * @return array | Cart
     * @throws Exception
     */
    public function validateCart(Cart $cart, $checkStockDisponibility)
    {

        if($cart->getStatus() == Cart::STATUS_TRANSFORMED ||  $cart->getStatus() == Cart::STATUS_MOQ_PREORDER){
            throw new BadRequestException('Cart already validated');
        }
        
          
        $this->checkDeliveryDates($cart);

        $this->inspectStockDisponibility($cart, $checkStockDisponibility);

        $hasMoqPlatform = $cart->getPlatform()->getAttributByKey(Attribut::KEY_HAS_MOQ);
        if ($hasMoqPlatform) {
            $hasMoqPlatformValue = $hasMoqPlatform->getValue();
        } else {
            $hasMoqPlatformValue = 0;
        }

        if ($hasMoqPlatformValue && $cart->getStatus() == Cart::STATUS_CURRENT_PREORDER && $cart->getCountItemMoqs() > 0) {
            $cartItems = $cart->getMoqedCartItems();

            foreach ($cartItems as $cartItem) {
                /** @var CartItem $cartItem */
                $cartItem->setStatus(CartItem::STATUS_MOQ_PREORDER);
                $this->em->merge($cartItem);
            }
            $cart->setStatus(Cart::STATUS_MOQ_PREORDER);
            $this->em->merge($cart);
            $this->em->flush();

            return $cart;
        }

        $deliveryModeAttribut = $cart->getPlatform()->getAttributByKey(Attribut::KEY_DELIVERY_MODE);
        if($deliveryModeAttribut){
            $deliveryModeValue = $deliveryModeAttribut->getValue();
        }else{
            $deliveryModeValue = 0;
        }

        if($deliveryModeValue) {
            if (!$this->checkDeliveryAmount($cart->calculateDeliveryAmount($cart->getDeliveryMode()), $cart->getDeliveryAmount())) {
                throw new BadRequestException('check delivery amount ! ');
            }
        }

        $splitOrderAttribut = $cart->getPlatform()->getAttributByKey(Attribut::KEY_SPLITTING_ORDER);
        if($splitOrderAttribut){
            $splitOrderValue = $splitOrderAttribut->getValue();
        }else{
            $splitOrderValue = 0;
        }

        $orders = $this->createOrderByType($cart, $splitOrderValue);

        if ($orders && $cart->getStatus() !== Cart::STATUS_TRANSFORMED) {
            $cart->setStatus(Cart::STATUS_TRANSFORMED);
            $this->em->merge($cart);
        }

        $this->em->flush();
        $this->mailer->sendBDC($orders, $cart->getUser(), $cart->getIsPreorder());

        return $orders;
    }

    /**
     * @param string $date "ddmmyyyy"
     * @return null|string "dd-mm-yyyy"
     */
    public function dateFormatHelper($date) {
        if ($date === '' || strlen($date) != 8) {

            return null;
        }

        $day = substr($date, 0, 2);
        $month = substr($date, 2, 2);
        $year = substr($date, -4);

        return sprintf('%s-%s-%s', $year, $month, $day);
    }

    /**
     * @param array $arrayStringDate
     * @param string $format
     * @return array
     * @throws BadRequestException
     */
    function validateDate(array $arrayStringDate, $format = 'Y-m-d') {
        $arrayDate = array();

        foreach ($arrayStringDate as $key => $value) {
            if (!empty($value)) {
                $dateFormatted = $this->dateFormatHelper($value);
                $d = DateTime::createFromFormat($format, $dateFormatted);

                if ($d && $d->format($format) === $dateFormatted) {
                    $arrayDate[$key] = $d;
                    continue;
                }

                throw new BadRequestException(sprintf('delivery date %s is invalid', $key));
            }
        }

        return $arrayDate;
    }

    /**
     * @param Cart $cart
     * @return array
     * @throws \Doctrine\Common\Persistence\Mapping\MappingException
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    protected function createOrderByType(Cart $cart, $splitValue) {
        $cartItems = $cart->getCartItems();

        $arrayOrder = array();
        if($splitValue == 0) {
            foreach ($cartItems as $cartItem) {
                $item = $cartItem->getItem();
                if ($item->getType() == Item::TEMPERATURE_DRY) {
                    if (!isset($orderSec)) {
                        $orderSec = new Order();
                        $arrayOrder[Item::TEMPERATURE_DRY] = $orderSec;
                    }
                    $this->createOrderItem($orderSec, $cartItem);
                } elseif ($item->getType() == Item::TEMPERATURE_FRESH) {
                    if (!isset($orderFrais)) {
                        $orderFrais = new Order();
                        $arrayOrder[Item::TEMPERATURE_FRESH] = $orderFrais;
                    }
                    $this->createOrderItem($orderFrais, $cartItem);
                } elseif ($item->getType() == Item::TEMPERATURE_FROZEN) {
                    if (!isset($orderSurgele)) {
                        $orderSurgele = new Order();
                        $arrayOrder[Item::TEMPERATURE_FROZEN] = $orderSurgele;
                    }
                    $this->createOrderItem($orderSurgele, $cartItem);
                }
            }
        }else{
            //no splite
            $order = new Order();
            foreach ($cartItems as $cartItem){
                $this->createOrderItem($order, $cartItem);
            }
            $arrayOrder[] = $order;
        }

        return $this->updateOrder($arrayOrder, $cart, $splitValue);
    }

    /**
     * @param array $arrayOrder
     * @param Cart $cart
     * @return array
     * @throws \Doctrine\Common\Persistence\Mapping\MappingException
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    protected function updateOrder(array $arrayOrder, Cart $cart, $splitValue) {
        $approvedOrder = [];
        $totalByType = $cart->getTotalsByType();

        $deliveryDatesByType = $cart->getDeliveryDatesByType();
        $validator = $this->getOriginalUser();

        foreach ($arrayOrder as $key => $order) {
            $order->setComment($cart->getComment());
            $order->setUser($cart->getUser());
            $order->setValidator($validator);
            $order->setPlatform($cart->getPlatform());
            $order->setDateValidate(new DateTime());
            $order->setDeliveryMode($cart->getDeliveryMode());
            $order->setDeliveryAmount($cart->getDeliveryAmount());
            $order->setOrderer($cart->getOrderer());

            if($splitValue == 0) {
                //splite
                if ($cart->getDeliveryMode() !== null) {
                    $order->setTotalPrice($totalByType[$key]['total_price'] + $cart->getDeliveryAmount());
                    $order->setTotalPriceVat($totalByType[$key]['total_price_vat'] + $cart->getDeliveryAmount());
                } else {
                    $order->setTotalPrice($totalByType[$key]['total_price']);
                    $order->setTotalPriceVat($totalByType[$key]['total_price_vat']);
                }
                $order->setDateDelivery($deliveryDatesByType[$key]);
            }else{
                //TODO no splite ,  check si dans ce cas tous les date de livraisons prendre le meme valeurs
                foreach($deliveryDatesByType as $value){
                    if(isset($value)){
                        $order->setDateDelivery($value);
                        break;
                    }
                }
                if ($cart->getDeliveryMode() !== null) {
                    $order->setTotalPrice($cart->getTotalPrice() + $cart->getDeliveryAmount());
                    $order->setTotalPriceVat($cart->getTotalPriceVat() + $cart->getDeliveryAmount());
                } else {
                    $order->setTotalPrice($cart->getTotalPrice());
                    $order->setTotalPriceVat($cart->getTotalPriceVat());
                }
            }

            /**
             * update status and historique of order
             */
            $order->setStatus($this->em->getRepository('SogedialApiBundle:Status')->findOneBy(array('name' => Order::STATUS_APPROVED)));

            $historique = new Historique();
            $historique->setOrder($order);
            $historique->setStatus(Order::STATUS_PENDING_VALIDATION);
            $order->addHistorique($historique);

            $order->setIsPreorder($cart->getIsPreorder());

            if ($cart->getIsPreorder()) {
                $preOrderDateBeginAttribut = $order->getPlatform()->getAttributByKey(Attribut::KEY_PREORDER_DATE_BEGIN);

                $preOrderDurationAttribut = $order->getPlatform()->getAttributByKey(Attribut::KEY_PREORDER_DURATION);

                $preOrderDeliveryAttribut = $order->getPlatform()->getAttributByKey(Attribut::KEY_PREORDER_DELIVERY);
                $now = new DateTime();
                $preOrderDateBegin = new DateTime($preOrderDateBeginAttribut->getValue());
                $preOrderDateEnd = clone $preOrderDateBegin;
                $preOrderDateEnd->modify('+' . $preOrderDurationAttribut->getValue() . ' day');
                $preDeliveryDate = clone $preOrderDateEnd;
                $preDeliveryDate->modify('+' . $preOrderDeliveryAttribut->getValue() . ' day');
                while (!(($now >= $preOrderDateBegin ) && ($now <= $preOrderDateEnd))) {
                    $preOrderDateBegin = clone $preOrderDateEnd;
                    $preOrderDateEnd = clone $preOrderDateBegin;
                    $preOrderDateEnd->modify('+' . $preOrderDurationAttribut->getValue() . ' day');
                    $preDeliveryDate = clone $preOrderDateEnd;
                    $preDeliveryDate->modify('+' . $preOrderDeliveryAttribut->getValue() . ' day');
                }
                if ($preOrderDateEnd != null) {
                    $order->setPreOrderDateEnd($preOrderDateEnd);
                }
                if ($preDeliveryDate != null) {
                    $order->setPreOrderDeliveryDate($preDeliveryDate);
                }
            }
            $this->em->persist($order);

            $approvedOrder[] = $order;
        }

        $this->em->flush();

        $finalOrders = [];
        foreach($approvedOrder as $order){
            $finalOrders[$order->getId()] = $order;
        }        

        /**
         * Generate AS400 IBM FILE         
         */
        $this->generateFileManager->generateIbmFile($finalOrders);

        return $arrayOrder;
    }

    /**
     * @param OrderItem $orderItem
     * @param Item $item
     */
    protected function captureItem(OrderItem $orderItem, Item $item) {
        $orderItem->setItemPrice($item->getPrice());
        $orderItem->setItemPriceVat($item->getPriceVat());
        $orderItem->setItemVat($item->getVat());
        $orderItem->setItemReference($item->getReference());
        $orderItem->setItemType($item->getType());
        $orderItem->setItemName($item->getName());
        $orderItem->setItemEan13($item->getEan13());
        $orderItem->setItemWeight($item->getWeight());
        $orderItem->setItemPcb($item->getPcb());
        $orderItem->setItemUnity($item->getUnity());
        $orderItem->setItemUpc($item->getUpc());
        $orderItem->setItemActive($item->getActive());
        $orderItem->setItemDateBegin($item->getDateBegin());
        $orderItem->setItemDateEnd($item->getDateEnd());
        $orderItem->setItemIsPreorder($item->getIsPreorder());
        $orderItem->setItemCodeNature($item->getCodeNature());
        $orderItem->setItemFrequency($item->getFrequency());
        $orderItem->setItemMoq($item->getMoq());
        $orderItem->setItemCode($item->getCode());
        $orderItem->setItemIsNew($item->getIsNew());
        $orderItem->setItemHasPromotion($item->getHasPromotion());

        if ($item->getCategory()) {
            $orderItem->setItemCategoryName($item->getCategory()->getName());
        }

        if ($item->getManufacturer()) {
            $orderItem->setItemManufacturerName($item->getManufacturer()->getName());
        }
    }

    /**
     * @param Order $order
     * @param CartItem $cartItem
     * @throws \Doctrine\ORM\ORMException
     */
    protected function createOrderItem(Order $order, CartItem $cartItem) {
        $orderItem = new OrderItem();
        //fix error of count in reference
        $order->addOrderItem($orderItem);

        $orderItem->setOrder($order);
        $orderItem->setItem($cartItem->getItem());
        $orderItem->setQuantity($cartItem->getQuantity());
        $orderItem->setFinalPrice($cartItem->getTotalPrice());
        $orderItem->setFinalPriceVat($cartItem->getTotalPriceVat());

        $degressive = $cartItem->getDegressive();
        if ($degressive != null) {
            $orderItem->setDegressiveStep($degressive->getStep());
            $orderItem->setDegressivePrice($degressive->getAmount());
        }

        $this->captureItem($orderItem, $cartItem->getItem());
        $this->em->persist($orderItem);
    }


    /**
     * @return object|string
     */
    public function getOriginalUser() {
        if ($this->decisionManager->decide($this->tokenStorage->getToken(), ['ROLE_PREVIOUS_ADMIN'])) {
            $roles = $this->tokenStorage->getToken()->getRoles();
            foreach ($roles as $role) {
                if ($role instanceof SwitchUserRole) {

                    return $role->getSource()->getUser();

                }
            }
        }

        return $this->tokenStorage->getToken()->getUser();
    }

    private function checkDeliveryAmount($calculatedDeliveryAmount, $receivedDelilveryAmount)
    {
        $epsilon = 0.00001;
        if(abs($calculatedDeliveryAmount - $receivedDelilveryAmount) < $epsilon){
            return true;
        }else{
            return false;
        }
    }

    private function checkDeliveryDates(Cart $cart)
    {
        if(!$cart->getIsPreorder()){
            $countItemsByType = $cart->getCountItemsByType();
            $deliveryDatesByType = $cart->getDeliveryDatesByType();
            foreach ($countItemsByType as $key => $value){
                if(!isset($deliveryDatesByType[$key])){
                    throw new BadRequestException('missing a delivery date');
                }
            }
        }
    }


    /**
     * @param Cart $cart
     * @param null $checkStockDisponibility
     */
    private function inspectStockDisponibility(Cart $cart, $checkStockDisponibility = null)
    {
        $cartItems = $cart->getCartItems();
        $itemsNotAvailable = $this->getItemsNotAvailable($cartItems);
        if($cartItems->count() == count($itemsNotAvailable)){
            throw new BadRequestException('all items are unavailable !');
        }
        $this->applyCheckDisponibility($cart, $checkStockDisponibility, $itemsNotAvailable);
    }

    /**
     * @param $cartItems
     * @return array
     */
    private function getItemsNotAvailable($cartItems){
        $itemsNotAvailable = [];
        foreach ($cartItems as $cartItem) {
            if (!$cartItem->allowedToOrder()) {
                $itemsNotAvailable[] = $cartItem->getId();
            }
        }
        return $itemsNotAvailable;
    }

    /**
     * throw exception if flag checkStockDisponibility does not exist
     * or delete item not available from cart if flag exist
     *
     * @param Cart $cart
     * @param $checkStockDisponibility
     * @param $itemsNotAvailable
     * @throws BadRequestException
     */
    private function applyCheckDisponibility(Cart $cart, $checkStockDisponibility, $itemsNotAvailable): void
    {
        if (count($itemsNotAvailable) > 0) {
            if (!isset($checkStockDisponibility)) {
                throw new BadRequestException('You are not allowed to order one of those item');
            } elseif (isset($checkStockDisponibility) && $checkStockDisponibility == true) {
                $cartItems = $cart->getCartItems();
                /** @var CartItem $cartItem */
                foreach ($cartItems as $cartItem) {
                    if (in_array($cartItem->getId(), $itemsNotAvailable)) {
                        $cart->removeCartItem($cartItem);
                        $this->em->remove($cartItem);
                    }
                }
            }
        }
    }

}
