<?php

namespace Sogedial\ApiBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as Serializer;

/**
 * Invoice
 *
 * @ORM\Table(name="invoice")
 * @ORM\Entity(repositoryClass="Sogedial\ApiBundle\Repository\InvoiceRepository")
 * @Serializer\ExclusionPolicy("all")
 */
class Invoice extends AbstractEntity {

    /**
     * @var string
     *
     * @ORM\Column(name="number", type="string", length=255, nullable=true)
     * @Serializer\Groups({"add", "update","list", "detail"})
     * @Serializer\Expose
     * @Serializer\Groups({"list"})
     */
    private $number;
    /**
     * @ORM\ManyToOne(targetEntity="Platform")
     * @Serializer\Groups({"add", "update"})
     * @ORM\JoinColumn(name="platform_id", referencedColumnName="id")
     */
    private $platform;

    /**
     * @ORM\ManyToOne(targetEntity="Sogedial\ApiBundle\Entity\Client", inversedBy="invoicesClient")
     * @ORM\JoinColumn(nullable=false)
     * @Serializer\MaxDepth(1)
     * @Serializer\Expose()
     * @Serializer\Groups({"add", "update"})
     */
    private $client;

    /**
     * @ORM\ManyToOne(targetEntity="Sogedial\ApiBundle\Entity\Order", inversedBy="invoicesOrder")
     * @ORM\JoinColumn(nullable=true)
     * @Serializer\MaxDepth(1)
     * @Serializer\Expose()
     * @Serializer\Groups({"add", "update","list", "detail"})
     */
    private $order;
    
    
    /**
     * @var string
     *
     * @ORM\Column(name="order_type", type="string", length=255, nullable=true)
     * @Serializer\Groups({"add", "update","list", "detail"})
     */
    private $orderType;

