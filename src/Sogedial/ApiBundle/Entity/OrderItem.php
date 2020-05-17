<?php

namespace Sogedial\ApiBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as Serializer;

/**
 * OrderItem
 *
 * @ORM\Table(name="order_item")
 * @ORM\Entity(repositoryClass="Sogedial\ApiBundle\Repository\OrderItemRepository")
 * @Serializer\ExclusionPolicy("all")
 */
class OrderItem extends AbstractEntity
{

    /**
     * @ORM\ManyToOne(targetEntity="Sogedial\ApiBundle\Entity\Order", inversedBy="orderItems", cascade={"persist"})
     * @ORM\JoinColumn(nullable=false)
     */
    private $order;


    /**
     * @ORM\ManyToOne(targetEntity="Sogedial\ApiBundle\Entity\Item", inversedBy="orderItems")
     * @ORM\JoinColumn(nullable=false)     
     */
    private $item;

    /**
     * @ORM\Column(name="quantity", type="integer", nullable=true)
     * @Serializer\Expose
     * @Serializer\Groups({"credit", "list_credit"})
     */
    private $quantity;

    /**
     * @ORM\Column(name="final_price", type="decimal", precision=20, scale=6)
     * @Serializer\Expose
     * @Serializer\Groups({"credit", "list_credit"})
     */
    private $finalPrice;

    /**
     * @ORM\Column(name="final_price_Vat", type="decimal", precision=20, scale=6)
     * @Serializer\Expose
     * @Serializer\Groups({"credit", "list_credit"})
     */
    private $finalPriceVat;

    /**
     * @ORM\Column(name="item_price", type="string", length=45, nullable=true)
     * @Serializer\Expose
     * @Serializer\Groups({"credit", "list_credit"})
     */
    private $itemPrice;


    /**
     * @ORM\Column(name="item_price_Vat", type="string", length=45, nullable=true)
     * @Serializer\Expose
     * @Serializer\Groups({"credit", "list_credit"})
     */

    private $itemPriceVat;


    /**
     * @ORM\Column(name="item_Vat", type="string", length=45, nullable=true)
     * @Serializer\Expose
     */
    private $itemVat;

    /**
     * @ORM\Column(name="item_reference", type="string", length=45, nullable=true)
     * @Serializer\Expose
     * @Serializer\Groups({"credit", "list_credit"})
     */
    private $itemReference;

    /**
     * @ORM\Column(name="item_type", type="string", length=45, nullable=true)
     * @Serializer\Expose
     */
    private $itemType;

    /**
     * @ORM\Column(name="item_name", type="string", length=45, nullable=true)
     * @Serializer\Expose
     * @Serializer\Groups({"credit", "list_credit"})
     */
    private $itemName;

    /**
     * @ORM\Column(name="item_ean13", type="string", length=45, nullable=true)
     * @Serializer\Expose
     * @Serializer\Groups({"credit", "list_credit"})
     */
    private $itemEan13;

    /**
     * @ORM\Column(name="item_weight", type="string", length=45, nullable=true)
     * @Serializer\Expose
     */
    private $itemWeight;

    /**
     * @ORM\Column(name="item_pcb", type="integer", nullable=true)
     * @Serializer\Expose
     * @Serializer\Groups({"credit", "list_credit"})
     */
    private $itemPcb;

    /**
     * @ORM\Column(name="item_unity", type="string", length=45, nullable=true)
     * @Serializer\Expose
     */
    private $itemUnity;

    /**
     * @ORM\Column(name="item_upc", type="string", length=12, nullable=true)
     * @Serializer\Expose
     */
    private $itemUpc;

    /**
     * @ORM\Column(name="item_active", type="boolean", nullable=true)
     * @Serializer\Expose
     */
    private $itemActive;

    /**
     * @ORM\Column(name="item_date_begin", type="datetime", nullable=true)
     * @Serializer\Expose
     */
    private $itemDateBegin;

    /**
     * @ORM\Column(name="item_date_end", type="datetime", nullable=true)
     * @Serializer\Expose
     */
    private $itemDateEnd;

