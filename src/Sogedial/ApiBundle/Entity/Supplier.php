<?php

namespace Sogedial\ApiBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use JMS\Serializer\Annotation as Serializer;

/**
 * Supplier
 *
 * @ORM\Table(name="supplier")
 * @ORM\Entity(repositoryClass="Sogedial\ApiBundle\Repository\SupplierRepository")
 * @Serializer\ExclusionPolicy("all")
 */
class Supplier extends AbstractEntity {

    /**
     * @var string
     * @Serializer\Expose
     * @ORM\Column(name="name", type="string", length=255)
     * @Serializer\Groups({"add", "update","list", "detail"})
     */
    private $name;

    /**
     * @var string
     * @Serializer\Expose
     * @ORM\Column(name="code", type="string", length=255, unique=true)
     * @Serializer\Groups({"add", "update","list", "detail"})
     */
    private $code;

    /**
     * @var string
     * @Serializer\Expose
     * @ORM\Column(name="appro", type="string", length=255, nullable=true)     
     */
    private $appro;

    /**
     * @var string
     * @Serializer\Expose
     * @ORM\Column(name="indicator11", type="string", length=1, nullable=true)
     */
    private $indicator11;

    /**
     * @var string
     * @Serializer\Expose
     * @ORM\Column(name="original_code", type="string", length=255, nullable=true)
     */
    private $originalCode;

    /**
     * @var string
     * @Serializer\Expose
     * @ORM\Column(name="frequency", type="text", nullable=true)
     */
    private $frequency;

    /**
     * @var string
     * @Serializer\Expose
     * @ORM\Column(name="appro_delay", type="string", length=180, nullable=true)
     */
    private $approDelay;

    /**
     * @var string
     * @Serializer\Expose
     * @ORM\Column(name="slug", type="string", length=255)
     * @Gedmo\Slug(fields={"name"}, unique=true)
     * @Serializer\Groups({"add", "update","list", "detail"})
     */
    private $slug;


    /**
     * @ORM\ManyToOne(targetEntity="Platform", inversedBy="suppliers")
     * @ORM\JoinColumn(name="platform_id", referencedColumnName="id")
     *
     */
    private $platform;

     /**
     * Constructor
     */
    public function __construct() {
        $this->items = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Set name.
     *
     * @param string $name
     *
     * @return Supplier
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
     * @return Supplier
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
     * Set dateCreate.
     *
     * @param \DateTime|null $dateCreate
     *
     * @return Supplier
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
     * @return Supplier
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
     * @return Supplier
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

    /**
     * Set appro.
     *
     * @param string|null $appro
     *
     * @return Supplier
     */
    public function setAppro($appro = null) {
        $this->appro = $appro;

        return $this;
    }

    /**
     * Get appro.
     *
     * @return string|null
     */
    public function getAppro() {
        return $this->appro;
    }

    /**
     * Set indicator11.
     *
     * @param string|null $indicator11
     *
     * @return Supplier
     */
    public function setIndicator11($indicator11 = null) {
        $this->indicator11 = $indicator11;

        return $this;
    }

    /**
     * Get indicator11.
     *
     * @return string|null
     */
    public function getIndicator11() {
        return $this->indicator11;
    }

    /**
     * Set originalCode.
     *
     * @param string|null $originalCode
     *
     * @return Supplier
     */
    public function setOriginalCode($originalCode = null) {
        $this->originalCode = $originalCode;

        return $this;
    }

    /**
     * Get originalCode.
     *
     * @return string|null
     */
    public function getOriginalCode() {
        return $this->originalCode;
    }

    /**
     * Set approDelay.
     *
     * @param string|null $approDelay
     *
     * @return Supplier
     */
    public function setApproDelay($approDelay = null) {
        $this->approDelay = $approDelay;

        return $this;
    }

    /**
     * Get approDelay.
     *
     * @return string|null
     */
    public function getApproDelay() {
        return $this->approDelay;
    }

    /**
     * Add item.
     *
     * @param \Sogedial\ApiBundle\Entity\Item $item
     *
     * @return Supplier
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
     * Set items.
     *
     * @param \Sogedial\ApiBundle\Entity\Item|null $items
     *
     * @return Supplier
     */
    public function setItems(\Sogedial\ApiBundle\Entity\Item $items = null)
    {
        $this->items = $items;

        return $this;
    }

    /**
     * Set frequency.
     *
     * @param string|null $frequency
     *
     * @return Supplier
     */
    public function setFrequency($frequency = null)
    {
        $this->frequency = $frequency;

        return $this;
    }

    /**
     * Get frequency.
     *
     * @return string|null
     */
    public function getFrequency()
    {
        return $this->frequency;
    }

    /**
     * Set platform.
     *
     * @param \Sogedial\ApiBundle\Entity\Platform|null $platform
     *
     * @return Supplier
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
