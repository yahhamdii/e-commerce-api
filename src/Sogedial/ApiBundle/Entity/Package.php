<?php

namespace Sogedial\ApiBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as Serializer;

/**
 * package
 *
 * @ORM\Table(name="package")
 * @ORM\Entity(repositoryClass="Sogedial\ApiBundle\Repository\PackageRepository")
 * @Serializer\ExclusionPolicy("all")
 */
class Package extends AbstractEntity {

    /**
     * @var string     
     * @ORM\Column(name="code", type="string", length=255, unique=true)
     */
    private $code;

    /**
     * @var string|null
     * @ORM\Column(name="weight_gross_ctu", type="string", length=255, nullable=true)
     */
    private $weightGrossCtu;

    /**
     * @var string|null     
     * @ORM\Column(name="weight_net_ctu", type="string", length=255, nullable=true)
     */
    private $weightNetCtu;

    /**
     * @var string|null     
     * @Serializer\Expose     
     * @Serializer\Groups({"list", "detail", "search"})
     * @ORM\Column(name="weight_gross_package", type="string", length=255, nullable=true)
     */
    private $weightGrossPackage;

    /**
     * @var string
     * @Serializer\Expose     
     * @Serializer\Groups({"list", "detail", "search"})
     * @ORM\Column(name="volume_package", type="string", length=255)
     */
    private $volumePackage;

    /**
     * @var string|null     
     * @ORM\Column(name="pallet_layer", type="string", length=255, nullable=true)
     */
    private $palletLayer;

    /**
     * @var string|null     
     * @ORM\Column(name="pallet_package", type="string", length=255, nullable=true)
     */
    private $palletPackage;

    /**
     * @var string     
     * @ORM\Column(name="commercial_unit_number", type="string", length=255)
     */
    private $commercialUnitNumber;

    /**
     * @var string|null     
     * @ORM\Column(name="commercial_unit_type", type="string", length=255, nullable=true)
     */
    private $commercialUnitType;

    /**
     * @var string|null     
     * @ORM\Column(name="commercial_unit_description", type="text", nullable=true)
     */
    private $commercialUnitDescription;

    /**
     * @var string|null     
     * @ORM\Column(name="volume_uc", type="string", length=255, nullable=true)
     */
    private $volumeUc;

    /**
     * @var string|null          
     * @ORM\Column(name="length_dimensions_uc", type="string", length=255, nullable=true)
     */
    private $lengthDimensionsUc;

    /**
     * @var string|null     
     * @ORM\Column(name="width_dimensions_uc", type="string", length=255, nullable=true)
     */
    private $widthDimensionsUc;

    /**
     * @var string|null     
     * @ORM\Column(name="height_dimensions_uc", type="string", length=255, nullable=true)
     */
    private $heightDimensionsUc;

    /**
     * @ORM\OneToMany(targetEntity="Item", mappedBy="package")
     */
    private $items;
    
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->items = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Set code.
     *
     * @param string $code
     *
     * @return Package
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
     * Set weightGrossCtu.
     *
     * @param string|null $weightGrossCtu
     *
     * @return Package
     */
    public function setWeightGrossCtu($weightGrossCtu = null)
    {
        $this->weightGrossCtu = $weightGrossCtu;

        return $this;
    }

    /**
     * Get weightGrossCtu.
     *
     * @return string|null
     */
    public function getWeightGrossCtu()
    {
        return $this->weightGrossCtu;
    }

    /**
     * Set weightNetCtu.
     *
     * @param string|null $weightNetCtu
     *
     * @return Package
     */
    public function setWeightNetCtu($weightNetCtu = null)
    {
        $this->weightNetCtu = $weightNetCtu;

        return $this;
    }

    /**
     * Get weightNetCtu.
     *
     * @return string|null
     */
    public function getWeightNetCtu()
    {
        return $this->weightNetCtu;
    }

    /**
     * Set weightGrossPackage.
     *
     * @param string|null $weightGrossPackage
     *
     * @return Package
     */
    public function setWeightGrossPackage($weightGrossPackage = null)
    {
        $this->weightGrossPackage = $weightGrossPackage;

        return $this;
    }

    /**
     * Get weightGrossPackage.
     *
     * @return string|null
     */
    public function getWeightGrossPackage()
    {
        return $this->weightGrossPackage;
    }

    /**
     * Set volumePackage.
     *
     * @param string $volumePackage
     *
     * @return Package
     */
    public function setVolumePackage($volumePackage)
    {
        $this->volumePackage = $volumePackage;

        return $this;
    }