    /**
     * @ORM\Column(name="item_is_preorder", type="boolean", nullable=true)
     * @Serializer\Expose
     */
    private $itemIsPreorder;

    
    /**
     * @ORM\Column(name="item_frequency", type="string", length=255, nullable=true)
     * @Serializer\Expose
     */
    private $itemFrequency;


    /**
     * @ORM\Column(name="item_code_nature", type="string", length=255, nullable=true)
     * @Serializer\Expose
     */
    private $itemCodeNature;

    /**
     * @ORM\Column(name="item_code", type="string", length=255, nullable=true)
     * @Serializer\Expose
     * @Serializer\Groups({"list_credit"})
     */
    private $itemCode;


      /**
     * @ORM\Column(name="item_manufacturer_name", type="string", length=255, nullable=true)
     * @Serializer\Expose
     * @Serializer\Groups({"credit", "list_credit"})
     */
    private $itemManufacturerName;

    /**
     * @ORM\Column(name="item_category", type="string", length=255, nullable=true)
     * @Serializer\Expose
     */
    private $itemCategoryName;

    /**
     * @ORM\Column(name = "item_moq",nullable= true, type="integer")
     * @Serializer\Expose
     */
    private $itemMoq;

    /**
     * @ORM\Column(name="degressive_price", type="string", length=45, nullable=true)
     * @Serializer\Expose
     */
    private $degressivePrice;

    /**
     * @ORM\Column(name="degressive_step", type="string", length=45, nullable=true)
     * @Serializer\Expose
     */
    private $degressiveStep;

    /**
     * @var bool
     *
     * @ORM\Column(name="item_is_new ", type="boolean", nullable= true)
     * @Serializer\Expose
     */
    private $itemIsNew;

    /**
     * @var bool
     *
     * @ORM\Column(name="item_has_promotion", type="boolean", nullable= true)
     * @Serializer\Expose
     */
    private $itemHasPromotion;

    /**
     * Set quantity.
     *
     * @param int|null $quantity
     *
     * @return OrderItem
     */
    public function setQuantity($quantity = null)
    {
        $this->quantity = $quantity;

        return $this;
    }

    /**
     * Get quantity.
     *
     * @return int|null
     */
    public function getQuantity()
    {
        return $this->quantity;
    }

    /**
     * Set finalPrice.
     *
     * @param string $finalPrice
     *
     * @return OrderItem
     */
    public function setFinalPrice($finalPrice)
    {
        $this->finalPrice = $finalPrice;

        return $this;
    }

    /**
     * Get finalPrice.
     *
     * @return string
     */
    public function getFinalPrice()
    {
        return $this->finalPrice;
    }

    /**
     * Set itemPrice.
     *
     * @param string|null $itemPrice
     *
     * @return OrderItem
     */
    public function setItemPrice($itemPrice = null)
    {
        $this->itemPrice = $itemPrice;

        return $this;
    }

    /**
     * Get itemPrice.
     *
     * @return string|null
     */
    public function getItemPrice()
    {
        return $this->itemPrice;
    }

    /**
     * Set itemPriceVat.
     *
     * @param string|null $itemPriceVat
     *
     * @return OrderItem
     */
    public function setItemPriceVat($itemPriceVat = null)
    {
        $this->itemPriceVat = $itemPriceVat;

        return $this;
    }

    /**
     * Get itemPriceVat.
     *
     * @return string|null
     */
    public function getItemPriceVat()
    {
        return $this->itemPriceVat;
    }

    /**
     * Set itemVat.
     *
     * @param string|null $itemVat
     *
     * @return OrderItem
     */
    public function setItemVat($itemVat = null)
    {
        $this->itemVat = $itemVat;

        return $this;
    }

    /**
     * Get itemVat.
     *
     * @return string|null
     */
    public function getItemVat()
    {
        return $this->itemVat;
    }

