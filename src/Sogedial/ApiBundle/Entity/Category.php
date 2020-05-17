<?php

namespace Sogedial\ApiBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use JMS\Serializer\Annotation as Serializer;

/**
 * Category
 *
 * @ORM\Table(name="category")
 * @ORM\Entity(repositoryClass="Sogedial\ApiBundle\Repository\CategoryRepository")
 * @Serializer\ExclusionPolicy("all")
 */
class Category extends AbstractEntity {

    /**
     * @ORM\ManyToOne(targetEntity="Category", inversedBy="children")
     * @ORM\JoinColumn(name="parent_id", referencedColumnName="id")
     * @Serializer\Groups({"add", "update"})
     * @Serializer\MaxDepth(1)
     * @Serializer\Expose
     */
    private $parent;

    /**
     * @ORM\OneToMany(targetEntity="Category", mappedBy="parent")
     * @Serializer\Groups({"list", "detail", "listitems"})
     * @Serializer\MaxDepth(1)
     * @Serializer\Expose
     */
    private $children;

    /**
     * @ORM\ManyToMany(targetEntity="Sogedial\ApiBundle\Entity\Item", mappedBy="categories", fetch="EXTRA_LAZY")          
     */
    private $items;

    /**
     * @var string
     *
     * @ORM\Column(name="type", type="string", length=255)
     * @Serializer\Groups({"add", "update","list", "detail", "listitems"})
     * @Serializer\Expose
     */
    private $type;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=255)
     * @Serializer\Groups({"add", "update","list", "detail", "listitems", "search" })
     * @Serializer\Expose
     */
    private $name;

    /**
     * @var string
     * @Gedmo\Slug(fields={"code","type","name"}, unique=true)
     * @ORM\Column(name="slug", type="string", length=255)
     * @Serializer\Groups({"add", "update","list", "detail", "listitems"})
     * @Serializer\Expose
     */
    private $slug;

    /**
     * @var string|null
     *
     * @ORM\Column(name="code", type="string", length=255, unique=true)
     */
    private $code;

    /**
     * @var string|null
     *
     * @ORM\Column(name="path", type="string", length=255, unique=false)
     */
    private $path;

    /**
     * @var bool
     *
     * @ORM\Column(name="is_root", type="boolean")
     * @Serializer\Expose
     */
    private $isRoot = false;

    /**
     * @var string|null
     *
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $temperature;


    /**
     * Constructor
     */
    public function __construct() {
        $this->isRoot = FALSE;
        $this->children = new \Doctrine\Common\Collections\ArrayCollection();
        $this->items = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Set type.
     *
     * @param string $type
     *
     * @return Category
     */
    public function setType($type) {
        $this->type = $type;

        return $this;
    }

    /**
     * Get type.
     *
     * @return string
     */
    public function getType() {
        return $this->type;
    }

    /**
     * Set name.
     *
     * @param string $name
     *
     * @return Category
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
     * @return Category
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
     * @return Category
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
     * @return Category
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
     * @return Category
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
     * Set parent.
     *
     * @param \Sogedial\ApiBundle\Entity\Category|null $parent
     *
     * @return Category
     */
    public function setParent(\Sogedial\ApiBundle\Entity\Category $parent = null) {
        $this->parent = $parent;

        return $this;
    }

    /**
     * Get parent.
     *
     * @return \Sogedial\ApiBundle\Entity\Category|null
     */
    public function getParent() {
        return $this->parent;
    }

    /**
     * Add child
     *
     * @param \Sogedial\ApiBundle\Entity\Category $child
     *
     * @return Category
     */
    public function addChild(\Sogedial\ApiBundle\Entity\Category $child) {
        $this->children[] = $child;

        return $this;
    }

    /**
     * Remove child
     *
     * @param \Sogedial\ApiBundle\Entity\Category $child
     */
    public function removeChild(\Sogedial\ApiBundle\Entity\Category $child) {
        $this->children->removeElement($child);
    }

    /**
     * Get children
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getChildren() {
        return $this->children;
    }

    /**
     * Has children
     *
     * @return Boolean
     */    
    public function hasChildren(){
        return !$this->children->isEmpty();
    }

    /**
     * Set isRoot.
     *
     * @param bool $isRoot
     *
     * @return Category
     */
    public function setIsRoot($isRoot)
    {
        $this->isRoot = $isRoot;

        return $this;
    }

    /**
     * Get isRoot.
     *
     * @return bool
     */
    public function getIsRoot()
    {
        return $this->isRoot;
    }

    /**
     * Set temperature.
     *
     * @param string|null $temperature
     *
     * @return Category
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
     * Set items.
     *
     * @param \Sogedial\ApiBundle\Entity\Item $item
     *
     * @return Category
     */
    public function setItems($items)
    {
        $this->items = $items;

        return $this;
    }

    /**
     * Add item.
     *
     * @param \Sogedial\ApiBundle\Entity\Item $item
     *
     * @return Category
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
     * @Serializer\VirtualProperty()
     * @Serializer\Groups({"listitems"})
     */
    public function getItems($onlyActive = true) {
        return (!$onlyActive)?$this->items:$this->items->filter(function($item){
            return $item->getActive();
        });
    }
    /**
     * Count items
     * @return Int
     * @Serializer\VirtualProperty()
     * @Serializer\Groups({"add", "update","list", "detail", "listitems"})
     */
    public function getCountItems(){        
        return $this->getItems()->count();
    }

    /**
     * get all children
     * @return Category
     */
    public function getAllChildren($allChildren = []){  
        $allChildren = array_merge($this->getChildren()->toArray(), $allChildren);
        foreach($this->getChildren() as $child){
            if($child->hasChildren()){
                $allChildren = $child->getAllChildren($allChildren);
            }
        }
        return $allChildren;
    }

    /**
     * Set path.
     *
     * @param string $path
     *
     * @return Category
     */
    public function setPath($path)
    {
        $this->path = $path;

        return $this;
    }

    /**
     * Get path.
     *
     * @return string
     */
    public function getPath()
    {
        return $this->path;
    }

    /**
     * Get parent Id extracted from path
     * 
     */
    
    function getParentIdFromPath(){

        $path = $this->getPath();

        $p = explode('-',$path);

        return $p[count($p)-1];
    }
}
