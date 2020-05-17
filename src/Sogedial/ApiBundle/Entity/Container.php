<?php

namespace Sogedial\ApiBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as Serializer;

/**
 * Container
 *
 * @ORM\HasLifecycleCallbacks()
 * @ORM\Table(name="container")
 * @ORM\Entity(repositoryClass="Sogedial\ApiBundle\Repository\ContainerRepository")
 * @Serializer\ExclusionPolicy("all")
 */
class Container extends AbstractEntity
{

    const TYPE_20_REEFER = 'TC20_REEFER';
    const TYPE_20_DRY = 'TC20_DRY';
    const TYPE_40_REEFER = 'TC40_REEFER';
    const TYPE_40_DRY = 'TC40_DRY';
    const LOADING_PALLET = 'PALETTE';
    const LOADING_VRAC = 'VRAC';


    /**
     * @var string
     * @Serializer\Expose
     * @ORM\Column(name="loading_type", type="string", length=20)
     */
    private $loadingType;

    /**
     * @var string
     * @Serializer\Expose
     * @ORM\Column(name="name", type="string", length=20)
     */
    private $name;

    /**
     * @Serializer\Expose
     * @ORM\Column(name="volume_permissible_max", type="decimal", precision=8, scale=2, nullable = true)
     */
    private $volumePermissibleMax;

    /**
     * @Serializer\Expose
     * @ORM\Column(name="weight_permissible_max", type="decimal", precision=8, scale=2, nullable = true)
     */
    private $weightPermissibleMax;

    /**
     * @Serializer\Expose
     * @ORM\Column(name="volume_loaded", type="decimal", precision=8, scale=2, nullable = true)
     */
    private $volumeLoaded;

    /**
     * @Serializer\Expose
     * @ORM\Column(name="weight_loaded", type="decimal", precision=8, scale=2, nullable = true)
     */
    private $weightLoaded;


    /**
     * @ORM\Column(name="temperature" ,type="string", length=20)
     * @Serializer\Expose
     */
    private $temperature;

    /**
     * @ORM\OneToMany(targetEntity="Sogedial\ApiBundle\Entity\ContainerCartItem", mappedBy="container", cascade={"remove"})
     * @ORM\JoinColumn(nullable= false)
     */
    private $containerCartItems;


    /**
     * Set loadingType.
     *
     * @param string $loadingType
     *
     * @return Container
     */
    public function setLoadingType($loadingType)
    {
        $this->loadingType = $loadingType;

        return $this;
    }

    /**
     * Get loadingType.
     *
     * @return string
     */
    public function getLoadingType()
    {
        return $this->loadingType;
    }

    /**
     * Set name.
     *
     * @param string $name
     *
     * @return Container
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name.
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set volumePermissibleMax.
     *
     * @param string|null $volumePermissibleMax
     *
     * @return Container
     */
    public function setVolumePermissibleMax($volumePermissibleMax = null)
    {
        $this->volumePermissibleMax = $volumePermissibleMax;

        return $this;
    }

    /**
     * Get volumePermissibleMax.
     *
     * @return string|null
     */
    public function getVolumePermissibleMax()
    {
        return $this->volumePermissibleMax;
    }

    /**
     * Set weightPermissibleMax.
     *
     * @param string|null $weightPermissibleMax
     *
     * @return Container
     */
    public function setWeightPermissibleMax($weightPermissibleMax = null)
    {
        $this->weightPermissibleMax = $weightPermissibleMax;

        return $this;
    }

    /**
     * Get weightPermissibleMax.
     *
     * @return string|null
     */
    public function getWeightPermissibleMax()
    {
        return $this->weightPermissibleMax;
    }

    /**
     * Set volumeLoaded.
     *
     * @param string|null $volumeLoaded
     *
     * @return Container
     */
    public function setVolumeLoaded($volumeLoaded = null)
    {
        $this->volumeLoaded = $volumeLoaded;

        return $this;
    }

    /**
     * Get volumeLoaded.
     *
     * @return string|null
     */
    public function getVolumeLoaded()
    {
        return $this->volumeLoaded;
    }

    /**
     * Set weightLoaded.
     *
     * @param string|null $weightLoaded
     *
     * @return Container
     */
    public function setWeightLoaded($weightLoaded = null)
    {
        $this->weightLoaded = $weightLoaded;

        return $this;
    }

    /**
     * Get weightLoaded.
     *
     * @return string|null
     */
    public function getWeightLoaded()
    {
        return $this->weightLoaded;
    }

    /**
     * Set dateCreate.
     *
     * @param \DateTime|null $dateCreate
     *
     * @return Container
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
     * @return Container
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
     * Set temperature.
     *
     * @param string $temperature
     *
     * @return Container
     */
    public function setTemperature($temperature)
    {
        $this->temperature = $temperature;

        return $this;
    }

    /**
     * Get temperature.
     *
     * @return string
     */
    public function getTemperature()
    {
        return $this->temperature;
    }


    /**
     * @ORM\PrePersist()
     * @ORM\PreUpdate()
     */
    public function updateContainer()
    {
        if ($this->loadingType == self::LOADING_VRAC) {
            if ($this->name == self::TYPE_20_DRY) {
                $this->setVolumePermissibleMax("29");
                $this->setWeightPermissibleMax("25500");
            } elseif ($this->name == self::TYPE_20_REEFER) {
                $this->setVolumePermissibleMax("25");
                $this->setWeightPermissibleMax("25500");
            } elseif ($this->name == self::TYPE_40_DRY) {
                $this->setVolumePermissibleMax("68");
                $this->setWeightPermissibleMax("25500");
            } elseif ($this->name == self::TYPE_40_REEFER) {
                $this->setVolumePermissibleMax(null);
                $this->setWeightPermissibleMax(null);
            }
        } elseif ($this->loadingType == self::LOADING_PALLET) {
            if ($this->name == self::TYPE_20_DRY) {
                $this->setVolumePermissibleMax("26");
                $this->setWeightPermissibleMax("25500");
            } elseif ($this->name == self::TYPE_20_REEFER) {
                $this->setVolumePermissibleMax("22");
                $this->setWeightPermissibleMax("25500");
            } elseif ($this->name == self::TYPE_40_DRY) {
                $this->setVolumePermissibleMax("59");
                $this->setWeightPermissibleMax("25500");
            } elseif ($this->name == self::TYPE_40_REEFER) {
                $this->setVolumePermissibleMax("53");
                $this->setWeightPermissibleMax("25500");
            }
        }
    }

    /**
    * calcule le nombre de colis qu'on peut chargé dans ce container
    * sans depassé le volume et poids permissible
    */
   public function numberPackageAllowed($volumePackage, $weightPackage)
   {
       //calculer le volume et poids encore disponible
       $availableVolume = $this->getVolumePermissibleMax() - $this->getVolumeLoaded();
       $availableWeight = $this->getWeightPermissibleMax() - $this->getWeightLoaded();
       //calculer pour ce volume et poids encore disponible combien de colis on peut chargé,
       // ensuite on prend le minimum pour ne pas depasser le volume et poids permissible
       if(isset($volumePackage)){
           $quantityDependingOnVolume = floor($availableVolume / $volumePackage);
       }
       if(isset($weightPackage)){
           $quantityDependingOnWeight = floor($availableWeight / $weightPackage);
       }
       
       return min($quantityDependingOnVolume, $quantityDependingOnWeight);
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
     * @return Container
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
