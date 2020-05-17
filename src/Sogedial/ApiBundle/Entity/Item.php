<?php

namespace Sogedial\ApiBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as Serializer;

/**
 * Item
 *
 * @ORM\Table(name="item")
 * @ORM\Entity(repositoryClass="Sogedial\ApiBundle\Repository\ItemRepository")
 * @Serializer\ExclusionPolicy("all")
 */
class Item extends AbstractEntity {

    const TEMPERATURE_DRY = "SEC";
    const TEMPERATURE_FRESH = "POSITIF";
    const TEMPERATURE_FROZEN = "FROID";
    const TEMPERATURE_UNDEFINED = "UNDEFINED";

    /**
     * Precommand parameters
     */
    CONST PRECMD_CODE_NATURE = "ECLAT/PRE CDE";
    CONST PRECMD_FREQUENCY = 194;

    /**
     * @var string
     *
     * @ORM\Column(name="code", type="string", length=255, unique=true)
     * @Serializer\Expose
     * @Serializer\Groups({"add", "update","list", "detail", "listitems", "search"})
     */
    private $code;

    /**
     * @var string
     *
     * @ORM\Column(name="code_nature", type="string", length=50, nullable=true)
     * @Serializer\Expose
     * @Serializer\Groups({"add", "update","list", "detail", "listitems"})
     */
    private $codeNature;

    /**
     * @var string
     *
     * @ORM\Column(name="frequency", type="string", length=50, nullable=true)
     * @Serializer\Expose
     * @Serializer\Groups({"add", "update","list", "detail", "listitems"})
     */
    private $frequency;

    /**
     * @var string
     *
     * @ORM\Column(type="decimal", precision=20, scale=6, nullable=true)
     * @Serializer\Expose
     * @Serializer\Groups({"add", "update","list", "detail", "listitems", "search"})
     */
    private $price;

    /**
     * @var string
     *
     * @ORM\Column(name="price_vat", type="decimal", precision=20, scale=6, nullable=true)
     * @Serializer\Expose
     * @Serializer\Groups({"add", "update","list", "detail", "listitems", "search"})
     */
    private $priceVat;

    /**
     * @var string
     *
     * @ORM\Column(name="vat", type="decimal", precision=8, scale=2, nullable=true)
     * @Serializer\Expose
     * @Serializer\Groups({"add", "update","list", "detail", "listitems"})
     */
    private $vat;

    /**
     * @var string
     *
     * @ORM\Column(name="reference", type="string", length=255, nullable=true)
     * @Serializer\Expose
     * @Serializer\Groups({"add", "update","list", "detail", "listitems", "moq", "search"})
     */
    private $reference;

