<?php

namespace Sogedial\ApiBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as Serializer;
use Sogedial\OAuthBundle\Entity\UserCustomer;

/**
 * Promotion
 *
 * @ORM\Table(name="promotion")
 * @ORM\Entity(repositoryClass="Sogedial\ApiBundle\Repository\PromotionRepository")
 * @Serializer\ExclusionPolicy("all")
 */
class Promotion extends AbstractEntity {

    /* To add : CA */
    // const CODES_PRIORITY = ["EF", "CE", "CL", "RG", "EN", "FR", "TX"];
    const CODES_PRIORITY = ["EF", "CL", "EN", "TX"];

    /**
     * @ORM\ManyToMany(targetEntity="Item", inversedBy="promotions" )
     * @ORM\JoinColumn(nullable=false)
     */
    private $items;

    /**
     * @ORM\ManyToOne(targetEntity="Sogedial\ApiBundle\Entity\Client")
     */
    private $client;

    /**
     * @var Datetime
     * @Serializer\Expose
     * @ORM\Column(name="date_start_validity", type="datetime")
     * @Serializer\Groups({"add", "update","list", "detail"})
     */
    private $dateStartValidity;

    /**
     * @var Datetime
     * @Serializer\Expose
     * @ORM\Column(name="date_end_validity", type="datetime")
     * @Serializer\Groups({"add", "update","list", "detail"})
     */
    private $dateEndValidity;

    /**
     * @var string
     * @Serializer\Expose
     * @ORM\Column(name="exception_treatment_code", type="string", length=1)
     * @Serializer\Groups({"add", "update","list", "detail"})
     */
    private $exceptionTreatmentCode;

    /**
     * @var string
     * @Serializer\Expose
     * @ORM\Column(name="resale_loss", type="string", length=125)
     * @Serializer\Groups({"add", "update","list", "detail"})
     */
    private $resaleLoss;

    /**
     * @var string
     * @Serializer\Expose
     * @ORM\Column(name="price", type="string", length=255)
     * @Serializer\Groups({"add", "update","list", "detail", "search"})
     */
    private $price;

    /**
     * @var string
     * @Serializer\Expose
     * @ORM\Column(name="promo_code", type="string", length=125, nullable=true)
     * @Serializer\Groups({"add", "update","list", "detail"})
     */
    private $promoCode;

    /**
     * @var string
     * @Serializer\Expose
     * @ORM\Column(name="stock_commitment", type="string", length=15, nullable=true)
     * @Serializer\Groups({"add", "update","list", "detail"})
     */
    private $stockCommitment;

    /**
     * @var string
     * @Serializer\Expose
     * @ORM\Column(name="stock_commitment_remaining", type="string", length=15, nullable=true)
     * @Serializer\Groups({"add", "update","list", "detail"})
     */
    private $stockCommitmentRemaining;

    /**
     * @var string
     * @Serializer\Expose
     * @ORM\Column(name="stock_commitment_request", type="string", length=15, nullable=true)
     * @Serializer\Groups({"add", "update","list", "detail"})
     */
    private $stockCommitmentRequest;

    /**
     * @var boolean
     * @Serializer\Expose
     * @ORM\Column(name="has_requested_commitment", type="boolean", options={"default": false})
     * @Serializer\Groups({"list"})
     */
    private $hasRequestedCommitment;

    /**
     * @var string|null
     * @Serializer\Expose
     * @ORM\Column(name="code", type="string", length=255, unique=true)
     * @Serializer\Groups({"add", "update","list", "detail"})
     */
    private $code;


    /**
     * @var string|null
     * @Serializer\Expose
     * @ORM\Column(name="display_as_promotion", type="boolean", options={"default":true})
     * @Serializer\Groups({"add", "update","list", "detail", "search"})
     */
    private $displayAsPromotion;

    /**
     * @ORM\ManyToOne(targetEntity="Brand", inversedBy="promotions")
     * @ORM\JoinColumn(name="brand_id", referencedColumnName="id")
     */
    private $brand;

    /**
     * Set client.
     * @param \Sogedial\ApiBundle\Entity\Client|null $client
     *
     * @return Promotion
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
     * Set dateCreate.
     *
     * @param \DateTime|null $dateCreate
     *
     * @return Promotion
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
     * @return Promotion
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
     * Set dateStartValidity.
     *
     * @param \DateTime $dateStartValidity
     *
     * @return Promotion
     */
    public function setDateStartValidity($dateStartValidity) {
        $this->dateStartValidity = $dateStartValidity;

        return $this;
    }

    /**
     * Get dateStartValidity.
     *
     * @return \DateTime
     */
    public function getDateStartValidity() {
        return $this->dateStartValidity;
    }

    /**
     * Set dateEndValidity.
     *
     * @param \DateTime $dateEndValidity
     *
     * @return Promotion
     */
    public function setDateEndValidity($dateEndValidity) {
        $this->dateEndValidity = $dateEndValidity;

        return $this;
    }

    /**
     * Get dateEndValidity.
     *
     * @return \DateTime
     */
    public function getDateEndValidity() {
        return $this->dateEndValidity;
    }

    /**
     * Set exceptionTreatmentCode.
     *
     * @param string $exceptionTreatmentCode
     *
     * @return Promotion
     */
    public function setExceptionTreatmentCode($exceptionTreatmentCode) {
        $this->exceptionTreatmentCode = $exceptionTreatmentCode;

        return $this;
    }

