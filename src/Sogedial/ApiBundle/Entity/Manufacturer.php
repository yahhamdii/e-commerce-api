<?php

namespace Sogedial\ApiBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use JMS\Serializer\Annotation as Serializer;

/**
 * Manufacturer
 *
 * @ORM\Table(name="manufacturer")
 * @ORM\Entity(repositoryClass="Sogedial\ApiBundle\Repository\ManufacturerRepository")
 * @Serializer\ExclusionPolicy("all")
 */
class Manufacturer extends AbstractEntity {

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=255)
     * @Serializer\Groups({"add", "update","list", "detail", "search"})
     * @Serializer\Expose
     */
    private $name;

    /**
     * @var string
     * @Gedmo\Slug(fields={"name"}, unique=true)
     * @ORM\Column(name="slug", type="string", length=255)
     * @Serializer\Groups({"add", "update","list", "detail"})
     * @Serializer\Expose
     */
    private $slug;

    /**
     * @var string
     *
     * @ORM\Column(name="ext_code", type="string", length=255, unique=true)
     */
    private $extCode;

    /**
     * @ORM\OneToMany(targetEntity="Product", mappedBy="manufacturer")
     */
    private $products;

    /**
     * Set name.
     *
     * @param string $name
     *
     * @return Manufacturer
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
     * @return Manufacturer
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
     * Set extCode.
     *
     * @param string $extCode
     *
     * @return Manufacturer
     */
    public function setExtCode($extCode) {
        $this->extCode = $extCode;

        return $this;
    }

    /**
     * Get extCode.
     *
     * @return string
     */
    public function getExtCode() {
        return $this->extCode;
    }

    /**
     * Constructor
     */
    public function __construct() {
        $this->products = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Add product.
     *
     * @param \Sogedial\ApiBundle\Entity\Product $product
     *
     * @return Manufacturer
     */
    public function addProduct(\Sogedial\ApiBundle\Entity\Product $product) {
        $this->products[] = $product;

        return $this;
    }

    /**
     * Remove product.
     *
     * @param \Sogedial\ApiBundle\Entity\Product $product
     *
     * @return boolean TRUE if this collection contained the specified element, FALSE otherwise.
     */
    public function removeProduct(\Sogedial\ApiBundle\Entity\Product $product) {
        return $this->products->removeElement($product);
    }

    /**
     * Get products.
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getProducts() {
        return $this->products;
    }

    /**
     * Set dateCreate.
     *
     * @param \DateTime|null $dateCreate
     *
     * @return Manufacturer
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
     * @return Manufacturer
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

}
