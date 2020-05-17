<?php

namespace Sogedial\ApiBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use JMS\Serializer\Annotation as Serializer;

/**
 * DeliveryPlanning
 *
 * @ORM\Table(name="delivery_planning")
 * @ORM\Entity(repositoryClass="Sogedial\ApiBundle\Repository\DeliveryPlanningRepository")
 * @Serializer\ExclusionPolicy("all")
 */
class DeliveryPlanning extends AbstractEntity
{

    const TEMPERATURE_DRY = "SEC";
    const TEMPERATURE_FRESH = "POSITIF";
    const TEMPERATURE_FROZEN = "FROID";

    /**
     * @ORM\ManyToOne(targetEntity="Platform")
     * @ORM\JoinColumn(nullable=false)
     * @Serializer\Groups({"add", "update","list", "detail"})
     * @Serializer\MaxDepth(1)
     * @Serializer\Expose
     */
    private $platform;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=45)
     * @Serializer\Groups({"add", "update","list", "detail"})
     * @Serializer\Expose
     */
    private $name;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="date_begin", type="datetime", nullable= true)
     * @Serializer\Type("DateTime<'Y-m-d'>")
     * @Gedmo\Timestampable(on="create")
     * @Serializer\Groups({"add", "update","list", "detail"})
     * @Serializer\Expose
     */
    private $dateBegin;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="date_end", type="datetime", nullable=true)
     * @Serializer\Type("DateTime<'Y-m-d'>")
     * @Serializer\Groups({"add", "update","list", "detail"})
     * @Serializer\Expose
     */
    private $dateEnd;


     /**
     * @var string
     *
     * @ORM\Column(name="temperature", type="string", length=45, nullable=true)
     * @Serializer\Groups({"add", "update","list", "detail"})
     * @Serializer\Expose
     */
    private $temperature;

    /**
     * @var bool
     *
     * @ORM\Column(name="active", type="boolean", nullable=true, options={"default" : 1})
     * @Serializer\Groups({"add", "update","list", "detail"})
     * @Serializer\Expose
     */
    private $active;

    /**
     * @var string
     *
     * @ORM\Column(name="time_zone", type="string", length=45, nullable=true)
     * @Serializer\Groups({"add", "update","list", "detail"})
     * @Serializer\Expose
     */
    private $timeZone;
    
    /**
     * @ORM\OneToMany(targetEntity="DeliveryPlanningDay", mappedBy="deliveryPlanning", cascade={"persist", "remove", "merge"})
     * @Serializer\Groups({"add", "update","list", "detail"})
     * @Serializer\MaxDepth(4)
     * @Serializer\Expose
     */
    private $days;

    /**
     * @ORM\ManyToOne(targetEntity="DeliveryPlanning", inversedBy="children")
     * @ORM\JoinColumn(name="parent_id", referencedColumnName="id")
     * @Serializer\Groups({"add", "update"})
     * @Serializer\Expose
     * @Serializer\MaxDepth(1)
     */
    private $parent;

    /**
     * @ORM\OneToMany(targetEntity="DeliveryPlanning", mappedBy="parent")     
     * @Serializer\Groups({"list", "detail", "update"})
     * @Serializer\Expose
     */
    private $children;

    /**
     * @ORM\ManyToMany(targetEntity="Client", mappedBy="deliveryPlannings")
     */
    private $clients;


    /**
     * @ORM\Column(name="delivery_mode" ,type="boolean" ,nullable=true)
     * @Serializer\Groups({"add", "update","list", "detail"})
     * @Serializer\Expose()
     */
    private $deliveryMode;

    /**
     * Set name.
     *
     * @param string $name
     *
     * @return DeliveryPlanning
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
     * Set dateBegin.
     *
     * @param \DateTime $dateBegin
     *
     * @return DeliveryPlanning
     */
    public function setDateBegin($dateBegin)
    {
        $this->dateBegin = $dateBegin;

        return $this;
    }

    /**
     * Get dateBegin.
     *
     * @return \DateTime
     */
    public function getDateBegin()
    {
        return $this->dateBegin;
    }

    /**
     * Set dateEnd.
     *
     * @param \DateTime $dateEnd
     *
     * @return DeliveryPlanning
     */
    public function setDateEnd($dateEnd)
    {
        $this->dateEnd = $dateEnd;

        return $this;
    }

    /**
     * Get dateEnd.
     *
     * @return \DateTime
     */
    public function getDateEnd()
    {
        return $this->dateEnd;
    }

    /**
     * Set active.
     *
     * @param bool $active
     *
     * @return DeliveryPlanning
     */
    public function setActive($active)
    {
        $this->active = $active;

        return $this;
    }

    /**
     * Get active.
     *
     * @return bool
     */
    public function getActive()
    {
        return $this->active;
    }

    /**
     * Set timeZone.
     *
     * @param string $timeZone
     *
     * @return DeliveryPlanning
     */
    public function setTimeZone($timeZone)
    {
        $this->timeZone = $timeZone;

        return $this;
    }

    /**
     * Get timeZone.
     *
     * @return string
     */
    public function getTimeZone()
    {
        return $this->timeZone;
    }

    /**
     * Set dateCreate.
     *
     * @param \DateTime|null $dateCreate
     *
     * @return DeliveryPlanning
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
     * @return DeliveryPlanning
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
     * @param \Sogedial\ApiBundle\Entity\Platform $platform
     *
     * @return DeliveryPlanning
     */
    public function setPlatform(\Sogedial\ApiBundle\Entity\Platform $platform)
    {
        $this->platform = $platform;

        return $this;
    }

    /**
     * Get platform.
     *
     * @return \Sogedial\ApiBundle\Entity\Platform
     */
    public function getPlatform()
    {
        return $this->platform;
    }
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->days = new \Doctrine\Common\Collections\ArrayCollection();
        $this->children = new \Doctrine\Common\Collections\ArrayCollection();
        $this->clients = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Add day.
     *
     * @param \Sogedial\ApiBundle\Entity\DeliveryPlanningDay $day
     *
     * @return DeliveryPlanning
     */
    public function addDay(\Sogedial\ApiBundle\Entity\DeliveryPlanningDay $day)
    {
        $this->days[] = $day;

        return $this;
    }

    public function removeDays()
    {
        $this->days = new \Doctrine\Common\Collections\ArrayCollection();

        return $this;
    }

    public function setDays(\Doctrine\Common\Collections\ArrayCollection $days = null)
    {
        $this->days = $days;

        return $this;
    }

    /**
     * Remove day.
     *
     * @param \Sogedial\ApiBundle\Entity\DeliveryPlanningDay $day
     *
     * @return boolean TRUE if this collection contained the specified element, FALSE otherwise.
     */
    public function removeDay(\Sogedial\ApiBundle\Entity\DeliveryPlanningDay $day)
    {
        return $this->days->removeElement($day);
    }

    /**
     * Get days.
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getDays()
    {
        return $this->days;
    }

    /**
     * Add client.
     *
     * @param \Sogedial\ApiBundle\Entity\Client $client
     *
     * @return DeliveryPlanning
     */
    public function addClient(\Sogedial\ApiBundle\Entity\Client $client)
    {
        $this->clients[] = $client;

        return $this;
    }

    /**
     * Remove client.
     *
     * @param \Sogedial\ApiBundle\Entity\Client $client
     *
     * @return boolean TRUE if this collection contained the specified element, FALSE otherwise.
     */
    public function removeClient(\Sogedial\ApiBundle\Entity\Client $client)
    {
        return $this->clients->removeElement($client);
    }

    /**
     * Get clients.
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getClients()
    {
        return $this->clients;
    }

    /**
     * Set temperature.
     *
     * @param string|null $temperature
     *
     * @return DeliveryPlanning
     */
    public function setTemperature($temperature = null)
    {
        $this->temperature = $temperature;

        return $this;
    }

    /**
     * Get temperature.
     *
     * @return string|null
     */
    public function getTemperature()
    {
        return $this->temperature;
    }

    /**
     * Set parent.
     *
     * @param \Sogedial\ApiBundle\Entity\DeliveryPlanning|null $parent
     *
     * @return DeliveryPlanning
     */
    public function setParent(\Sogedial\ApiBundle\Entity\DeliveryPlanning $parent = null)
    {
        $this->parent = $parent;

        return $this;
    }

    /**
     * Get parent.
     *
     * @return \Sogedial\ApiBundle\Entity\DeliveryPlanning|null
     */
    public function getParent()
    {
        return $this->parent;
    }

    /**
     * Add child.
     *
     * @param \Sogedial\ApiBundle\Entity\DeliveryPlanning $child
     *
     * @return DeliveryPlanning
     */
    public function addChild(\Sogedial\ApiBundle\Entity\DeliveryPlanning $child)
    {
        $this->children[] = $child;

        return $this;
    }

    /**
     * Remove child.
     *
     * @param \Sogedial\ApiBundle\Entity\DeliveryPlanning $child
     *
     * @return boolean TRUE if this collection contained the specified element, FALSE otherwise.
     */
    public function removeChild(\Sogedial\ApiBundle\Entity\DeliveryPlanning $child)
    {
        return $this->children->removeElement($child);
    }

    /**
     * Get children.
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getChildren()
    {
        return $this->children;
    }

     /**
     * @return Boolean
     * @serializer\VirtualProperty()
     * @Serializer\Groups({"add", "update","list", "detail"})
     */
    public function isChild()
    {
        return ($this->getParent() == null)?false:true;
        
    }

     /**
     * @return Int
     * @serializer\VirtualProperty()
     * @Serializer\Groups({"add", "update","list", "detail"})
     */
    public function countClients()
    {
        return ($this->getClients())?$this->getClients()->count():0;
        
    }

    /**
     * @return Boolean
     * @serializer\VirtualProperty()
     * @Serializer\Groups({"add", "update","list", "detail"})
     */
    public function getParentId()
    {
        return ($this->getParent())?$this->getParent()->getId():null;
        
    }

    /**
     * @return int
     * @serializer\VirtualProperty()
     * @Serializer\Groups({"add", "update","list", "detail"})
     */
    public function getPlatformId()
    {
        return $this->getPlatform()->getId();
        
    }

    /**
     * Set deliveryMode.
     *
     * @param bool|null $deliveryMode
     *
     * @return DeliveryPlanning
     */
    public function setDeliveryMode($deliveryMode = null)
    {
        $this->deliveryMode = $deliveryMode;

        return $this;
    }

    /**
     * Get deliveryMode.
     *
     * @return bool|null
     */
    public function getDeliveryMode()
    {
        return $this->deliveryMode;
    }
}