    /**
     * @ORM\Column(name="total_price", type="decimal", precision=20, scale=6, nullable=true)
     * @Serializer\Expose
     * @Serializer\Groups({"add", "update","list", "detail"})
     */
    private $totalPrice;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="date", type="datetime", nullable=true)
     * @Serializer\Type("DateTime<'Y-m-d'>")
     * @Serializer\Expose
     * @Serializer\Groups({"add", "update","list", "detail"})
     */
    private $date;

    
    /**
     * @var string|null
     *
     * @ORM\Column(name="code", type="string", length=255, unique=true)
     * @Serializer\Expose
     * @Serializer\Groups({"add", "update"})
     */
    private $code;

    /**
     * @ORM\OneToMany(targetEntity="InvoiceItem", mappedBy="invoice")
     * @Serializer\Expose
     * @Serializer\MaxDepth(1)
     * @Serializer\Groups({"detail"})
     */
    private $invoiceItems;
    
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->invoiceItems = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Set number.
     *
     * @param string|null $number
     *
     * @return Invoice
     */
    public function setNumber($number = null)
    {
        $this->number = $number;

        return $this;
    }

    /**
     * Get number.
     *
     * @return string|null
     */
    public function getNumber()
    {
        return $this->number;
    }

    /**
     * Set orderType.
     *
     * @param string|null $orderType
     *
     * @return Invoice
     */
    public function setOrderType($orderType = null)
    {
        $this->orderType = $orderType;

        return $this;
    }

    /**
     * Get orderType.
     *
     * @return string|null
     */
    public function getOrderType()
    {
        return $this->orderType;
    }

    /**
     * Set totalPrice.
     *
     * @param string|null $totalPrice
     *
     * @return Invoice
     */
    public function setTotalPrice($totalPrice = null)
    {
        $this->totalPrice = $totalPrice;

        return $this;
    }

    /**
     * Get totalPrice.
     *
     * @return string|null
     */
    public function getTotalPrice()
    {
        return $this->totalPrice;
    }

    /**
     * Set date.
     *
     * @param \DateTime|null $date
     *
     * @return Invoice
     */
    public function setDate($date = null)
    {
        $this->date = $date;

        return $this;
    }

    /**
     * Get date.
     *
     * @return \DateTime|null
     */
    public function getDate()
    {
        return $this->date;
    }

    /**
     * Set dateCreate.
     *
     * @param \DateTime|null $dateCreate
     *
     * @return Invoice
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
     * @return Invoice
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
     * Set platform.
     *
     * @param \Sogedial\ApiBundle\Entity\Platform|null $platform
     *
     * @return Invoice
     */
    public function setPlatform(\Sogedial\ApiBundle\Entity\Platform $platform = null)
    {
        $this->platform = $platform;

        return $this;
    }

    /**
     * Get platform.
     *
     * @return \Sogedial\ApiBundle\Entity\Platform|null
     */
    public function getPlatform()
    {
        return $this->platform;
    }

    /**
     * Set client.
     *
     * @param \Sogedial\ApiBundle\Entity\Client $client
     *
     * @return Invoice
     */
    public function setClient(\Sogedial\ApiBundle\Entity\Client $client)
    {
        $this->client = $client;

        return $this;
    }

    /**
     * Get client.
     *
     * @return \Sogedial\ApiBundle\Entity\Client
     */
    public function getClient()
    {
        return $this->client;
    }

    /**
     * Set order.
     *
     * @param \Sogedial\ApiBundle\Entity\Order $order
     *
     * @return Invoice
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
     * Add invoiceItem.
     *
     * @param \Sogedial\ApiBundle\Entity\InvoiceItem $invoiceItem
     *
     * @return Invoice
     */
    public function addInvoiceItem(\Sogedial\ApiBundle\Entity\InvoiceItem $invoiceItem)
    {
        $this->invoiceItems[] = $invoiceItem;

        return $this;
    }

    /**
     * Remove invoiceItem.
     *
     * @param \Sogedial\ApiBundle\Entity\InvoiceItem $invoiceItem
     *
     * @return boolean TRUE if this collection contained the specified element, FALSE otherwise.
     */
    public function removeInvoiceItem(\Sogedial\ApiBundle\Entity\InvoiceItem $invoiceItem)
    {
        return $this->invoiceItems->removeElement($invoiceItem);
    }

    /**
     * Get invoiceItems.
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getInvoiceItems()
    {
        return $this->invoiceItems;
    }

    /**
     * Set code.
     *
     * @param string $code
     *
     * @return Invoice
     */
    public function setCode($code)
    {
        $this->code = $code;

        return $this;
    }

    /**
     * Get code.
     *
     * @return string
     */
    public function getCode()
    {
        return $this->code;
    }


    /**
     * @Serializer\VirtualProperty()
     * @Serializer\Groups({"list", "detail"})
     */
    public function getTotalsByType()
    {
        $totals = [];

        if (!empty($this->invoiceItems)) {
            foreach ($this->invoiceItems as $invoiceItem) {                
                if ($invoiceItem->getItemType()) {
                    if (!isset($totals[$invoiceItem->getItemType()])) {
                        $totals[$invoiceItem->getItemType()] = array(
                            'total_price_vat' => 0,
                            'total_price' => 0,
                            'total_quantity' => 0,
                        );
                    }

                    $totals[$invoiceItem->getItemType()]['total_price_vat'] += $invoiceItem->getFinalPriceVat();
                    $totals[$invoiceItem->getItemType()]['total_price'] += $invoiceItem->getFinalPrice();
                    $totals[$invoiceItem->getItemType()]['total_quantity'] += $invoiceItem->getQuantityInvoicedUC();
                }
            }
        }

        return $totals;
    }

    /**
     * @Serializer\VirtualProperty()
     * @Serializer\Groups({"list", "detail"})
     */
    public function getCountItemsByType()
    {
        $totals = [];

        if(!empty($this->invoiceItems)){
            foreach ($this->invoiceItems as $invoiceItem){
                if( $invoiceItem->getItem() ){
                    if( !isset($totals[ $invoiceItem->getItemType() ])){
                        $totals[ $invoiceItem->getItemType() ] = 0;
                    }

                    $totals[$invoiceItem->getItemType()] += 1;
                }
            }
        }

        return $totals;
    }

    /**
     * @Serializer\VirtualProperty()
     * @Serializer\Groups({"list", "detail"})
     */
    public function getReferences()
    {

        $packages = 0;
        $units = 0;

        if (!empty($this->invoiceItems)) {
            foreach ($this->invoiceItems as $invoiceItem) {
                $packages += $invoiceItem->getQuantityInvoicedPackage();
                $units += $invoiceItem->getQuantityInvoicedUC();
            }
        }

        return array('packages' => $packages, 'items' => $units);
    }

}
