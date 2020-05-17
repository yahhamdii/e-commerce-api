<?php

namespace Sogedial\ApiBundle\Entity;

use JMS\Serializer\Annotation as Serializer;
use Doctrine\ORM\Mapping as ORM;

/**
 * InvoiceItem
 *
 * @ORM\Table(name="invoice_item")
 * @ORM\Entity(repositoryClass="Sogedial\ApiBundle\Repository\InvoiceItemRepository")
 */
class InvoiceItem extends AbstractEntity {

    /**
     * @ORM\ManyToOne(targetEntity="Sogedial\ApiBundle\Entity\Invoice", inversedBy="invoiceItems")
     * @ORM\JoinColumn(nullable=false)
     * @Serializer\Groups({"add", "update"}) 
     */
    private $invoice;


    /**
     * @ORM\ManyToOne(targetEntity="Sogedial\ApiBundle\Entity\Item")
     * @ORM\JoinColumn(nullable=false)
     * @Serializer\Expose
     * @Serializer\MaxDepth(2)
     * @Serializer\Groups({"add", "update","list", "detail"})
     */
    private $item;

    /**
     * @var string
     *
     * @ORM\Column(name="label", type="string", length=255, nullable=true)
     * @Serializer\Expose
     * @Serializer\Groups({"add", "update","list", "detail", "list_credit"})
     */
    private $label;

    /**
     * @var string
     *
     * @ORM\Column(name="ean13", type="string", length=255, nullable=true)
     * @Serializer\Expose
     * @Serializer\Groups({"add", "update","list", "detail", "list_credit"})
     */
    private $ean13;
    /**
     * @var float
     *
     * @ORM\Column(name="quantity_ordred_package", type="float", nullable=true )
     * @Serializer\Expose
     * @Serializer\Groups({"add", "update","list", "detail", "list_credit"})
     */
    private $quantityOrdredPackage;
    /**
     * @var float
     *
     * @ORM\Column(name="quantity_ordred_uc", type="float", nullable=true )
     * @Serializer\Expose
     * @Serializer\Groups({"add", "update","list", "detail", "list_credit"})
     */
    private $quantityOrdredUC;
    /**
     * @var float
     *
     * @ORM\Column(name="quantity_invoiced_package", type="float", nullable=true )
     * @Serializer\Expose
     * @Serializer\Groups({"add", "update","list", "detail", "list_credit"})
     */
    private $quantityInvoicedPackage;
    /**
     * @var float
     *
     * @ORM\Column(name="quantity_invoiced_uc", type="float", nullable=true )
     * @Serializer\Expose
     * @Serializer\Groups({"add", "update","list", "detail", "list_credit"})
     */
    private $quantityInvoicedUC;
    /**
     * @ORM\Column(name="tva_code", type="float", nullable=true)
     * @Serializer\Expose     
     * @Serializer\Groups({"add", "update","list", "detail", "list_credit"})
     */
    private $tvaCode = 0;
    /**
     * @var float
     *
     * @ORM\Column(name="price", type="float", nullable=true )
     * @Serializer\Expose
     * @Serializer\Groups({"add", "update","list", "detail", "list_credit"})
     */
    private $price;
    /**
     * @var float
     *
     * @ORM\Column(name="final_price", type="float", nullable=true )
     * @Serializer\Expose
     * @Serializer\Groups({"add", "update","list", "detail", "list_credit"})
     */
    private $finalPrice;
    /**
     * @var float
     *
     * @ORM\Column(name="final_price_vat", type="float", nullable=true )
     * @Serializer\Expose
     * @Serializer\Groups({"add", "update","list", "detail", "list_credit"})
     */
    private $finalPriceVat;

    /**
     * @ORM\OneToMany(targetEntity="Sogedial\ApiBundle\Entity\Credit", mappedBy="invoiceItem", cascade={"remove"})
     * @ORM\JoinColumn(nullable= true)
     * @Serializer\Groups({"credit", "list_credit"})
     * @Serializer\Expose
     */
    private $credits;

    /**
     * Set label.
     *
     * @param string|null $label
     *
     * @return InvoiceItem
     */
    public function setLabel($label = null)
    {
        $this->label = $label;

        return $this;
    }

    /**
     * Get label.
     *
     * @return string|null
     */
    public function getLabel()
    {
        return $this->label;
    }

    /**
     * Set ean13.
     *
     * @param string|null $ean13
     *
     * @return InvoiceItem
     */
    public function setEan13($ean13 = null)
    {
        $this->ean13 = $ean13;

        return $this;
    }

    /**
     * Get ean13.
     *
     * @return string|null
     */
    public function getEan13()
    {
        return $this->ean13;
    }

    /**
     * Set quantityOrdredPackage.
     *
     * @param float|null $quantityOrdredPackage
     *
     * @return InvoiceItem
     */
    public function setQuantityOrdredPackage($quantityOrdredPackage = null)
    {
        $this->quantityOrdredPackage = $quantityOrdredPackage;

        return $this;
    }

    /**
     * Get quantityOrdredPackage.
     *
     * @return float|null
     */
    public function getQuantityOrdredPackage()
    {
        return $this->quantityOrdredPackage;
    }

    /**
     * Set quantityOrdredUC.
     *
     * @param float|null $quantityOrdredUC
     *
     * @return InvoiceItem
     */
    public function setQuantityOrdredUC($quantityOrdredUC = null)
    {
        $this->quantityOrdredUC = $quantityOrdredUC;

        return $this;
    }

    /**
     * Get quantityOrdredUC.
     *
     * @return float|null
     */
    public function getQuantityOrdredUC()
    {
        return $this->quantityOrdredUC;
    }