    /**
     * Get exceptionTreatmentCode.
     *
     * @return string
     */
    public function getExceptionTreatmentCode() {
        return $this->exceptionTreatmentCode;
    }

    /**
     * Set resaleLoss.
     *
     * @param string $resaleLoss
     *
     * @return Promotion
     */
    public function setResaleLoss($resaleLoss) {
        $this->resaleLoss = $resaleLoss;

        return $this;
    }

    /**
     * Get resaleLoss.
     *
     * @return string
     */
    public function getResaleLoss() {
        return $this->resaleLoss;
    }

    /**
     * Set price.
     *
     * @param string $price
     *
     * @return Promotion
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
    public function getPrice( $qty = 1 ) {

        return $this->price * $qty;

    }

    /**
     * Get price vat.
     *
     * @return string
     */
    public function getPriceVat( $vat, $qty = 1 ) {

        $price = $this->getPrice( $qty );

        $priceVat = $price + $price *  $vat / 100;

        return $priceVat;
    }

    /**
     * Set promoCode.
     *
     * @param string|null $promoCode
     *
     * @return Promotion
     */
    public function setPromoCode($promoCode = null) {
        $this->promoCode = $promoCode;

        return $this;
    }

    /**
     * Get promoCode.
     *
     * @return string|null
     */
    public function getPromoCode() {
        return $this->promoCode;
    }

    /**
     * Set stockCommitment.
     *
     * @param string|null $stockCommitment
     *
     * @return Promotion
     */
    public function setStockCommitment($stockCommitment = null) {
        $this->stockCommitment = $stockCommitment;

        return $this;
    }

    /**
     * Get stockCommitment.
     *
     * @return string|null
     */
    public function getStockCommitment() {
        return $this->stockCommitment;
    }

    /**
     * Set stockCommitmentRemaining.
     *
     * @param string|null $stockCommitmentRemaining
     *
     * @return Promotion
     */
    public function setStockCommitmentRemaining($stockCommitmentRemaining = null) {
        $this->stockCommitmentRemaining = $stockCommitmentRemaining;

        return $this;
    }

    /**
     * Get stockCommitmentRemaining.
     *
     * @return string|null
     */
    public function getStockCommitmentRemaining() {
        return $this->stockCommitmentRemaining;
    }

    /**
     * Set stockCommitmentRequest.
     *
     * @param string|null $stockCommitmentRequest
     *
     * @return Promotion
     */
    public function setStockCommitmentRequest($stockCommitmentRequest = null) {
        $this->stockCommitmentRequest = $stockCommitmentRequest;

        return $this;
    }

    /**
     * Get stockCommitmentRequest.
     *
     * @return string|null
     */
    public function getStockCommitmentRequest() {
        return $this->stockCommitmentRequest;
    }

    /**
     * Set code.
     *
     * @param string|null $code
     *
     * @return Promotion
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
     * Constructor
     */
    public function __construct() {
        $this->items = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Add item.
     *
     * @param \Sogedial\ApiBundle\Entity\Item $item
     *
     * @return Promotion
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
     * Set brand.
     *
     * @param \Sogedial\ApiBundle\Entity\Brand|null $brand
     *
     * @return Promotion
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

    public function isUserValid(){
        if(!$this->getBrand() && !$this->getClient()){
            return true;
        }

        $platform = $this->getPlatform();
        $user = $this->getCurrentUser();        
        if( $user && $user instanceof UserCustomer){
            $client = $user->getClient();            
            $userBrand = $client->getBrandByPlatform( $platform );
            if($this->getBrand() && $userBrand && $userBrand->getId() === $this->getBrand()->getId()){
                return true;
            }
            if($this->getClient() && $client && $client->getId() === $this->getClient()->getId()){
                return true;
            }
        }
        return false;
    }

    /**
     * Set displayAsPromotion.
     *
     * @param string $displayAsPromotion
     *
     * @return Promotion
     */
    public function setDisplayAsPromotion($displayAsPromotion)
    {
        $this->displayAsPromotion = $displayAsPromotion;

        return $this;
    }

    /**
     * Get displayAsPromotion.
     *
     * @return string
     */
    public function getDisplayAsPromotion()
    {
        return $this->displayAsPromotion;
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
        if($this->getPromoCode() == "EF" && $this->getStockCommitmentRemaining() == 0){
            return true;
        }

        return false;
    }

    /**
     * Should Replace regular Stock     
     * @return boolean
     */
    public function getShouldReplaceRegularStock()
    {
        if($this->getPromoCode() == "EF"){
            return true;
        }

        return false;
    }

    public function getStock(){
        $items = $this->getItems();        
        $newStock = new Stock();
        if($items->count()>0){
            $newStock->setValueCu($this->getStockCommitmentRemaining()*$items->first()->getPcb());            
        }
        $newStock->setValuePacking($this->getStockCommitmentRemaining());        
        return $newStock;
    }

    public function getPlatform(){
        $items = $this->getItems();
        if($items->count()>0){
            return $items->first()->getPlatform();
        }
        return false;
    }

    /**
     * Set hasRequestedCommitment.
     *
     * @param bool $hasRequestedCommitment
     *
     * @return Promotion
     */
    public function setHasRequestedCommitment($hasRequestedCommitment)
    {
        $this->hasRequestedCommitment = $hasRequestedCommitment;

        return $this;
    }

    /**
     * Get hasRequestedCommitment.
     *
     * @return bool
     */
    public function getHasRequestedCommitment()
    {
        return $this->hasRequestedCommitment;
    }
}