    /**
     * @var string
     *
     * @ORM\Column(name="type", type="string", length=255, nullable=true)
     * @Serializer\Expose
     * @Serializer\Groups({"add", "update","list", "detail", "listitems", "moq", "search"})
     */
    private $type;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=255, nullable=true)
     * @Serializer\Expose
     * @Serializer\Groups({"add", "update","list", "detail", "listitems", "moq", "search"})
     */
    private $name;

    /**
     * @var string
     *
     * @ORM\Column(name="ean13", type="string", length=255, nullable=true)
     * @Serializer\Expose
     * @Serializer\Groups({"add", "update","list", "detail", "listitems", "moq", "search"})
     */
    private $ean13;

    /**
     * @var string
     *
     * @ORM\Column(name="weight", type="string", length=255, nullable=true)
     * @Serializer\Expose
     * @Serializer\Groups({"add", "update","list", "detail", "listitems", "search"})
     */
    private $weight;

    /**
     * @var int
     *
     * @ORM\Column(name="pcb", type="float", nullable=true)
     * @Serializer\Expose
     * @Serializer\Groups({"add", "update","list", "detail", "listitems", "moq", "search"})
     */
    private $pcb;

    /**
     * @var string
     *
     * @ORM\Column(name="unity", type="string", length=255, nullable=true)
     * @Serializer\Expose
     * @Serializer\Groups({"add", "update","list", "detail", "listitems", "moq", "search"})
     */
    private $unity;

    /**
     * @var string
     *
     * @ORM\Column(name="upc", type="string", length=255, nullable=true)
     * @Serializer\Expose
     * @Serializer\Groups({"add", "update","list", "detail", "listitems", "search"})
     */
    private $upc;

    /**
     * @var string
     *
     * @ORM\Column(name="variable_weight", type="boolean", nullable=true, options={"default" : false})
     * @Serializer\Expose
     * @Serializer\Groups({"add", "update","list", "detail", "search"})
     */
    private $variableWeight;
    

    /**
     * @var bool
     *
     * @ORM\Column(name="active", type="boolean")
     * @Serializer\Expose
     * @Serializer\Groups({"add", "update","list", "detail", "listitems", "search"})
     */
    private $active;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="date_begin", type="datetime", nullable=true)
     * @Serializer\Expose
     * @Serializer\Groups({"add", "update","list", "detail", "listitems", "search"})
     */
    private $dateBegin;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="date_end", type="datetime", nullable=true)
     * @Serializer\Expose
     * @Serializer\Groups({"add", "update","list", "detail", "listitems", "search"})
     */
    private $dateEnd;

    /**
     * @ORM\ManyToOne(targetEntity="Product", inversedBy="items")
     * @ORM\JoinColumn(name="product_id", referencedColumnName="id")
     * @Serializer\Expose
     * @Serializer\Groups({"add", "update","list", "detail"})
     */
    private $product;

    /**
     * @ORM\ManyToOne(targetEntity="Package", inversedBy="items")
     * @ORM\JoinColumn(name="package_id", referencedColumnName="id")
     * @Serializer\Expose
     * @Serializer\Groups({"add", "update","list", "detail", "search"})
     */
    private $package;

    /**
     * @ORM\ManyToOne(targetEntity="Stock", inversedBy="items")
     * @ORM\JoinColumn(name="stock_id", referencedColumnName="id")     
     */
    private $stock;

    /**
     * @ORM\OneToMany(targetEntity="Degressive", mappedBy="item")
     * @Serializer\Expose
     * @Serializer\Groups({"add", "update","list", "detail"})
     */
    private $degressives;

    /**
     * @ORM\ManyToMany(targetEntity="Promotion", mappedBy="items")
     */
    private $promotions;

    /**
     * @ORM\ManyToOne(targetEntity="DeliveryMode")
     * @Serializer\Expose
     * @Serializer\Groups({"add", "update","list", "detail", "search"})
     */
    private $deliveryMode;

    /**
     * @ORM\ManyToOne(targetEntity="GroupClient")
     * @Serializer\Expose
     * @Serializer\Groups({"add", "update","list", "detail"})
     */
    private $groupClient;

    /**
     * @ORM\ManyToMany(targetEntity="Attribut")
     * @Serializer\Expose
     * @Serializer\Groups({"add", "update","list", "detail", "search"})
     */
    private $attributs;

    /**
     * @ORM\ManyToOne(targetEntity="Supplier")
     * @ORM\JoinColumn(name="supplier_id", referencedColumnName="id")
     * @Serializer\Expose
     * @Serializer\Groups({"add", "update","list", "detail", "search"})
     */
    private $supplier;

    /**
     *
     * @ORM\ManyToMany(targetEntity="Category", inversedBy="items")
     * @Serializer\Expose
     * @Serializer\MaxDepth(2)
     * @Serializer\Groups({"add", "update","list", "detail", "search"})
     */
    private $categories;

    /**
     * @ORM\ManyToOne(targetEntity="Platform")
     * @ORM\JoinColumn(name="platform_id", referencedColumnName="id")
     *
     */
    private $platform;

    /**
     * @ORM\ManyToMany(targetEntity="GroupItem", mappedBy="items")
     *
     */
    private $groupItems;

    /**
     * @ORM\OneToMany(targetEntity="Sogedial\ApiBundle\Entity\OrderItem", mappedBy="item")
     * @ORM\JoinColumn(nullable=true)
     */
    private $orderItems;
    
    /**
     * @var int
     *
     * @ORM\Column(name="moq", type="integer", nullable= true)
     * @Serializer\Expose
     * @Serializer\Groups({"add", "update","list", "detail", "listitems", "moq"})
     */
    private $moq;

    /**
     * @var bool
     *
     * @ORM\Column(name="is_preorder", type="boolean", nullable=true)
     * @Serializer\Expose
     * @Serializer\Groups({"add", "update","list", "detail", "listitems", "moq", "search"})
     */
    private $isPreorder;

    public function __construct() {
        $this->categories = new \Doctrine\Common\Collections\ArrayCollection();
        $this->groupItems = new \Doctrine\Common\Collections\ArrayCollection();
        $this->orderItems = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Set priceVat.
     *
     * @param string $priceVat
     *
     * @return Item
     */
    public function setPriceVat($priceVat) {
        $this->priceVat = $priceVat;

        return $this;
    }

    /**
     * Get priceVat.
     *
     * @return string
     */
    public function getPriceVat( $qty = 1 ) {
        
        $vat = $this->getVat();

        $price = $this->getPrice( $qty );

        $priceVat = $price + $price *  $vat / 100;

        return $priceVat;
    }

    /**
     * @Serializer\VirtualProperty()
     * @Serializer\Groups({"add", "update","list", "detail","moq", "search"})
     */
    public function getApplicablePriceVat( $qty = 1 ){

        $promotion = $this->getPromotion();

        if($promotion){

            return $this->getPromotion()->getPriceVat( $this->getVat(), $qty );
            
        }
        
        return $this->getPriceVat( $qty );
    }

    /**
     * Set vat.
     *
     * @param string $vat
     *
     * @return Item
     */
    public function setVat($vat) {
        $this->vat = $vat;

        return $this;
    }

    /**
     * Get vat.
     *
     * @return string
     */
    public function getVat() {
        return $this->vat;
    }

    /**
     * Set reference.
     *
     * @param string $reference
     *
     * @return Item
     */
    public function setReference($reference) {
        $this->reference = $reference;

        return $this;
    }

    /**
     * Get reference.
     *
     * @return string
     */
    public function getReference() {
        return $this->reference;
    }

    /**
     * Set type.
     *
     * @param string $type
     *
     * @return Item
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
     * @return Item
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
     * Set ean13.
     *
     * @param string $ean13
     *
     * @return Item
     */
    public function setEan13($ean13) {
        $this->ean13 = $ean13;

        return $this;
    }

    /**
     * Get ean13.
     *
     * @return string
     */
    public function getEan13() {
        return $this->ean13;
    }

    /**
     * Set weight.
     *
     * @param string $weight
     *
     * @return Item
     */
    public function setWeight($weight) {
        $this->weight = $weight;

        return $this;
    }

    /**
     * Get weight.
     *
     * @return string
     */
    public function getWeight() {
        return $this->weight;
    }

    /**
     * Set pcb.
     *
     * @param int $pcb
     *
     * @return Item
     */
    public function setPcb($pcb) {
        $this->pcb = $pcb;

        return $this;
    }

    /**
     * Get pcb.
     *
     * @return int
     */
    public function getPcb() {
        return $this->pcb;
    }

    /**
     * Set unity.
     *
     * @param string $unity
     *
     * @return Item
     */
    public function setUnity($unity) {
        $this->unity = $unity;

        return $this;
    }

    /**
     * Get unity.
     *
     * @return string
     */
    public function getUnity() {
        return $this->unity;
    }

    /**
     * Set upc.
     *
     * @param string $upc
     *
     * @return Item
     */
    public function setUpc($upc) {
        $this->upc = $upc;

        return $this;
    }

    /**
     * Get upc.
     *
     * @return string
     */
    public function getUpc() {
        return $this->upc;
    }

    /**
     * Set active.
     *
     * @param bool $active
     *
     * @return Item
     */
    public function setActive($active) {
        $this->active = $active;

        return $this;
    }

    /**
     * Get active.
     *
     * @return bool
     */
    public function getActive() {
        return $this->active;
    }

    /**
     * Set dateBegin.
     *
     * @param \DateTime $dateBegin
     *
     * @return Item
     */
    public function setDateBegin($dateBegin) {
        $this->dateBegin = $dateBegin;

        return $this;
    }

    /**
     * Get dateBegin.
     *
     * @return \DateTime
     */
    public function getDateBegin() {
        return $this->dateBegin;
    }

    /**
     * Set dateEnd.
     *
     * @param \DateTime $dateEnd
     *
     * @return Item
     */
    public function setDateEnd($dateEnd) {
        $this->dateEnd = $dateEnd;

        return $this;
    }

    /**
     * Get dateEnd.
     *
     * @return \DateTime
     */
    public function getDateEnd() {
        return $this->dateEnd;
    }

    /**
     * Set product.
     *
     * @param \Sogedial\ApiBundle\Entity\Product|null $product
     *
     * @return Item
     */
    public function setProduct(\Sogedial\ApiBundle\Entity\Product $product = null) {
        $this->product = $product;

        return $this;
    }

    /**
     * Get product.
     *
     * @return \Sogedial\ApiBundle\Entity\Product|null
     */
    public function getProduct() {
        return $this->product;
    }

    /**
     * Set stock.
     *
     * @param \Sogedial\ApiBundle\Entity\Stock|null $stock
     *
     * @return Item
     */
    public function setStock(\Sogedial\ApiBundle\Entity\Stock $stock = null) {
        $this->stock = $stock;

        return $this;
    }
    
    /**
     * Get Stock
     *
     * @Serializer\VirtualProperty()
     * @Serializer\Expose
     * @Serializer\Groups({"add", "update","list", "detail", "search"})
     */
    public function getStock() {        
        if($this->getShouldReplaceRegularStock()){  
            return $this->getPromotion()->getStock();
        }        
        return $this->stock;        
    }

     /**
     * Get Real Stock : get stock without promotion overring
     *      
     * @return boolean
     */
    public function getInitialStock() {          
        return $this->stock;
    }

    /**
     * Add degressive.
     *
     * @param \Sogedial\ApiBundle\Entity\Degressive $degressive
     *
     * @return Item
     */
    public function addDegressive(\Sogedial\ApiBundle\Entity\Degressive $degressive) {
        $this->degressives[] = $degressive;

        return $this;
    }

    /**
     * Remove degressive.
     *
     * @param \Sogedial\ApiBundle\Entity\Degressive $degressive
     *
     * @return boolean TRUE if this collection contained the specified element, FALSE otherwise.
     */
    public function removeDegressive(\Sogedial\ApiBundle\Entity\Degressive $degressive) {
        return $this->degressives->removeElement($degressive);
    }

    /**
     * Get degressives.
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getDegressives() {
        return $this->degressives;
    }

    /**
     * Set dateCreate.
     *
     * @param \DateTime|null $dateCreate
     *
     * @return Item
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
     * @return Item
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
     * Set deliveryMode.
     *
     * @param \Sogedial\ApiBundle\Entity\DeliveryMode|null $deliveryMode
     *
     * @return Item
     */
    public function setDeliveryMode(\Sogedial\ApiBundle\Entity\DeliveryMode $deliveryMode = null) {
        $this->deliveryMode = $deliveryMode;

        return $this;
    }

    /**
     * Get deliveryMode.
     *
     * @return \Sogedial\ApiBundle\Entity\DeliveryMode|null
     */
    public function getDeliveryMode() {
        return $this->deliveryMode;
    }

    /**
     * Add attribut.
     *
     * @param \Sogedial\ApiBundle\Entity\Attribut $attribut
     *
     * @return Item
     */
    public function addAttribut(\Sogedial\ApiBundle\Entity\Attribut $attribut) {
        $this->attributs[] = $attribut;

        return $this;
    }

    /**
     * Remove attribut.
     *
     * @param \Sogedial\ApiBundle\Entity\Attribut $attribut
     *
     * @return boolean TRUE if this collection contained the specified element, FALSE otherwise.
     */
    public function removeAttribut(\Sogedial\ApiBundle\Entity\Attribut $attribut) {
        return $this->attributs->removeElement($attribut);
    }

    /**
     * Get attributs.
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getAttributs() {
        return $this->attributs;
    }

    /**
     * Set price.
     *
     * @param string $price
     *
     * @return Item
     */
    public function setPrice($price) {
        $this->price = $price;

        return $this;
    }

    /**
     * Get price.
     *
     * @return string
     */
    public function getPrice($qty = 1) {

        return $this->price * $qty;

    }

     /**
     * @Serializer\VirtualProperty()
     * @Serializer\Groups({"add", "update","list", "detail","moq", "search"})
     */
    public function getApplicablePrice( $qty = 1 ){

        $promotion = $this->getPromotion();

        if($promotion){

            return $this->getPromotion()->getPrice( $qty );

        }

        return $this->getPrice($qty);
    }

    /**
     * Set code.
     *
     * @param string $code
     *
     * @return Item
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
     * Add category.
     *
     * @param \Sogedial\ApiBundle\Entity\Category $category
     *
     * @return Item
     */
    public function addCategory(\Sogedial\ApiBundle\Entity\Category $category) {
        $this->categories[] = $category;

        return $this;
    }

    /**
     * Remove category.
     *
     * @param \Sogedial\ApiBundle\Entity\Category $category
     *
     * @return boolean TRUE if this collection contained the specified element, FALSE otherwise.
     */
    public function removeCategory(\Sogedial\ApiBundle\Entity\Category $category) {
        return $this->categories->removeElement($category);
    }

    /**
     * Get categories.
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getCategories() {
        return $this->categories;
    }

    /**
     * Set platform.
     *
     * @param \Sogedial\ApiBundle\Entity\Platform|null $platform
     *
     * @return Item
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
     * Add degressife.
     *
     * @param \Sogedial\ApiBundle\Entity\Degressive $degressife
     *
     * @return Item
     */
    public function addDegressife(\Sogedial\ApiBundle\Entity\Degressive $degressife) {
        $this->degressives[] = $degressife;

        return $this;
    }

    /**
     * Remove degressife.
     *
     * @param \Sogedial\ApiBundle\Entity\Degressive $degressife
     *
     * @return boolean TRUE if this collection contained the specified element, FALSE otherwise.
     */
    public function removeDegressife(\Sogedial\ApiBundle\Entity\Degressive $degressife) {
        return $this->degressives->removeElement($degressife);
    }

    /**
     * Set groupClient.
     *
     * @param \Sogedial\ApiBundle\Entity\GroupClient|null $groupClient
     *
     * @return Item
     */
    public function setGroupClient(\Sogedial\ApiBundle\Entity\GroupClient $groupClient = null) {
        $this->groupClient = $groupClient;

        return $this;
    }

    /**
     * Get groupClient.
     *
     * @return \Sogedial\ApiBundle\Entity\GroupClient|null
     */
    public function getGroupClient() {
        return $this->groupClient;
    }

    /**
     * Set package.
     *
     * @param \Sogedial\ApiBundle\Entity\Package|null $package
     *
     * @return Item
     */
    public function setPackage(\Sogedial\ApiBundle\Entity\Package $package = null) {
        $this->package = $package;

        return $this;
    }

    /**
     * Get package.
     *
     * @return \Sogedial\ApiBundle\Entity\Package|null
     */
    public function getPackage() {
        return $this->package;
    }

    /**
     * @Serializer\VirtualProperty()
     * @Serializer\Groups({"add", "update","list", "detail","moq", "search"})
     */
    public function getImages() {

        $images = array(
            'small' => '//photos.groupesafo.com/wsse/cb8f190153190ebe8598ac49e86ebf37/75/75/' . $this->getProduct()->getEan(),
            'large' => '//photos.groupesafo.com/wsse/cb8f190153190ebe8598ac49e86ebf37/320/220/' . $this->getProduct()->getEan(),
        );

        return $images;
    }

     /**
     * @Serializer\VirtualProperty()
     * @Serializer\Groups({"detail", "list"})
     */
    public function getDocument() {
        return  '//photos.groupesafo.com/wsse/cb8f190153190ebe8598ac49e86ebf37/pdf/' . $this->getProduct()->getEan();
    }

    /**
     * @Serializer\VirtualProperty()
     * @Serializer\Groups({"add", "update","list", "detail", "moq", "search"})
     */
    public function getIsNew() {

        $limit = 90;

        $platform = $this->getPlatform();
        $attribut = $platform->getAttributByKey(Attribut::KEY_NEW);
        if ($attribut) {
            if ($attribut->getValue() == 0)
                $limit = 90;
            if ($attribut->getValue() == 1)
                $limit = 60;
        }

        $dateEntryStockLimit = date("Y-m-d H:i:s", time() - 60 * 60 * 24 * $limit);
        $currentDate = date("Y-m-d H:i:s");
        $firstDateEntryInStock = $this->getStock()->getFirstDateEntryInStock();
        if(is_null($firstDateEntryInStock)){
            return true;
        }else{
            $firstDateEntryInStock = $firstDateEntryInStock->format("Y-m-d H:i:s");
        }

        if ($firstDateEntryInStock >= $dateEntryStockLimit && $firstDateEntryInStock <= $currentDate) {
            return true;
        }

        return false;
    }

    /**
     * @Serializer\VirtualProperty()
     * @Serializer\Groups({"add", "update","list", "detail", "search"})
     */
    public function getCategory() {

        $categories = $this->categories;

        return $categories->first();
    }

    /**
     * @Serializer\VirtualProperty()
     * @Serializer\Groups({"add", "update","list", "detail", "listitems", "moq", "search"})
     */
    public function getDisplayAsPromotion() {

        return ($this->getPromotion()) ? $this->getPromotion()->getDisplayAsPromotion() : false;
    }

    /**
     * @Serializer\VirtualProperty()
     * @Serializer\Groups({"add", "update","list", "detail", "listitems", "moq", "search"})
     */
    public function getHasPromotion() {

        return ($this->getPromotion()) ? true : false;
    }

    /**
     * @Serializer\VirtualProperty()
     * @Serializer\Groups({"add", "update","list", "detail", "search"})
     */
    public function getPromotion() {

        $promotion = false;

        $promotions = $this->getPromotions();
        
        if ($promotions) {

            $currentHighterPriority = null;

            $filteredPromotions = $promotions->filter(function($promotion){
                if(!in_array($promotion->getPromoCode(), Promotion::CODES_PRIORITY)){
                    return false;
                }
                return $promotion->isUserValid();
            });
           
            foreach ($filteredPromotions as $key => $value) {
                if(
                    $value->getDateStartValidity() <=  new \DateTime() &&
                    $value->getDateEndValidity() > new \DateTime()
                ){                    
                    $promoCode = $value->getPromoCode();                    
                    $priority = array_search($promoCode, Promotion::CODES_PRIORITY);

                    if ($priority < $currentHighterPriority || is_null($currentHighterPriority)) {
                        $currentHighterPriority = $priority;
                        $promotion = $value;                        
                    }                    
                }            
            }            
        }        

        return $promotion;
    }


    /**
     * Add promotion.
     *
     * @param \Sogedial\ApiBundle\Entity\Promotion $promotion
     *
     * @return Item
     */
    public function addPromotion(\Sogedial\ApiBundle\Entity\Promotion $promotion) {
        $this->promotions[] = $promotion;

        return $this;
    }

    /**
     * Remove promotion.
     *
     * @param \Sogedial\ApiBundle\Entity\Promotion $promotion
     *
     * @return boolean TRUE if this collection contained the specified element, FALSE otherwise.
     */
    public function removePromotion(\Sogedial\ApiBundle\Entity\Promotion $promotion) {
        return $this->promotions->removeElement($promotion);
    }

    /**
     * Get promotions.
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getPromotions() {
        return $this->promotions;
    }

    /**
     * @Serializer\VirtualProperty()
     * @Serializer\Groups({"add", "update","list", "detail", "search"})
     */
    public function getManufacturer() {

        $product = $this->getProduct();

        if ($product && $product->getManufacturer()) {
            return $product->getManufacturer();
        }

        return false;
    }

    /**
     * @Serializer\VirtualProperty()
     * @Serializer\Groups({"add", "update","list", "detail", "listitems", "moq", "search"})
     */
    public function getHasStock() {

        return ($this->getStock()) ? true : false;
    }

    /**
     * @return string
     */
    public function getCodeNature() {
        return $this->codeNature;
    }

    /**
     * Set codeNature.
     *
     * @param string $codeNature
     *
     * @return Item
     */
    public function setCodeNature($codeNature) {
        $this->codeNature = $codeNature;

        return $this;
    }

    /**
     * @return string
     */
    public function getFrequency() {
        return $this->frequency;
    }

    /**
     * Set frequency.
     *
     * @param string $frequency
     *
     * @return Item
     */
    public function setFrequency($frequency) {
        $this->frequency = $frequency;

        return $this;
    }

    /**
     * Get isPreorder.
     *
     * @return bool
     */
    public function getIsPreorder()
    {
        return $this->isPreorder;
    }



    /**
     * Add groupItem.
     *
     * @param \Sogedial\ApiBundle\Entity\GroupItem $groupItem
     *
     * @return Item
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

    /**
     * Get groupItems.
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getGroupItems() {
        return $this->groupItems;
    }

    /**
     * @Serializer\VirtualProperty()
     * @Serializer\Groups({"list"})
     */
    public function getGroupItemsSlugs() {

        return $this->groupItems->map(function($groupItem){
            return $groupItem->getSlug();
        });
    }
    
    /**
     * Add orderItem.
     *
     * @param \Sogedial\ApiBundle\Entity\OrderItem $orderItem
     *
     * @return Item
     */
    public function addOrderItem(\Sogedial\ApiBundle\Entity\OrderItem $orderItem)
    {
        $this->orderItems[] = $orderItem;

        return $this;
    }

    /**
     * Remove orderItem.
     *
     * @param \Sogedial\ApiBundle\Entity\OrderItem $orderItem
     *
     * @return boolean TRUE if this collection contained the specified element, FALSE otherwise.
     */
    public function removeOrderItem(\Sogedial\ApiBundle\Entity\OrderItem $orderItem)
    {
        return $this->orderItems->removeElement($orderItem);
    }

    /**
     * Get orderItems.
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getOrderItems()
    {
        return $this->orderItems;
    }


    /**
     * Set moq.
     *
     * @param int|null $moq
     *
     * @return Item
     */
    public function setMoq($moq = null)
    {
        $this->moq = $moq;

        return $this;
    }

    /**
     * Get moq.
     *
     * @return int|null
     */
    public function getMoq()
    {
        return $this->moq;
    }


    /**
     * Is Product has moq
     *
     * @Serializer\VirtualProperty()
     * @Serializer\Groups({"add", "update","list", "detail", "listitems", "moq", "search"})
     * @return boolean
     */
    public function hasMoq()
    {

        return ($this->getMoq() !== null && $this->getIsPreorder()) ? true : false;
    }

    /**
     * Set supplier.
     *
     * @param \Sogedial\ApiBundle\Entity\Supplier|null $supplier
     *
     * @return Item
     */
    public function setSupplier(\Sogedial\ApiBundle\Entity\Supplier $supplier = null)
    {
        $this->supplier = $supplier;

        return $this;
    }

    /**
     * Get supplier.
     *
     * @return \Sogedial\ApiBundle\Entity\Supplier|null
     */
    public function getSupplier()
    {
        return $this->supplier;
    }

    /**
     * Set variableWeight.
     *
     * @param bool|null $variableWeight
     *
     * @return Item
     */
    public function setVariableWeight($variableWeight = null)
    {
        $this->variableWeight = $variableWeight;

        return $this;
    }

    /**
     * Get variableWeight.
     *
     * @return bool|null
     */
    public function getVariableWeight()
    {
        return $this->variableWeight;
    }

     /**
     * Should Request More Stock
     *
     * @Serializer\VirtualProperty()
     * @Serializer\Groups({"update", "list", "detail", "search"})
     * @return boolean
     */
    public function getShouldRequestStock()
    {
        $promotion = $this->getPromotion();        
        if($promotion){
            return $promotion->getShouldRequestStock();
        }

        return false;
    }

    /**
     * Should Replace Regular Stock
     *
     * @Serializer\VirtualProperty()
     * @Serializer\Groups({"update", "list", "detail", "search"})
     * @return boolean
     */
    public function getShouldReplaceRegularStock()
    {
        $promotion = $this->getPromotion();        
        if($promotion){
            return $promotion->getShouldReplaceRegularStock();
        }

        return false;
    }
    

    /**
     * Set isPreorder.
     *
     * @param bool|null $isPreorder
     *
     * @return Item
     */
    public function setIsPreorder($isPreorder = null)
    {
        $this->isPreorder = $isPreorder;

        return $this;
    }
}
