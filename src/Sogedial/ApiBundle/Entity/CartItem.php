<?php

namespace Sogedial\ApiBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as Serializer;

/**
 * CartItem
 *
 * @ORM\Table(name="cart_item")
 * @ORM\Entity(repositoryClass="Sogedial\ApiBundle\Repository\CartItemRepository")
 * @Serializer\ExclusionPolicy("all")
 */
class CartItem extends AbstractEntity
{
    const STATUS_MOQ_PREORDER  = 'MOQ_PREORDER';
    const STATUS_STAND_BY_VALIDATION_PREORDER = 'STAND_BY_VALIDATION_PREORDER';

    /* ! TO DO Expose Cart only for the add action, do not needs it for the get aned get all actions */
    /**
     * @ORM\ManyToOne(targetEntity="Cart",inversedBy="cartItems", cascade={"persist"})
     * @ORM\JoinColumn(nullable=true)
     * @Serializer\Groups({"moq"})
     * @Serializer\Expose
     * @Serializer\MaxDepth(3)
     */
    private $cart;

    /**
     * @ORM\ManyToOne(targetEntity="Item")
     * @ORM\JoinColumn(nullable=true)
     * @Serializer\Expose
     * @Serializer\MaxDepth(3)     
     */
    private $item;

    /**
     * @ORM\Column(name="quantity", type="integer", nullable=true)
     * @Serializer\Groups({"moq"})
     * @Serializer\Expose     
     */
    private $quantity = 0;

    /**
    * @ORM\Column(name="status", type="string", length=255, nullable=true)
    * @Serializer\Expose
    */
    private $status;

    /**
     * @ORM\OneToMany(targetEntity="Sogedial\ApiBundle\Entity\ContainerCartItem", mappedBy="cartItem", cascade={"remove"})
     * @ORM\JoinColumn(nullable= true)
     */
    private $containerCartItems;

    /**
     * Set quantity.
     *
     * @param int $quantity
     *
     * @return CartItem
     */
    public function setQuantity($quantity)
    {
        $this->quantity = $quantity;

        return $this;
    }

    /**
     * Get quantity.
     *
     * @return int
     */
    public function getQuantity()
    {
        return $this->quantity;
    }

    /**
     * Set cart.
     *
     * @param \Sogedial\ApiBundle\Entity\Cart $cart
     *
     * @return CartItem
     */
    public function setCart(\Sogedial\ApiBundle\Entity\Cart $cart)
    {
        $this->cart = $cart;

        return $this;
    }

    /**
     * Get cart.
     *
     * @return \Sogedial\ApiBundle\Entity\Cart
     */
    public function getCart()
    {
        return $this->cart;
    }

    /**
     * Set item.
     *
     * @param \Sogedial\ApiBundle\Entity\Item $item
     *
     * @return CartItem
     */
    public function setItem(\Sogedial\ApiBundle\Entity\Item $item)
    {
        $this->item = $item;

        return $this;
    }

    /**
     * Get item.
     *
     * @return \Sogedial\ApiBundle\Entity\Item
     */
    public function getItem()
    {
        return $this->item;
    }

    /**
     * Set dateCreate.
     *
     * @param \DateTime|null $dateCreate
     *
     * @return CartItem
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
     * @return CartItem
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
     * @Serializer\VirtualProperty()
     */
    public function getTotalPriceVat()
    {

        $total = 0;

        if ($this->item) {

            $total = $this->item->getApplicablePriceVat( $this->getQuantity() * $this->item->getPcb() );

        }

        return $total;
    }

    /**
     * @Serializer\VirtualProperty()
     */
    public function getTotalPrice()
    {
        $total = 0;

        if ($this->item) {

            $total = $this->item->getApplicablePrice( $this->getQuantity() * $this->item->getPcb() );

        }

        return $total;
    }

    /**
     * @Serializer\VirtualProperty()
     */
    public function getItemId()
    {
        return ($this->getItem())?$this->getItem()->getId():null;

    }

    /**
     * @Serializer\VirtualProperty()
     */
    public function getItemType()
    {
        return ($this->getItem())?$this->getItem()->getType():null;

    }

     /**
     * @Serializer\VirtualProperty()
     */
    public function getItemPcb()
    {
        return ($this->getItem())?$this->getItem()->getPcb():null;

    }
    
    /**
     * @Serializer\VirtualProperty()
     */
    public function getCartId()
    {
        return $this->getCart()->getId();

    }