    /**
     * Set itemReference.
     *
     * @param string|null $itemReference
     *
     * @return OrderItem
     */
    public function setItemReference($itemReference = null)
    {
        $this->itemReference = $itemReference;

        return $this;
    }

    /**
     * Get itemReference.
     *
     * @return string|null
     */
    public function getItemReference()
    {
        return $this->itemReference;
    }

    /**
     * Set itemType.
     *
     * @param string|null $itemType
     *
     * @return OrderItem
     */
    public function setItemType($itemType = null)
    {
        $this->itemType = $itemType;

        return $this;
    }

    /**
     * Get itemType.
     *
     * @return string|null
     */
    public function getItemType()
    {
        return $this->itemType;
    }

    /**
     * Set itemName.
     *
     * @param string|null $itemName
     *
     * @return OrderItem
     */
    public function setItemName($itemName = null)
    {
        $this->itemName = $itemName;

        return $this;
    }

    /**
     * Get itemName.
     *
     * @return string|null
     */
    public function getItemName()
    {
        return $this->itemName;
    }

    /**
     * Set itemEan13.
     *
     * @param string|null $itemEan13
     *
     * @return OrderItem
     */
    public function setItemEan13($itemEan13 = null)
    {
        $this->itemEan13 = $itemEan13;

        return $this;
    }

    /**
     * Get itemEan13.
     *
     * @return string|null
     */
    public function getItemEan13()
    {
        return $this->itemEan13;
    }

    /**
     * Set itemWeight.
     *
     * @param string|null $itemWeight
     *
     * @return OrderItem
     */
    public function setItemWeight($itemWeight = null)
    {
        $this->itemWeight = $itemWeight;

        return $this;
    }

    /**
     * Get itemWeight.
     *
     * @return string|null
     */
    public function getItemWeight()
    {
        return $this->itemWeight;
    }

    /**
     * Set itemPcb.
     *
     * @param int|null $itemPcb
     *
     * @return OrderItem
     */
    public function setItemPcb($itemPcb = null)
    {
        $this->itemPcb = $itemPcb;

        return $this;
    }

    /**
     * Get itemPcb.
     *
     * @return int|null
     */
    public function getItemPcb()
    {
        return $this->itemPcb;
    }

    /**
     * Set itemUnity.
     *
     * @param string|null $itemUnity
     *
     * @return OrderItem
     */
    public function setItemUnity($itemUnity = null)
    {
        $this->itemUnity = $itemUnity;

        return $this;
    }

    /**
     * Get itemUnity.
     *
     * @return string|null
     */
    public function getItemUnity()
    {
        return $this->itemUnity;
    }

    /**
     * Set itemUpc.
     *
     * @param string|null $itemUpc
     *
     * @return OrderItem
     */
    public function setItemUpc($itemUpc = null)
    {
        $this->itemUpc = $itemUpc;

        return $this;
    }

    /**
     * Get itemUpc.
     *
     * @return string|null
     */
    public function getItemUpc()
    {
        return $this->itemUpc;
    }

    /**
     * Set itemDateBegin.
     *
     * @param \DateTime|null $itemDateBegin
     *
     * @return OrderItem
     */
    public function setItemDateBegin($itemDateBegin = null)
    {
        $this->itemDateBegin = $itemDateBegin;

        return $this;
    }

    /**
     * Get itemDateBegin.
     *
     * @return \DateTime|null
     */
    public function getItemDateBegin()
    {
        return $this->itemDateBegin;
    }

    /**
     * Set itemDateEnd.
     *
     * @param \DateTime|null $itemDateEnd
     *
     * @return OrderItem
     */
    public function setItemDateEnd($itemDateEnd = null)
    {
        $this->itemDateEnd = $itemDateEnd;

        return $this;
    }

    /**
     * Get itemDateEnd.
     *
     * @return \DateTime|null
     */
    public function getItemDateEnd()
    {
        return $this->itemDateEnd;
    }

    /**
     * Set degressivePrice.
     *
     * @param string|null $degressivePrice
     *
     * @return OrderItem
     */
    public function setDegressivePrice($degressivePrice = null)
    {
        $this->degressivePrice = $degressivePrice;

        return $this;
    }