    /**
     * Get volumePackage.
     *
     * @return string
     */
    public function getVolumePackage()
    {
        return $this->volumePackage;
    }

    /**
     * Set palletLayer.
     *
     * @param string|null $palletLayer
     *
     * @return Package
     */
    public function setPalletLayer($palletLayer = null)
    {
        $this->palletLayer = $palletLayer;

        return $this;
    }

    /**
     * Get palletLayer.
     *
     * @return string|null
     */
    public function getPalletLayer()
    {
        return $this->palletLayer;
    }

    /**
     * Set palletPackage.
     *
     * @param string|null $palletPackage
     *
     * @return Package
     */
    public function setPalletPackage($palletPackage = null)
    {
        $this->palletPackage = $palletPackage;

        return $this;
    }

    /**
     * Get palletPackage.
     *
     * @return string|null
     */
    public function getPalletPackage()
    {
        return $this->palletPackage;
    }

    /**
     * Set commercialUnitNumber.
     *
     * @param string $commercialUnitNumber
     *
     * @return Package
     */
    public function setCommercialUnitNumber($commercialUnitNumber)
    {
        $this->commercialUnitNumber = $commercialUnitNumber;

        return $this;
    }

    /**
     * Get commercialUnitNumber.
     *
     * @return string
     */
    public function getCommercialUnitNumber()
    {
        return $this->commercialUnitNumber;
    }

    /**
     * Set commercialUnitType.
     *
     * @param string|null $commercialUnitType
     *
     * @return Package
     */
    public function setCommercialUnitType($commercialUnitType = null)
    {
        $this->commercialUnitType = $commercialUnitType;

        return $this;
    }

    /**
     * Get commercialUnitType.
     *
     * @return string|null
     */
    public function getCommercialUnitType()
    {
        return $this->commercialUnitType;
    }

    /**
     * Set commercialUnitDescription.
     *
     * @param string|null $commercialUnitDescription
     *
     * @return Package
     */
    public function setCommercialUnitDescription($commercialUnitDescription = null)
    {
        $this->commercialUnitDescription = $commercialUnitDescription;

        return $this;
    }

    /**
     * Get commercialUnitDescription.
     *
     * @return string|null
     */
    public function getCommercialUnitDescription()
    {
        return $this->commercialUnitDescription;
    }

    /**
     * Set volumeUc.
     *
     * @param string|null $volumeUc
     *
     * @return Package
     */
    public function setVolumeUc($volumeUc = null)
    {
        $this->volumeUc = $volumeUc;

        return $this;
    }

    /**
     * Get volumeUc.
     *
     * @return string|null
     */
    public function getVolumeUc()
    {
        return $this->volumeUc;
    }

    /**
     * Set lengthDimensionsUc.
     *
     * @param string|null $lengthDimensionsUc
     *
     * @return Package
     */
    public function setLengthDimensionsUc($lengthDimensionsUc = null)
    {
        $this->lengthDimensionsUc = $lengthDimensionsUc;

        return $this;
    }

    /**
     * Get lengthDimensionsUc.
     *
     * @return string|null
     */
    public function getLengthDimensionsUc()
    {
        return $this->lengthDimensionsUc;
    }

    /**
     * Set widthDimensionsUc.
     *
     * @param string|null $widthDimensionsUc
     *
     * @return Package
     */
    public function setWidthDimensionsUc($widthDimensionsUc = null)
    {
        $this->widthDimensionsUc = $widthDimensionsUc;

        return $this;
    }

    /**
     * Get widthDimensionsUc.
     *
     * @return string|null
     */
    public function getWidthDimensionsUc()
    {
        return $this->widthDimensionsUc;
    }

    /**
     * Set heightDimensionsUc.
     *
     * @param string|null $heightDimensionsUc
     *
     * @return Package
     */
    public function setHeightDimensionsUc($heightDimensionsUc = null)
    {
        $this->heightDimensionsUc = $heightDimensionsUc;

        return $this;
    }

    /**
     * Get heightDimensionsUc.
     *
     * @return string|null
     */
    public function getHeightDimensionsUc()
    {
        return $this->heightDimensionsUc;
    }

    /**
     * Set dateCreate.
     *
     * @param \DateTime|null $dateCreate
     *
     * @return Package
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
     * @return Package
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
     * Add item.
     *
     * @param \Sogedial\ApiBundle\Entity\Item $item
     *
     * @return Package
     */
    public function addItem(\Sogedial\ApiBundle\Entity\Item $item)
    {
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
    public function removeItem(\Sogedial\ApiBundle\Entity\Item $item)
    {
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
}