    /**
     * @Serializer\VirtualProperty()
     */
    public function getDegressive()
    {
        $item = $this->getItem();
        if($item){
            $qte = $this->getQuantity();
            $degressives = $item->getDegressives();
    
            $degressive = null;
    
            foreach ($degressives as $value) {
    
                if ($qte >= $value->getStep()) {
                    if (isset($degressive) && $value->getStep() < $degressive->getStep()) {
                        continue;
                    }
                    $degressive = $value;
    
                }
    
            }
    
            return $degressive;
        }

        return null;        

    }


    /**
     * quantity requested either in package or unit based on entity attribut
     *
     * @return array
     */
    public function getQuantityRequested()
    {
        $attribut = $this->getCart()->getPlatform()->getAttributByKey(Attribut::KEY_ORDERED_BY);
        //par defaults = 1 (ordered by colis) , 0 par unit
        $orderedBy = 1;
        if($attribut){
            $orderedBy = $attribut->getValue();
        }

        //commande par colis
        if($orderedBy == 1) {
            $packages = $this->getQuantity();
            $units = $this->getQuantity() * $this->getItem()->getPcb();
        }else{
            //commande par unit
            $units = $this->getQuantity();
            $packages = ceil($this->getQuantity() / $this->getItem()->getPcb());
        }

        return array('packages' => $packages, 'items' => $units);
    }


    /**
     * @Serializer\VirtualProperty()
     *
     * @return array
     */
    public function getRemainingQuantity()
    {
        $stock = $this->getItem()->getStock();
        if($stock){
            $quantityRequested = $this->getQuantityRequested();

            $remainingPackages = (int)$stock->getValuePacking() - (int)$quantityRequested['packages'];
            $remainingUnits = (int)$stock->getValueCu() - (int)$quantityRequested['items'];

            return array(
                'packages' => $remainingPackages,
                'items' => $remainingUnits
            );
        }

        return null;
    }


    public function allowedToOrder()
    {
        if (!$this->getItem()->getIsPreorder() && $this->getItem()->getStock()) {
            $platform = $this->getCart()->getPlatform();
            $stockCartAttribut = $platform->getAttributByKey(Attribut::KEY_STOCK_CART);

            // by defaults $valueStockCart = 0 cad que si le stock est insuffisant il est impossible de comamander
            $valueStockCart = 0;
            if ($stockCartAttribut) {
                $valueStockCart = $stockCartAttribut->getValue();
            }

            // si stock cart Value == 0, on doit verifier si la quantite en stock est suffisant ou nn
            if ($valueStockCart == 0) {
                // savoir si on commande par colis ou unity
                $orderByAttribut = $platform->getAttributByKey(Attribut::KEY_ORDERED_BY);
                // par defaults on commande par colis $orderByValue = 1
                $orderByValue = 1;
                if ($orderByAttribut) {
                    $orderByValue = $orderByAttribut->getValue();
                }

                $remainingStock = $this->getRemainingQuantity();
                //recuperer la quantite restant adequat (soit colis soit unit)
                if ($orderByValue == 1) {
                    $stock = $remainingStock['packages'];
                } else {
                    $stock = $remainingStock['items'];
                }

                if ($stock < 0) {

                    return false;
                }
            }
        }

        return true;
    }


    /**
     * Set status.
     *
     * @param string|null $status
     *
     * @return CartItem
     */
    public function setStatus($status = null)
    {
        $this->status = $status;

        return $this;
    }

    /**
     * Get status.
     *
     * @return string|null
     */
    public function getStatus()
    {
        return $this->status;
    }


    /**
     * @Serializer\VirtualProperty()
     * @Serializer\Groups({"add", "update","list", "detail", "listitems"})
     *
     * @return int|null
     */
    public function getItemMoq()
    {

        return ($this->getItem()->hasMoq()) ? $this->getItem()->getMoq() : null;
    }

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->containerCartItems = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Add containerCartItem.
     *
     * @param \Sogedial\ApiBundle\Entity\ContainerCartItem $containerCartItem
     *
     * @return CartItem
     */
    public function addContainerCartItem(\Sogedial\ApiBundle\Entity\ContainerCartItem $containerCartItem)
    {
        $this->containerCartItems[] = $containerCartItem;

        return $this;
    }

    /**
     * Remove containerCartItem.
     *
     * @param \Sogedial\ApiBundle\Entity\ContainerCartItem $containerCartItem
     *
     * @return boolean TRUE if this collection contained the specified element, FALSE otherwise.
     */
    public function removeContainerCartItem(\Sogedial\ApiBundle\Entity\ContainerCartItem $containerCartItem)
    {
        return $this->containerCartItems->removeElement($containerCartItem);
    }

    /**
     * Get containerCartItems.
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getContainerCartItems()
    {
        return $this->containerCartItems;
    }

}