    /**
     * Get degressivePrice.
     *
     * @return string|null
     */
    public function getDegressivePrice()
    {
        return $this->degressivePrice;
    }

    /**
     * Set degressiveStep.
     *
     * @param string|null $degressiveStep
     *
     * @return OrderItem
     */
    public function setDegressiveStep($degressiveStep = null)
    {
        $this->degressiveStep = $degressiveStep;

        return $this;
    }

    /**
     * Get degressiveStep.
     *
     * @return string|null
     */
    public function getDegressiveStep()
    {
        return $this->degressiveStep;
    }



    /**
     * Set order.
     *
     * @param \Sogedial\ApiBundle\Entity\Order $order
     *
     * @return OrderItem
     */
    public function setOrder(\Sogedial\ApiBundle\Entity\Order $order)
    {
        $this->order = $order;

        return $this;
    }

    /**
     * Get order.
     *
     * @return \Sogedial\ApiBundle\Entity\Order
     */
    public function getOrder()
    {
        return $this->order;
    }

    /**
     * Set item.
     *
     * @param \Sogedial\ApiBundle\Entity\Item $item
     *
     * @return OrderItem
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
     * @return OrderItem
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
     * @return OrderItem
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
     * Set itemActive.
     *
     * @param bool|null $itemActive
     *
     * @return OrderItem
     */
    public function setItemActive($itemActive = null)
    {
        $this->itemActive = $itemActive;

        return $this;
    }

    /**
     * Get itemActive.
     *
     * @return bool|null
     */
    public function getItemActive()
    {
        return $this->itemActive;
    }

    /**
     * Set finalPriceVat.
     *
     * @param string $finalPriceVat
     *
     * @return OrderItem
     */
    public function setFinalPriceVat($finalPriceVat)
    {
        $this->finalPriceVat = $finalPriceVat;

        return $this;
    }

    /**
     * Get finalPriceVat.
     *
     * @return string
     */
    public function getFinalPriceVat()
    {
        return $this->finalPriceVat;
    }

     /**
     * @Serializer\VirtualProperty()
     */
    public function getItemImages() {

        $images = array(
            'small' => '//photos.groupesafo.com/wsse/cb8f190153190ebe8598ac49e86ebf37/75/75/' . $this->getItemEan13(),
            'large' => '//photos.groupesafo.com/wsse/cb8f190153190ebe8598ac49e86ebf37/320/220/' . $this->getItemEan13(),
        );

        return $images;
    }

    /**
     * Set itemManufacturerName.
     *
     * @param string|null $itemManufacturerName
     *
     * @return OrderItem
     */
    public function setItemManufacturerName($itemManufacturerName = null)
    {
        $this->itemManufacturerName = $itemManufacturerName;
        
        return $this;
    }

    /**
     * Get itemManufacturerName.
     *
     * @return string|null
     */
    public function getItemManufacturerName()
    {
        if(!$this->itemManufacturerName){            
            if($this->item){
                $manufacturer = $this->item->getManufacturer();
                if( $manufacturer ){
                    return $manufacturer->getName();
                }
            }
        }

        return $this->itemManufacturerName;
    }

    /**
     * Set itemCategoryName.
     *
     * @param string|null $itemCategoryName
     *
     * @return OrderItem
     */
    public function setItemCategoryName($itemCategoryName = null)
    {        
        $this->itemCategoryName = $itemCategoryName;

        return $this;
    }

    /**
     * Get itemCategoryName.
     *
     * @return string|null
     */
    public function getItemCategoryName()
    {
        if (!$this->itemCategoryName) {
            if ($this->item) {
                $category = $this->item->getCategory();
                return ($category) ? $category->getName() : null;
            }
        }

        return $this->itemCategoryName;
    }

    /**
     * Set itemIsPreorder.
     *
     * @param bool|null $itemIsPreorder
     *
     * @return OrderItem
     */
    public function setItemIsPreorder($itemIsPreorder = null)
    {
        $this->itemIsPreorder = $itemIsPreorder;

        return $this;
    }

