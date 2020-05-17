<?php

namespace Sogedial\ApiBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use JMS\Serializer\Annotation\ExclusionPolicy;
use JMS\Serializer\Annotation\Expose;
use JMS\Serializer\Annotation\VirtualProperty;
use JMS\Serializer\Annotation\Groups;

/**
 * Group Product
 *
 * @ORM\Entity(repositoryClass="Sogedial\ApiBundle\Repository\GroupClientRepository")
 * @ExclusionPolicy("all")
 */
class GroupClient extends Group {

    CONST STATUS_ENSEIGNE = "enseigne";
    CONST STATUS_SELECTION = "selection";

    /**
     * @ORM\ManyToMany(targetEntity="GroupItem", mappedBy="groupClients")
     * @Expose
     * @Groups({"add", "update","list", "detail"})
     */
    private $groupItems;

    /**
     * @ORM\ManyToMany(targetEntity="Sogedial\ApiBundle\Entity\Client", mappedBy="groupClients")
     *
     */
    private $clients;
    
    /**
     * @ORM\ManyToOne(targetEntity="Platform")
     * @ORM\JoinColumn(name="platform_id", referencedColumnName="id")
     */
    private $platform;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Expose
     * @Groups({"add", "update","list", "detail"})
     */
    protected $status = GroupClient::STATUS_ENSEIGNE;

    /**
     * @ORM\ManyToOne(targetEntity="Brand")
     * @Groups({"add", "update","list", "detail", "search"}) 
     */
    private $brand;


    /**
     * Constructor
     */
    public function __construct() {
        $this->clients = new \Doctrine\Common\Collections\ArrayCollection();
        $this->groupItems = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Add client.
     *
     * @param \Sogedial\ApiBundle\Entity\Client $client
     *
     * @return GroupClient
     */
    public function addClient(\Sogedial\ApiBundle\Entity\Client $client) {
        $this->clients[] = $client;

        return $this;
    }

    /**
     * Add client.
     *
     * @param \Sogedial\ApiBundle\Entity\Client $client
     *
     * @return GroupClient
     */
    public function setClients($clients) {
        $this->clients = $clients;

        return $this;
    }
    

    /**
     * Remove client.
     *
     * @param \Sogedial\ApiBundle\Entity\Client $client
     *
     * @return boolean TRUE if this collection contained the specified element, FALSE otherwise.
     */
    public function removeClient(\Sogedial\ApiBundle\Entity\Client $client) {
        return $this->clients->removeElement($client);
    }
    
    /**
     * Remove all clients.
     *     
     */
    public function removeClients() {
        foreach($this->clients as $client){
            $client->removeGroupClient($this);
            $this->removeClient($client);
        }
        return $this;
    }

    /**
     * Get clients.
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getClients() {
        return $this->clients;
    }

     /**
     * @VirtualProperty()
     * @return bool
     */
    public function countClients() {
        return $this->getClients()->count();
    }


    /**
     * @return mixed
     */
    public function getStatus() {
        return $this->status;
    }

    /**
     * @param mixed $status
     */
    public function setStatus($status) {
        $this->status = $status;
    }


    public function __clone() {
        if ($this->id) {
            $this->groupItems = new ArrayCollection();
            $this->clients = new ArrayCollection();
            $this->setSlug(null);
            $this->setDateCreate(null);
            $this->setDateUpdate(null);
        }

        return $this;
    }


    /**
     * Add groupItem.
     *
     * @param \Sogedial\ApiBundle\Entity\GroupItem $groupItem
     *
     * @return GroupClient
     */
    public function addGroupItem(\Sogedial\ApiBundle\Entity\GroupItem $groupItem) {
        $this->groupItems[] = $groupItem;

        return $this;
    }

    /**
     * Remove groupItem.
     *
     * @param \Sogedial\ApiBundle\Entity\GroupItem $groupItem
     *
     * @return boolean TRUE if this collection contained the specified element, FALSE otherwise.
     */
    public function removeGroupItem(\Sogedial\ApiBundle\Entity\GroupItem $groupItem) {
        return $this->groupItems->removeElement($groupItem);
    }

    public function removeGroupItems() {
        foreach($this->groupItems as $groupItem){
            $this->removeGroupItem($groupItem);
        }

        return $this;        
    }

    /**
     * Get groupItems.
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getGroupItems( $status = '', $dateRestriction = false ) {
        return $this->groupItems->filter(function( $groupItem ) use ($status, $dateRestriction) {            
            $isStatusValid = ( $status == '' || $groupItem->getStatus() == $status);
            $isDateValid = ( !$dateRestriction || $groupItem->getDateEnd() >= new \DateTime() || $groupItem->getDateEnd() == null);
            return $isStatusValid && $isDateValid;
        });
    }

    /**
     * Set platform.
     *
     * @param \Sogedial\ApiBundle\Entity\Platform|null $platform
     *
     * @return GroupClient
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
     * Set brand.
     *
     * @param \Sogedial\ApiBundle\Entity\Brand|null $brand
     *
     * @return GroupClient
     */
    public function setBrand(\Sogedial\ApiBundle\Entity\Brand $brand = null)
    {
        $this->brand = $brand;

        return $this;
    }

    /**
     * Get brand.
     *
     * @return \Sogedial\ApiBundle\Entity\Brand|null
     */
    public function getBrand()
    {
        return $this->brand;
    }
    
    /**
     * @VirtualProperty()
     * @Groups({"add", "update","list", "detail"}) 
     * @return string
     */
    public function getBrandName() {
        return ($this->getBrand())?$this->getBrand()->getName():null;
    }

    /**
     * @VirtualProperty()
     * @Groups({"add", "update","list", "detail"})
     * @return string
     */
    public function getBrandId() {
        return ($this->getBrand())?$this->getBrand()->getId():null;
    }

}
