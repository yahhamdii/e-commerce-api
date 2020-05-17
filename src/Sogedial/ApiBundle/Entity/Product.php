<?php

namespace Sogedial\ApiBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use JMS\Serializer\Annotation as Serializer;

/**
 * Product
 *
 * @ORM\Table(name="product")
 * @ORM\Entity(repositoryClass="Sogedial\ApiBundle\Repository\ProductRepository")
 * @Serializer\ExclusionPolicy("all")
 */
class Product extends AbstractEntity {

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=255)
     * @Serializer\Groups({"add", "update","list", "detail"})
     * @Serializer\Expose
     */
    private $name;

    /**
     * @var string
     *
     * @ORM\Column(name="slug", type="string", length=255)
     * @Gedmo\Slug(fields={"name"}, unique=true)
     * @Serializer\Groups({"add", "update","list", "detail"})
     * @Serializer\Expose
     */
    private $slug;

    /**
     * @var string
     *
     * @ORM\Column(name="ean", type="string", length=255, unique=true)
     * @Serializer\Groups({"add", "update","list", "detail"})
     * @Serializer\Expose
     */
    private $ean;

    /**
     * @var string
     *
     * @ORM\Column(name="code", type="string", length=255)
     * @Serializer\Expose
     */
    private $code;

    /**
     * @ORM\ManyToOne(targetEntity="Manufacturer", inversedBy="products")
     * @ORM\JoinColumn(name="manufacturer_id", referencedColumnName="id")
     * @Serializer\Groups({"add", "update","list", "detail"})
     * @Serializer\Expose
     */
    private $manufacturer;

    /**
     *
     * @ORM\ManyToMany(targetEntity="Platform", inversedBy="products")
     *
     */
    private $platforms;

    /**
     * @ORM\OneToMany(targetEntity="Item", mappedBy="product")
     */
    private $items;


    /**
     * Set name.
     *
     * @param string $name
     *
     * @return Product
     */
    public function setName($name) {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name.
     *
     * @return string
     */
    public function getName() {
        return $this->name;
    }

    /**
     * Set slug.
     *
     * @param string $slug
     *
     * @return Product
     */
    public function setSlug($slug) {
        $this->slug = $slug;

        return $this;
    }

    /**
     * Get slug.
     *
     * @return string
     */
    public function getSlug() {
        return $this->slug;
    }

    /**
     * Set ean.
     *
     * @param string $ean
     *
     * @return Product
     */
    public function setEan($ean) {
        $this->ean = $ean;

        return $this;
    }

    /**
     * Get ean.
     *
     * @return string
     */
    public function getEan() {
        return $this->ean;
    }

    /**
     * Constructor
     */
    public function __construct() {
        $this->platforms = new \Doctrine\Common\Collections\ArrayCollection();
        $this->items = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Set manufacturer.
     *
     * @param \Sogedial\ApiBundle\Entity\Manufacturer|null $manufacturer
     *
     * @return Product
     */
    public function setManufacturer(\Sogedial\ApiBundle\Entity\Manufacturer $manufacturer = null) {
        $this->manufacturer = $manufacturer;

        return $this;
    }

    /**
     * Get manufacturer.
     *
     * @return \Sogedial\ApiBundle\Entity\Manufacturer|null
     */
    public function getManufacturer() {
        return $this->manufacturer;
    }

    /**
     * Add platform.
     *
     * @param \Sogedial\ApiBundle\Entity\Platform $platform
     *
     * @return Product
     */
    public function addPlatform(\Sogedial\ApiBundle\Entity\Platform $platform) {
        $this->platforms[] = $platform;

        return $this;
    }

    /**
     * Remove platform.
     *
     * @param \Sogedial\ApiBundle\Entity\Platform $platform
     *
     * @return boolean TRUE if this collection contained the specified element, FALSE otherwise.
     */
    public function removePlatform(\Sogedial\ApiBundle\Entity\Platform $platform) {
        return $this->platforms->removeElement($platform);
    }

    /**
     * Get platforms.
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getPlatforms() {
        return $this->platforms;
    }

    /**
     * Add item.
     *
     * @param \Sogedial\ApiBundle\Entity\Item $item
     *
     * @return Product
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
     * Set dateCreate.
     *
     * @param \DateTime|null $dateCreate
     *
     * @return Product
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
     * @return Product
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
     * @param string $code
     *
     * @return Product
     */
    public function setCode($code) {
        $this->code = $code;

        return $this;
    }

    /**
     * Get code.
     *
     * @return string
     */
    public function getCode() {
        return $this->code;
    }

}
