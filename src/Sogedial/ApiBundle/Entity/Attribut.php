<?php

namespace Sogedial\ApiBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use JMS\Serializer\Annotation as Serializer;

/**
 * Attribut
 *
 * @ORM\Table(name="`attribut`")
 * @ORM\Entity(repositoryClass="Sogedial\ApiBundle\Repository\AttributRepository")
 * @Serializer\ExclusionPolicy("all")
 */
class Attribut extends AbstractEntity {

    const KEY_NEW = 'new-delay';
    CONST KEY_PREORDER_END_DATE = 'preorder-date-end';
    CONST KEY_PREORDER_DELIVERY_DATE = 'preorder-delivery-date';
    CONST KEY_PREORDER_DATE_BEGIN = 'preorder-date-begin';
    CONST KEY_PREORDER_DURATION = 'preorder-duration';
    CONST KEY_PREORDER_DELIVERY = 'preorder-delivery';
    CONST KEY_DELIVERY_MODE = 'delivery-mode';
    CONST KEY_ORDERED_BY = 'ordered-by';
    CONST KEY_STOCK_CART = 'stock-cart';
    CONST KEY_HAS_MOQ = 'has-moq';
    CONST KEY_SPLITTING_ORDER = 'splitting-order';
    CONST KEY_HAS_CONTAINERIZATION = 'has-containerization';

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=255)
     * @Serializer\Expose
     */
    private $name;

    /**
     * @var string
     *
     * @ORM\Column(name="`key`", type="string", length=255)
     * @Gedmo\Slug(fields={"name"}, unique=false)
     * @Serializer\Expose
     */
    private $key;

    /**
     * @var string
     *
     * @ORM\Column(name="value", type="string", length=255)
     * @Serializer\Expose
     */
    private $value;

    /**
     * @ORM\ManyToOne(targetEntity="Platform", inversedBy="attributs")
     * @ORM\JoinColumn(nullable=true)
     * @Serializer\Expose
     * @Serializer\MaxDepth(1)
     */
    private $platform;

    /**
     * Set name.
     *
     * @param string $name
     *
     * @return Attribut
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
     * Set value.
     *
     * @param string $value
     *
     * @return Attribut
     */
    public function setValue($value) {
        $this->value = $value;

        return $this;
    }

    /**
     * Get value.
     *
     * @return string
     */
    public function getValue() {
        return $this->value;
    }

    /**
     * Set dateCreate.
     *
     * @param \DateTime|null $dateCreate
     *
     * @return Attribut
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
     * @return Attribut
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
     * Set key
     *
     * @param string $key
     *
     * @return Attribut
     */
    public function setKey($key) {
        $this->key = $key;

        return $this;
    }

    /**
     * Get key
     *
     * @return string
     */
    public function getKey() {
        return $this->key;
    }


    /**
     * Set platform.
     *
     * @param \Sogedial\ApiBundle\Entity\Platform|null $platform
     *
     * @return Attribut
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
}