    /**
     * Get itemIsPreorder.
     *
     * @return bool|null
     */
    public function getItemIsPreorder()
    {
        return $this->itemIsPreorder;
    }

    /**
     * Set itemFrequency.
     *
     * @param string|null $itemFrequency
     *
     * @return OrderItem
     */
    public function setItemFrequency($itemFrequency = null)
    {
        $this->itemFrequency = $itemFrequency;

        return $this;
    }

    /**
     * Get itemFrequency.
     *
     * @return string|null
     */
    public function getItemFrequency()
    {
        return $this->itemFrequency;
    }

    /**
     * Set itemCodeNature.
     *
     * @param string|null $itemCodeNature
     *
     * @return OrderItem
     */
    public function setItemCodeNature($itemCodeNature = null)
    {
        $this->itemCodeNature = $itemCodeNature;

        return $this;
    }

    /**
     * Get itemCodeNature.
     *
     * @return string|null
     */
    public function getItemCodeNature()
    {
        return $this->itemCodeNature;
    }

    /**
     * @Serializer\VirtualProperty()
     */
    public function getItemId()
    {
        return ($this->getItem()) ? $this->getItem()->getId() : null;
    }


    /**
     * quantity requested either in package or unit based on entity attribut
     *
     * @return array
     */
    public function getQuantityOrdered()
    {
        $attribut = $this->getOrder()->getPlatform()->getAttributByKey(Attribut::KEY_ORDERED_BY);
        //par defaults = 1 (ordered by colis) , 0 par unit
        $orderedBy = 1;
        if($attribut){
            $orderedBy = $attribut->getValue();
        }

        //commande par colis
        if($orderedBy == 1) {
            $packages = $this->getQuantity();
            $units = $this->getQuantity() * $this->getItemPcb();
        }else{
            //commande par unit
            $units = $this->getQuantity();
            $packages = ceil($this->getQuantity() / $this->getItemPcb());
        }

        return array('packages' => $packages, 'items' => $units);
    }

    /**
     * @Serializer\VirtualProperty()
     */
    public function getItemStock()
    {
        $stock = $this->getItem()->getStock();

        return array(
            'packages' => $stock->getValuePacking(),
            'items' => $stock->getValueCu()
        );
    }


    /**
     * Set itemMoq.
     *
     * @param int|null $itemMoq
     *
     * @return OrderItem
     */
    public function setItemMoq($itemMoq = null)
    {
        $this->itemMoq = $itemMoq;

        return $this;
    }

    /**
     * Get itemMoq.
     *
     * @return int|null
     */
    public function getItemMoq()
    {
        return $this->itemMoq;
    }
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->credits = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Set itemCode.
     *
     * @param string|null $itemCode
     *
     * @return OrderItem
     */
    public function setItemCode($itemCode = null)
    {
        $this->itemCode = $itemCode;

        return $this;
    }

    /**
     * Get itemCode.
     *
     * @return string|null
     */
    public function getItemCode()
    {
        return $this->itemCode;
    }

    /**
     * Set itemIsNew.
     *
     * @param bool|null $itemIsNew
     *
     * @return OrderItem
     */
    public function setItemIsNew($itemIsNew = null)
    {
        $this->itemIsNew = $itemIsNew;

        return $this;
    }

    /**
     * Get itemIsNew.
     *
     * @return bool|null
     */
    public function getItemIsNew()
    {
        return $this->itemIsNew;
    }

    /**
     * Set itemHasPromotion.
     *
     * @param bool|null $itemHasPromotion
     *
     * @return OrderItem
     */
    public function setItemHasPromotion($itemHasPromotion = null)
    {
        $this->itemHasPromotion = $itemHasPromotion;

        return $this;
    }

    /**
     * Get itemHasPromotion.
     *
     * @return bool|null
     */
    public function getItemHasPromotion()
    {
        return $this->itemHasPromotion;
    }
}
