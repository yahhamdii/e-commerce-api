<?php

namespace Sogedial\ApiBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation\ExclusionPolicy;
use JMS\Serializer\Annotation\Expose;
use JMS\Serializer\Annotation as Serializer;
use JMS\Serializer\Annotation\VirtualProperty;
use JMS\Serializer\Annotation\Groups;

/**
 * Group Item
 *
 * @ORM\Entity(repositoryClass="Sogedial\ApiBundle\Repository\GroupItemRepository")
 * @ExclusionPolicy("all")
 */
class GroupItem extends Group {

    CONST STATUS_PREORDER = "preorder";
    CONST STATUS_CATALOG = "catalog";
    CONST STATUS_SELECTION = "selection";

    /**
     * @ORM\ManyToMany(targetEntity="Item", inversedBy="groupItems")     
     */
    private $items;

    /**
     * @ORM\ManyToMany(targetEntity="GroupClient", inversedBy="groupItems")     
     */
    private $groupClients;

    /**
     * @ORM\ManyToOne(targetEntity="Client", inversedBy="groupItems")
     * @ORM\JoinColumn(name="client_id", referencedColumnName="id")
     */
    private $client;

    /**
     * @ORM\ManyToOne(targetEntity="Platform")
     * @ORM\JoinColumn(name="platform_id", referencedColumnName="id")
     */
    private $platform;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Groups({"add", "update","list", "detail", "listitems"})
     * @Expose
     */
    protected $status = GroupItem::STATUS_CATALOG;

    /**
     * @ORM\ManyToOne(targetEntity="Brand")
     */
    private $brand;

    /**     
     * Array of (array) categories
     */
    private $categories;


    /**
     * @var \DateTime
     *
     * @ORM\Column(name="date_end", type="datetime", nullable = true)
     * @Groups({"add", "update","list", "detail", "listitems"})
     * @Serializer\Type("DateTime<'Y-m-d'>")
     * @Serializer\Expose
     */
    private $dateEnd;


    /**     
     * @ORM\Column(type="boolean", nullable= true, options={"default": true })
     * @Groups({"add", "update","list", "detail", "listitems"})
     * @Serializer\Expose
     */
    protected $enabled = true;





     /**
     * Constructor
     */
    public function __construct() {
        $this->items = new \Doctrine\Common\Collections\ArrayCollection();
        $this->groupClients = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Set platform.
     *
     * @param \Sogedial\ApiBundle\Entity\Platform|null $platform
     *
     * @return GroupItem
     */
    public function setPlatform(\Sogedial\ApiBundle\Entity\Platform $platform = null) {
        $this->platform = $platform;

        return $this;
    }

    /**
     * Get platform.
     *
     * @return \Sogedial\ApiBundle\Entity\Platform|null
     */
    public function getPlatform() {
        return $this->platform;
    }

    /**
     * Add groupClient.
     *
     * @param \Sogedial\ApiBundle\Entity\GroupClient $groupClient
     *
     * @return GroupItem
     */
    public function addGroupClient(\Sogedial\ApiBundle\Entity\GroupClient $groupClient) {
        $this->groupClients[] = $groupClient;

        return $this;
    }

    /**
     * Remove groupClient.
     *
     * @param \Sogedial\ApiBundle\Entity\GroupClient $groupClient
     *
     * @return boolean TRUE if this collection contained the specified element, FALSE otherwise.
     */
    public function removeGroupClient(\Sogedial\ApiBundle\Entity\GroupClient $groupClient) {        
        return $this->groupClients->removeElement($groupClient);
    }

     /**
     * Remove groupClients.
     *   
     */
    public function removeGroupClients() {
        foreach($this->groupClients as $groupClient){
            $this->removeGroupClient($groupClient);
        }        
        return $this;
    }

    /**
     * Get groupClients.
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getGroupClients() {
        return $this->groupClients;
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

    /**
     * @VirtualProperty()
     * @Groups({"add", "update","list", "detail", "listitems"})
     * @return bool
     */
    public function getIsPreorder() {
        if ($this->status == self::STATUS_PREORDER) {
            return true;
        }
        return false;
    }

    /**
     * @VirtualProperty()
     * @Groups({"add", "update","list", "detail", "listitems"})
     * @return bool
     */
    public function countItems() {
        return $this->getItems()->count();
    }

    /**
     * @VirtualProperty()
     * @Groups({"add", "update","list", "detail", "listitems"})
     * @return string
     */
    public function getBrandName() {
        return ($this->getBrand())?$this->getBrand()->getName():null;
    }

    /**
     * @VirtualProperty()
     * @Groups({"add", "update","list", "detail", "listitems"})
     * @return string
     */
    public function getBrandId() {
        return ($this->getBrand())?$this->getBrand()->getId():null;
    }

    /**
     * Set client.
     *
     * @param \Sogedial\ApiBundle\Entity\Client|null $client
     *
     * @return GroupItem
     */
    public function setClient(\Sogedial\ApiBundle\Entity\Client $client = null) {
        $this->client = $client;

        return $this;
    }

    /**
     * Get client.
     *
     * @return \Sogedial\ApiBundle\Entity\Client|null
     */
    public function getClient() {
        return $this->client;
    }

    /**
     * Add item.
     *
     * @param \Sogedial\ApiBundle\Entity\Item $item
     *
     * @return GroupItem
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
     * Remove items.
     *     
     *     
     */
    public function removeItems() {
        foreach($this->items as $item){
            $this->removeItem($item);
        }
        return $this;        
    }

     /**
     * Set items.
     *     
     *     
     */
    public function setItems($items) {
        $this->items = $items;
        return $this;
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
     * Get items Ids.
     * @VirtualProperty()     
     * @Groups({"detail"})
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getItemsIds() {        
        $ids = $this->getItems()->map(function($item){
            return $item->getId();
        });
        
        return $ids;
    }


     /**
     * Set categories.
     *
     */
    public function setCategories($categories) {
        $this->categories = $categories;
        return $this;
    }

    /**
     * Get categories.
     * @VirtualProperty()     
     * @Groups({"list", "listitems"})
     * @Serializer\Expose
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getCategories() {
        return $this->categories;
    }

    /**
     * Set brand.
     *
     * @param \Sogedial\ApiBundle\Entity\Brand|null $brand
     *
     * @return GroupItem
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
     * Set dateEnd.
     *
     * @param \DateTime|null $dateEnd
     *
     * @return GroupItem
     */
    public function setDateEnd($dateEnd = null)
    {
        $this->dateEnd = $dateEnd;

        return $this;
    }

    /**
     * Get dateEnd.
     *
     * @return \DateTime|null
     */
    public function getDateEnd()
    {
        return $this->dateEnd;
    }

    /**
     * Set enabled.
     *
     * @param bool $enabled
     *
     * @return GroupItem
     */
    public function setEnabled($enabled)
    {
        $this->enabled = $enabled;

        return $this;
    }

    /**
     * Get enabled.
     *
     * @return bool
     */
    public function getEnabled()
    {
        return $this->enabled;
    }


}
