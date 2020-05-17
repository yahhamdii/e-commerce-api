<?php

namespace Sogedial\ApiBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as Serializer;

/**
 * Cart
 *
 * @ORM\Table(name="cart")
 * @ORM\Entity(repositoryClass="Sogedial\ApiBundle\Repository\CartRepository")
 * @Serializer\ExclusionPolicy("all")
 */
class Cart extends AbstractEntity
{
    const STATUS_CURRENT = 'CURRENT';
    const STATUS_CURRENT_PREORDER = 'CURRENT_PREORDER';
    const STATUS_CUSTOM = 'CUSTOM';
    const STATUS_CUSTOM_PREORDER = 'CUSTOM_PREORDER';
    const STATUS_TRANSFORMED = 'TRANSFORMED';
    const STATUS_ARCHIVED = 'ARCHIVED';
    const STATUS_PENDING_VALIDATION = 'PENDING_VALIDATION';
    const STATUS_MOQ_PREORDER  = 'MOQ_PREORDER';
    const STATUS_STAND_BY_VALIDATION_PREORDER = 'STAND_BY_VALIDATION_PREORDER';

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=255, nullable=true)
     * @Serializer\Expose
     */
    private $name;

    /**
     * @var string
     *
     * @ORM\Column(name="comment", type="text", nullable=true)
     * @Serializer\Expose
     * @Serializer\Groups({"add", "update","list", "detail", "listitems"})
     */
    private $comment;

    /**
     * @ORM\ManyToOne(targetEntity="Sogedial\OAuthBundle\Entity\User")
     * @ORM\JoinColumn(nullable=false)
     * @Serializer\Expose
     * @Serializer\Groups({"moq"})
     * @Serializer\MaxDepth(5)
     */
    private $user;

    /**
     * @ORM\Column(name="delivery_date_dry", type="datetime", nullable=true)
     * @Serializer\Type("DateTime<'Y-m-d'>")
     * @Serializer\Expose
     */
    private $deliveryDateDry;

    /**
     * @ORM\Column(name="delivery_date_fresh", type="datetime", nullable=true)
     * @Serializer\Type("DateTime<'Y-m-d'>")
     * @Serializer\Expose
     */
    private $deliveryDateFresh;

    /**
     * @ORM\Column(name="delivery_date_frozen", type="datetime", nullable=true)
     * @Serializer\Type("DateTime<'Y-m-d'>")
     * @Serializer\Expose
     */
    private $deliveryDateFrozen;

    /**
     * @ORM\Column(name="status", type="string", length=255, nullable=true)
     * @Serializer\Expose
     */
    private $status;

    /**
     * @ORM\ManyToOne(targetEntity="Platform")
     * @ORM\JoinColumn(nullable=true)
     * @Serializer\Expose
     * @Serializer\MaxDepth(1)
     */
    private $platform;

    /**
     * @ORM\OneToMany(targetEntity="CartItem", mappedBy="cart", cascade={"remove", "persist"})
     * @Serializer\Expose
     * @Serializer\MaxDepth(2)
     */
    private $cartItems;


    /**
     * @ORM\Column(name="delivery_mode" ,type="boolean" ,nullable=true)
     * @Serializer\Expose()
     */
    private $deliveryMode;

    /**
     * @ORM\Column(name="delivery_amount", type="decimal", precision=8, scale=2, nullable=true)
     * @Serializer\Expose
     */
    private $deliveryAmount;


    /**
     * @ORM\ManyToOne(targetEntity="Sogedial\OAuthBundle\Entity\User")
     * @Serializer\Expose
     * @Serializer\MaxDepth(1)
     */
    private $orderer;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->cartItems = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Set user.
     *
     * @param \Sogedial\OAuthBundle\Entity\User $user
     *
     * @return Cart
     */
    public function setUser(\Sogedial\OAuthBundle\Entity\User $user)
    {
        $this->user = $user;

        return $this;
    }

    /**
     * Get user.
     *
     * @return \Sogedial\OAuthBundle\Entity\User
     */
    public function getUser()
    {
        return $this->user;
    }
    /**
     * Set dateCreate.
     *
     * @param \DateTime|null $dateCreate
     *
     * @return Cart
     */
    public function setDateCreate($dateCreate = null)
    {
        $this->dateCreate = $dateCreate;

        return $this;
    }

    /**
     * Get dateCreate.
     *
     * @return \DateTime|null
     */
    public function getDateCreate()
    {
        return $this->dateCreate;
    }

    /**
     * Set dateUpdate.
     *
     * @param \DateTime|null $dateUpdate
     *
     * @return Cart
     */
    public function setDateUpdate($dateUpdate = null)
    {
        $this->dateUpdate = $dateUpdate;

        return $this;
    }

    /**
     * Get dateUpdate.
     *
     * @return \DateTime|null
     */
    public function getDateUpdate()
    {
        return $this->dateUpdate;
    }

    /**
     * Set name
     *
     * @param string $name
     *
     * @return Cart
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set status
     *
     * @param string $status
     *
     * @return Cart
     */
    public function setStatus($status)
    {
        $this->status = $status;

        return $this;
    }

    /**
     * Get status
     *
     * @return string
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * Set platform
     *
     * @param \Sogedial\ApiBundle\Entity\Platform $platform
     *
     * @return Cart
     */
    public function setPlatform(\Sogedial\ApiBundle\Entity\Platform $platform)
    {
        $this->platform = $platform;

        return $this;
    }

    /**
     * Get platform
     *
     * @return \Sogedial\ApiBundle\Entity\Platform
     */
    public function getPlatform()
    {
        return $this->platform;
    }

    /**
     * Add cartItem
     *
     * @param \Sogedial\ApiBundle\Entity\CartItem $cartItem
     *
     * @return Cart
     */
    public function addCartItem(\Sogedial\ApiBundle\Entity\CartItem $cartItem)
    {
        $this->cartItems[] = $cartItem;

        return $this;
    }

    /**
     * Remove cartItem
     *
     * @param \Sogedial\ApiBundle\Entity\CartItem $cartItem
     */
    public function removeCartItem(\Sogedial\ApiBundle\Entity\CartItem $cartItem)
    {
        $this->cartItems->removeElement($cartItem);
    }

    /**
     * Get cartItems
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getCartItems()
    {
        return $this->cartItems;
    }

    /**
     * Get getCartItemByItem
     *
     * @return \Sogedial\ApiBundle\Entity\CartItem
     */
    public function getCartItemByItem($item)
    {

        foreach ($this->cartItems as $cartItem) {

            if ($cartItem->getItem() && $cartItem->getItem()->getId() == $item->getId())
                return $cartItem;
        }

        return false;
    }

    /**
     * @Serializer\VirtualProperty()
     */
    public function getTotalPriceVat()
    {

        $total = 0;
        if (!empty($this->cartItems)) {
            foreach ($this->cartItems as $cartItem) {
                $total += $cartItem->getTotalPriceVat();
            }
        }
        return $total;
    }

    /**
     * @Serializer\VirtualProperty()
     */
    public function getTotalPrice()
    {

        $total = 0;
        if (!empty($this->cartItems)) {
            foreach ($this->cartItems as $cartItem)
                $total += $cartItem->getTotalPrice();
        }

        return $total;
    }

    /**
     * @Serializer\VirtualProperty()
     */
    public function getTotalQuantity()
    {

        $total = 0;
        if (!empty($this->cartItems)) {
            foreach ($this->cartItems as $cartItem)
                $total += $cartItem->getQuantity();
        }
        return $total;
    }


    /**
     * @Serializer\VirtualProperty()
     */
    public function getTotalsByType()
    {

        $totals = [];

        if (!empty($this->cartItems)) {
            foreach ($this->cartItems as $cartItem) {
                if ($cartItem->getItem()) {
                    if (!isset($totals[$cartItem->getItem()->getType()])) {
                        $totals[$cartItem->getItem()->getType()] = array(
                            'total_price_vat' => 0,
                            'total_price' => 0,
                            'total_quantity' => 0,
                        );
                    }

                    $totals[$cartItem->getItem()->getType()]['total_price_vat'] += $cartItem->getTotalPriceVat();
                    $totals[$cartItem->getItem()->getType()]['total_price'] += $cartItem->getTotalPrice();
                    $totals[$cartItem->getItem()->getType()]['total_quantity'] += $cartItem->getQuantity();
                }
            }
        }

        return $totals;
    }

    /**
     * @Serializer\VirtualProperty()
     */
    public function getCountItemsByType()
    {

        $totals = [];

        if (!empty($this->cartItems)) {
            foreach ($this->cartItems as $cartItem) {
                if ($cartItem->getItem()) {
                    if (!isset($totals[$cartItem->getItem()->getType()])) {
                        $totals[$cartItem->getItem()->getType()] = 0;
                    }

                    $totals[$cartItem->getItem()->getType()] += 1;
                }
            }
        }

        return $totals;
    }

    /**
     * @Serializer\VirtualProperty()
     */
    public function getReferences()
    {

        $packages = 0;
        $units = 0;

        if (!empty($this->cartItems)) {
            foreach ($this->cartItems as $cartItem) {
                $packages += $cartItem->getQuantity();
                $units += $cartItem->getItem()->getPcb() *  $cartItem->getQuantity();
            }
        }

        return array('packages' => $packages, 'items' => $units);
    }

    /**
     * @Serializer\VirtualProperty()
     */
    public function getDeliveryDatesByType()
    {

        return array(Item::TEMPERATURE_DRY => $this->getDeliveryDateDry(), Item::TEMPERATURE_FRESH => $this->getDeliveryDateFresh(), Item::TEMPERATURE_FROZEN => $this->getDeliveryDateFrozen());
    }



    public function __clone()
    {
        if ($this->id) {
            $cartItems = $this->getCartItems();
            $this->cartItems = new ArrayCollection();
            foreach ($cartItems as $cartItem) {
                if ($cartItem->getItem() !== null && $cartItem->getItem()->getActive() === true) {
                    $this->addCartItem(clone $cartItem);
                }
            }
        }

        return $this;
    }


    /**
     * Set deliveryDateDry.
     *
     * @param \DateTime|null $deliveryDateDry
     *
     * @return Cart
     */
    public function setDeliveryDateDry($deliveryDateDry = null)
    {
        $this->deliveryDateDry = $deliveryDateDry;

        return $this;
    }

    /**
     * Get deliveryDateDry.
     *
     * @return \DateTime|null
     */
    public function getDeliveryDateDry()
    {
        return $this->deliveryDateDry;
    }

    /**
     * Set deliveryDateFresh.
     *
     * @param \DateTime|null $deliveryDateFresh
     *
     * @return Cart
     */
    public function setDeliveryDateFresh($deliveryDateFresh = null)
    {
        $this->deliveryDateFresh = $deliveryDateFresh;

        return $this;
    }

    /**
     * Get deliveryDateFresh.
     *
     * @return \DateTime|null
     */
    public function getDeliveryDateFresh()
    {
        return $this->deliveryDateFresh;
    }

    /**
     * Set deliveryDateFrozen.
     *
     * @param \DateTime|null $deliveryDateFrozen
     *
     * @return Cart
     */
    public function setDeliveryDateFrozen($deliveryDateFrozen = null)
    {
        $this->deliveryDateFrozen = $deliveryDateFrozen;

        return $this;
    }

    /**
     * Get deliveryDateFrozen.
     *
     * @return \DateTime|null
     */
    public function getDeliveryDateFrozen()
    {
        return $this->deliveryDateFrozen;
    }

    /**
     * @Serializer\VirtualProperty()
     */
    public function getIsPreorder()
    {
        /* Maybe should check the product group instead */
        if (
            $this->getStatus() == self::STATUS_CURRENT_PREORDER ||
            $this->getStatus() == self::STATUS_CUSTOM_PREORDER ||
            $this->getStatus() == self::STATUS_MOQ_PREORDER ||
            $this->getStatus() == self::STATUS_STAND_BY_VALIDATION_PREORDER
        ) {
            return true;
        }

        $cartItems = $this->getCartItems();
        if (!empty($cartItems)) {
            foreach ($cartItems as $cartItem) {
                if ($cartItem->getItem()->getIsPreorder()) {

                    return true;
                }
            }
        }

        return false;
    }

    /**
     * Set comment.
     *
     * @param string|null $comment
     *
     * @return Cart
     */
    public function setComment($comment = null)
    {
        $this->comment = $comment;

        return $this;
    }


    /**
     * Set deliveryMode.
     *
     * @param bool|null $deliveryMode
     *
     * @return Cart
     */
    public function setDeliveryMode($deliveryMode = null)
    {
        $this->deliveryMode = $deliveryMode;

        return $this;
    }

    /**
     * Get comment.
     *
     * @return string|null
     */
    public function getComment()
    {
        return $this->comment;
    }

    /**
     * Get deliveryMode.
     *
     * @return bool|null
     */
    public function getDeliveryMode()
    {
        return $this->deliveryMode;
    }


    /**
     * @Serializer\VirtualProperty()
     */
    public function getDeliveryAmountCart()
    {
        return $this->calculateDeliveryAmount($this->getDeliveryMode());
    }


    public function calculateDeliveryAmount($deliveryMode)
    {
        //check if platform autorise la fonctionnalite du choix mode livraison
        $deliveryModeAttribut = $this->getPlatform()->getAttributByKey(Attribut::KEY_DELIVERY_MODE);
        if ($deliveryModeAttribut) {
            if ($deliveryModeAttribut->getValue() == 1) {
                //verifier le parametrage du delivery mode du client sur une platform (parametrer par admin ou bien par defaults)
                /** @var ClientDeliveryMode $clientDeliveryModeEntity */
                $clientDeliveryModeEntity = $this->getUser()->getClient()->getClientDeliveryModeByPlatform($this->getPlatform()->getId());

                //si clientDeliveryModeEntity n existe pas , on attribue au deliveryMode la valeur par default
                $clientDeliveryMode = ($clientDeliveryModeEntity) ? $clientDeliveryModeEntity->getDeliveryMode() : 0;

                if ($clientDeliveryMode == 1) {
                    //admin a attribuer a son client le mode preparation uniquement
                    $deliveryAmount = ($this->getTotalPrice() / 100) * 2;
                    if ($deliveryAmount > 80) {
                        $deliveryAmount = 80;
                    }
                } else {
                    //client a le choix du mode delivery
                    if ($deliveryMode == 1) {
                        $deliveryAmount = ($this->getTotalPrice() / 100) * 2;
                        if ($deliveryAmount > 80) {
                            $deliveryAmount = 80;
                        }
                    } else {
                        //choix du client : delivery mode preparation + livraison
                        $deliveryAmount = ($this->getTotalPrice() / 100) * 4;

                        //ajout du 2% prep
                        $deliveryAmount += ($this->getTotalPrice() / 100) * 2;

                        if ($deliveryAmount > 200) {
                            $deliveryAmount = 200;
                        }
                    }
                }

                return $deliveryAmount;
            }
        }

        return null;
    }


    /**
     * Set deliveryAmount.
     *
     * @param string|null $deliveryAmount
     *
     * @return Cart
     */
    public function setDeliveryAmount($deliveryAmount = null)
    {
        $this->deliveryAmount = $deliveryAmount;

        return $this;
    }

    /**
     * Get deliveryAmount.
     *
     * @return string|null
     */
    public function getDeliveryAmount()
    {
        return $this->deliveryAmount;
    }


    public function getMoqedCartItems()
    {
        $cartItems = $this->getCartItems()->filter(
            function ($elem) {
                /** @var CartItem $elem */
                if ($elem->getItem()->hasMoq()) {

                    return true;
                } else {

                    return false;
                }
            }
        );

        return $cartItems;
    }


    /**
     * Count items
     * @return Int
     * @Serializer\VirtualProperty()
     * @Serializer\Groups({"add", "update","list", "detail", "listitems"})
     */
    public function getCountItemMoqs()
    {
        $cartItems = $this->getCartItems();
        $count = 0;

        if ($cartItems !== null) {
            foreach ($cartItems as $cartItem) {
                $count += ($cartItem->getItem()->hasMoq()) ? 1 : 0;
            }
        }

        return $count;
    }


    /**
     * @Serializer\VirtualProperty()
     *
     * @return int
     */
    public function countNotValidatedCartItems()
    {
        $count  = 0;
        $cartItems = $this->getCartItems();
        if ($cartItems !== null) {
            foreach ($cartItems as $cartItem) {
                /** @var CartItem $cartItem */
                if ($cartItem->getStatus() == CartItem::STATUS_MOQ_PREORDER) {
                    $count++;
                }
            }
        }

        return $count;
    }


    /**
     * @Serializer\VirtualProperty()
     */
    public function getContainersAbilities()
    {
        $platform = $this->getPlatform();
        $attr = $platform->getAttributByKey(Attribut::KEY_HAS_CONTAINERIZATION);
        $valueAttributConteneurization = ($attr)?$attr->getValue():false;

        if ($valueAttributConteneurization) {
            $countItemsByType = $this->getCountItemsByType();
            if (!empty($countItemsByType)) {
                $containers_abilities = [];
                if (array_key_exists(Item::TEMPERATURE_DRY, $countItemsByType)) {
                    $container_20_dry = array(
                        'name' => Container::TYPE_20_DRY,
                        'temperature' => 'DRY',
                        'types' => array(
                            array(
                                'name' => Container::LOADING_PALLET,
                                'volume_m3' => '26',
                                'weight_kg' => '25500'
                            ),
                            array(
                                'name' => Container::LOADING_VRAC,
                                'volume_m3' => '29',
                                'weight_kg' => '25500'
                            )
                        )
                    );
                    array_push($containers_abilities, $container_20_dry);
                    $container_40_dry = array(
                        'name' => Container::TYPE_40_DRY,
                        'temperature' => 'DRY',
                        'types' => array(
                            array(
                                'name' => Container::LOADING_PALLET,
                                'volume_m3' => '59',
                                'weight_kg' => '25500'
                            ),
                            array(
                                'name' => Container::LOADING_VRAC,
                                'volume_m3' => '68',
                                'weight_kg' => '25500'
                            )
                        )
                    );
                    array_push($containers_abilities, $container_40_dry);
                }
                if (array_key_exists(Item::TEMPERATURE_FROZEN, $countItemsByType) || array_key_exists(Item::TEMPERATURE_FRESH, $countItemsByType)) {
                    $container_20_reefer = array(
                        'name' => Container::TYPE_20_REEFER,
                        'temperature' => 'REEFER',
                        'types' => array(
                            array(
                                'name' => Container::LOADING_PALLET,
                                'volume_m3' => '22',
                                'weight_kg' => '25500'
                            ),
                            array(
                                'name' => Container::LOADING_VRAC,
                                'volume_m3' => '25',
                                'weight_kg' => '25500'
                            )
                        )
                    );
                    array_push($containers_abilities, $container_20_reefer);
                    $container_40_dry = array(
                        'name' => Container::TYPE_40_REEFER,
                        'temperature' => 'REEFER',
                        'types' => array(
                            array(
                                'name' => Container::LOADING_PALLET,
                                'volume_m3' => '53',
                                'weight_kg' => '25500'
                            )
                        )
                    );
                    array_push($containers_abilities, $container_40_dry);
                }

                return $containers_abilities;
            }
        }

        return null;
    }


    /**
     * @param $temperature
     * @return array
     */
    public function getContainerByTemperature($temperature)
   {
       $cartItems = $this->getCartItems();
       $containers = [];

       if (!$cartItems->isEmpty()){
           foreach ($cartItems as $cartItem){
               /** @var CartItem $cartItem */
               $containerCartItems = $cartItem->getContainerCartItems();
               if(isset($containerCartItems)){
                   foreach ($containerCartItems as $containerCartItem) {
                       $container = $containerCartItem->getContainer();
                       if(!in_array($container ,$containers) && $container->getTemperature() == $temperature){
                           $containers[] = $container;
                       }
                   }
               }
           }
       }

       return $containers;
   }

    /**
     * @Serializer\VirtualProperty()
     */
    public function getContainers()
    {
        $cartItems = $this->getCartItems();
        $containers = [];
        if(!is_null($cartItems)) {
            foreach ($cartItems as $cartItem) {
                /** @var CartItem $cartItem */
                $containerCartItems = $cartItem->getContainerCartItems();
                if (isset($containerCartItems)) {
                    foreach ($containerCartItems as $containerCartItem) {
                        $container = $containerCartItem->getContainer();
                        if (!in_array($container, $containers)) {
                            $containers[] = $container;
                        }
                    }
                }
            }
        }

        return $containers;
    }


    /**
     * Set orderer.
     *
     * @param \Sogedial\OAuthBundle\Entity\User|null $orderer
     *
     * @return Cart
     */
    public function setOrderer(\Sogedial\OAuthBundle\Entity\User $orderer = null)
    {
        $this->orderer = $orderer;

        return $this;
    }

    /**
     * Get orderer.
     *
     * @return \Sogedial\OAuthBundle\Entity\User|null
     */
    public function getOrderer()
    {
        return $this->orderer;
    }
}
