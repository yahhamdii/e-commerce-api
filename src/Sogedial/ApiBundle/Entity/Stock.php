<?php

namespace Sogedial\ApiBundle\Entity;

use JMS\Serializer\Annotation as Serializer;
use Doctrine\ORM\Mapping as ORM;

/**
 * Stock
 *
 * @ORM\Table(name="stock")
 * @ORM\Entity(repositoryClass="Sogedial\ApiBundle\Repository\StockRepository")
 * @Serializer\ExclusionPolicy("all")
 */
class Stock extends AbstractEntity {

    /**
     * @var int
     * @Serializer\Expose
     * @ORM\Column(name="value_cu", type="integer")
     * @Serializer\Groups({"add", "update","list", "detail", "search"})
     */
    private $valueCu;

    /**
     * @var int
     * @Serializer\Expose
     * @ORM\Column(name="value_packing", type="integer")
     * @Serializer\Groups({"add", "update","list", "detail", "search"})
     */
    private $valuePacking;

    /**
     * @var string|null
     * @Serializer\Expose
     * @ORM\Column(name="code", type="string", length=255, unique=true)
     * @Serializer\Groups({"add", "update","list", "detail"})
     */
    private $code;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="next_date_delivery", type="datetime", nullable=true)
     * @Serializer\Groups({"add", "update","list", "detail"})* 
     * @Serializer\Expose
     */
    private $nextDateDelivery;

    /**
     * @var float
     *
     * @ORM\Column(name="quantity_package_next_delivery", type="float", nullable=true )
     * @Serializer\Groups({"add", "update","list", "detail"})
     * @Serializer\Expose
     */
    private $quantityPackageNextDelivery;

    /**
     * @var float
     *
     * @ORM\Column(name="quantity_uc_next_delivery", type="float", nullable=true )
     * @Serializer\Groups({"add", "update","list", "detail"})
     * @Serializer\Expose
     */
    private $quantityUcNextDelivery;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="first_date_entry_in_stock", type="datetime", nullable=true)
     * @Serializer\Groups({"add", "update","list", "detail"})
     * @Serializer\Expose
     */
    private $firstDateEntryInStock;

    /**
     * @ORM\OneToMany(targetEntity="Item", mappedBy="stock")
     */
    private $items;

    /**
     * Set valueCu.
     *
     * @param int $valueCu
     *
     * @return Stock
     */
    public function setValueCu($valueCu) {
        $this->valueCu = $valueCu;

        return $this;
    }

    /**
     * Get valueCu.
     *
     * @return int
     */
    public function getValueCu() {
        return $this->valueCu;
    }

    /**
     * Set valuePacking.
     *
     * @param int $valuePacking
     *
     * @return Stock
     */
    public function setValuePacking($valuePacking) {
        $this->valuePacking = $valuePacking;

        return $this;
    }

    /**
     * Get valuePacking.
     *
     * @return int
     */
    public function getValuePacking() {
        return $this->valuePacking;
    }

    /**
     * Set dateCreate.
     *
     * @param \DateTime|null $dateCreate
     *
     * @return Stock
     */
    public function setDateCreate($dateCreate = null) {
        $this->dateCreate = $dateCreate;

        return $this;
    }

    /**
     * Get dateCreate.
     *
     * @return \DateTime|null
     */
    public function getDateCreate() {
        return $this->dateCreate;
    }

    /**
     * Set dateUpdate.
     *
     * @param \DateTime|null $dateUpdate
     *
     * @return Stock
     */
    public function setDateUpdate($dateUpdate = null) {
        $this->dateUpdate = $dateUpdate;

        return $this;
    }

    /**
     * Get dateUpdate.
     *
     * @return \DateTime|null
     */
    public function getDateUpdate() {
        return $this->dateUpdate;
    }

    /**
     * Set code.
     *
     * @param string|null $code
     *
     * @return Stock
     */
    public function setCode($code = null) {
        $this->code = $code;

        return $this;
    }

    /**
     * Get code.
     *
     * @return string|null
     */
    public function getCode() {
        return $this->code;
    }

    /**
     * Constructor
     */
    public function __construct() {
        $this->items = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Add item.
     *
     * @param \Sogedial\ApiBundle\Entity\Item $item
     *
     * @return Stock
     */
    public function addItem(\Sogedial\ApiBundle\Entity\Item $item) {
        $this->items[] = $item;

        return $this;
    }

    /**
     * Remove item.
     *
     * @param \Sogedial\ApiBundle\Entity\Item $item
     *
     * @return boolean TRUE if this collection contained the specified element, FALSE otherwise.
     */
    public function removeItem(\Sogedial\ApiBundle\Entity\Item $item) {
        return $this->items->removeElement($item);
    }

    /**
     * Get items.
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getItems($onlyActive = true) {
        return (!$onlyActive)?$this->items:$this->items->filter(function($item){
            return $item->getActive();
        });
    }

    /**
     * Set nextDateDelivery.
     *
     * @param \DateTime $nextDateDelivery
     *
     * @return Stock
     */
    public function setNextDateDelivery($nextDateDelivery) {
        $this->nextDateDelivery = $nextDateDelivery;

        return $this;
    }

    /**
     * Get nextDateDelivery.
     *
     * @return \DateTime
     */
    public function getNextDateDelivery() {
        return $this->nextDateDelivery;
    }

    /**
     * Set quantityPackageNextDelivery.
     *
     * @param float|null $quantityPackageNextDelivery
     *
     * @return Stock
     */
    public function setQuantityPackageNextDelivery($quantityPackageNextDelivery = null) {
        $this->quantityPackageNextDelivery = $quantityPackageNextDelivery;

        return $this;
    }

    /**
     * Get quantityPackageNextDelivery.
     *
     * @return float|null
     */
    public function getQuantityPackageNextDelivery() {
        return $this->quantityPackageNextDelivery;
    }

    /**
     * Set quantityUcNextDelivery.
     *
     * @param float|null $quantityUcNextDelivery
     *
     * @return Stock
     */
    public function setQuantityUcNextDelivery($quantityUcNextDelivery = null) {
        $this->quantityUcNextDelivery = $quantityUcNextDelivery;

        return $this;
    }

    /**
     * Get quantityUcNextDelivery.
     *
     * @return float|null
     */
    public function getQuantityUcNextDelivery() {
        return $this->quantityUcNextDelivery;
    }

    /**
     * Set firstDateEntryInStock.
     *
     * @param \DateTime $firstDateEntryInStock
     *
     * @return Stock
     */
    public function setFirstDateEntryInStock($firstDateEntryInStock) {
        $this->firstDateEntryInStock = $firstDateEntryInStock;

        return $this;
    }

    /**
     * Get firstDateEntryInStock.
     *
     * @return \DateTime
     */
    public function getFirstDateEntryInStock() {
        return $this->firstDateEntryInStock;
    }

}