    /**
     * Set quantityInvoicedPackage.
     *
     * @param float|null $quantityInvoicedPackage
     *
     * @return InvoiceItem
     */
    public function setQuantityInvoicedPackage($quantityInvoicedPackage = null)
    {
        $this->quantityInvoicedPackage = $quantityInvoicedPackage;

        return $this;
    }

    /**
     * Get quantityInvoicedPackage.
     *
     * @return float|null
     */
    public function getQuantityInvoicedPackage()
    {
        return $this->quantityInvoicedPackage;
    }

    /**
     * Set quantityInvoicedUC.
     *
     * @param float|null $quantityInvoicedUC
     *
     * @return InvoiceItem
     */
    public function setQuantityInvoicedUC($quantityInvoicedUC = null)
    {
        $this->quantityInvoicedUC = $quantityInvoicedUC;

        return $this;
    }

    /**
     * Get quantityInvoicedUC.
     *
     * @return float|null
     */
    public function getQuantityInvoicedUC()
    {
        return $this->quantityInvoicedUC;
    }

    /**
     * Set tvaCode.
     *
     * @param int|null $tvaCode
     *
     * @return InvoiceItem
     */
    public function setTvaCode($tvaCode = null)
    {
        $this->tvaCode = $tvaCode;

        return $this;
    }

    /**
     * Get tvaCode.
     *
     * @return int|null
     */
    public function getTvaCode()
    {
        return $this->tvaCode;
    }

    /**
     * Set price.
     *
     * @param float|null $price
     *
     * @return InvoiceItem
     */
    public function setPrice($price = null)
    {
        $this->price = $price;

        return $this;
    }

    /**
     * Get price.
     *
     * @return float|null
     */
    public function getPrice()
    {
        return $this->price;
    }

    /**
     * Set finalPrice.
     *
     * @param float|null $finalPrice
     *
     * @return InvoiceItem
     */
    public function setFinalPrice($finalPrice = null)
    {
        $this->finalPrice = $finalPrice;

        return $this;
    }

    /**
     * Get finalPrice.
     *
     * @return float|null
     */
    public function getFinalPrice()
    {
        return $this->finalPrice;
    }

    /**
     * Set finalPriceVat.
     *
     * @param float|null $finalPriceVat
     *
     * @return InvoiceItem
     */
    public function setFinalPriceVat($finalPriceVat = null)
    {
        $this->finalPriceVat = $finalPriceVat;

        return $this;
    }

    /**
     * Get finalPriceVat.
     *
     * @return float|null
     */
    public function getFinalPriceVat()
    {
        return $this->finalPriceVat;
    }

    /**
     * Set dateCreate.
     *
     * @param \DateTime|null $dateCreate
     *
     * @return InvoiceItem
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
     * @return InvoiceItem
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
     * Set invoice.
     *
     * @param \Sogedial\ApiBundle\Entity\Invoice $invoice
     *
     * @return InvoiceItem
     */
    public function setInvoice(\Sogedial\ApiBundle\Entity\Invoice $invoice)
    {
        $this->invoice = $invoice;

        return $this;
    }

    /**
     * Get invoice.
     *
     * @return \Sogedial\ApiBundle\Entity\Invoice
     */
    public function getInvoice()
    {
        return $this->invoice;
    }

    /**
     * Set item.
     *
     * @param \Sogedial\ApiBundle\Entity\Item $item
     *
     * @return InvoiceItem
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
     * Constructor
     */
    public function __construct()
    {
        $this->credits = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Add credit.
     *
     * @param \Sogedial\ApiBundle\Entity\Credit $credit
     *
     * @return InvoiceItem
     */
    public function addCredit(\Sogedial\ApiBundle\Entity\Credit $credit)
    {
        $this->credits[] = $credit;

        return $this;
    }

    /**
     * Remove credit.
     *
     * @param \Sogedial\ApiBundle\Entity\Credit $credit
     *
     * @return boolean TRUE if this collection contained the specified element, FALSE otherwise.
     */
    public function removeCredit(\Sogedial\ApiBundle\Entity\Credit $credit)
    {
        return $this->credits->removeElement($credit);
    }

    /**
     * Get credits.
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getCredits()
    {
        return $this->credits;
    }


    
    /**
     * Get item type
     *
     * @return String
     */

    public function getItemType(){
        if($this->getItem()){
            return $this->getItem()->getType();
        }
        return Item::TEMPERATURE_UNDEFINED;
    }

    /**
     * @Serializer\VirtualProperty()
     * @Serializer\Expose()
     * @Serializer\Groups({"add", "update", "list", "detail", "list_credit"})
     */
    public function getInvoiceNumber()
    {   
        return $this->getInvoice()->getNumber();
    }

    /**
     * @Serializer\VirtualProperty()
     * @Serializer\Expose()
     * @Serializer\Groups({"add", "update", "list", "detail", "list_credit"})
     */
    public function getPcb()
    {
        if($this->getQuantityInvoicedPackage() == 0 || is_null($this->getQuantityInvoicedPackage())){

            return 0;
        }

        return round($this->getQuantityInvoicedUC()/$this->getQuantityInvoicedPackage(), 2);
    }

    /**
     * @Serializer\VirtualProperty()
     * @Serializer\Expose()
     * @Serializer\Groups({"add", "update", "list", "detail", "list_credit"})
     */
    public function getReference()
    {   
        return ($this->getItem())?$this->getItem()->getReference():'';
    }

    /**
     * @Serializer\VirtualProperty()
     * @Serializer\Expose()
     * @Serializer\Groups({"add", "update", "list", "detail", "list_credit"})
     */
    public function getManufacturerName()
    {   
        return ($this->getItem() && $this->getItem()->getManufacturer())?$this->getItem()->getManufacturer()->getName():'';
    }

}
