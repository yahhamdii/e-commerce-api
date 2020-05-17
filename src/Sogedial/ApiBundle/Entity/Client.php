<?php

namespace Sogedial\ApiBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as Serializer;

use Sogedial\OAuthBundle\Entity\UserAdmin;
use Sogedial\OAuthBundle\Entity\UserCommercial;

/**
 * Client
 *
 * @ORM\Table(name="client")
 * @ORM\Entity(repositoryClass="Sogedial\ApiBundle\Repository\ClientRepository")
 * @Serializer\ExclusionPolicy("all")
 */
class Client extends AbstractEntity {

    const STATUS_ACTIVE = 'ACTIVE';
    const STATUS_INACTIVE = 'INACTIVE';
    const STATUS_BANISH = 'BANISH';
    const STATUS_PROSPECT = 'PROSPECT';
    const STATUS_BLOCKED = 'BLOCKED';
    const STATUS_PARTIAL = 'PARTIAL';
    const STATUS_PREORDER_ACTIVE = 'ACTIVE';
    const STATUS_PREORDER_INACTIVE = 'INACTIVE';
    const STATUS_PREORDER_BLOCKED = 'BLOCKED';
    const STATUS_CATALOG_ACTIVE = 'ACTIVE';
    const STATUS_CATALOG_INACTIVE = 'INACTIVE';
    const STATUS_CATALOG_BLOCKED = 'BLOCKED';

    /**
     * @ORM\OneToMany(targetEntity="Sogedial\ApiBundle\Entity\ClientStatus", mappedBy="client", cascade={"persist"})
     * @Serializer\Expose()
     * @Serializer\Groups({"search"})
     * @Serializer\MaxDepth(5)
     */
    private $clientStatus;

        /**
     * @ORM\OneToMany(targetEntity="Sogedial\ApiBundle\Entity\ClientFranco", mappedBy="client", cascade={"persist"})
     * @Serializer\Expose()
     * @Serializer\MaxDepth(5)
     */
    private $clientFranco;


    /**
     * @ORM\OneToMany(targetEntity="\Sogedial\OAuthBundle\Entity\UserCustomer", mappedBy="client", cascade={"persist"})
     * @Serializer\Type("ArrayCollection<Sogedial\OAuthBundle\Entity\UserCustomer>")
     * @Serializer\Expose
     * @Serializer\Groups({"search"})
     */
    private $customers;

    /**
     *
     * @ORM\ManyToMany(targetEntity="\Sogedial\OAuthBundle\Entity\UserCommercial", inversedBy="clients")
     * @Serializer\Expose
     * @Serializer\MaxDepth(3)
     * @Serializer\Type("ArrayCollection<Sogedial\OAuthBundle\Entity\UserCommercial>")
     */
    private $commercials;

    /**
     * @ORM\ManyToMany(targetEntity="DeliveryPlanning", inversedBy="clients")
     * @Serializer\MaxDepth(6)
     * @Serializer\Expose
     */
    private $deliveryPlannings;

    /**
     *
     * @ORM\ManyToMany(targetEntity="Platform", inversedBy="clients")
     * @Serializer\Expose
     * @Serializer\MaxDepth(5)
     */
    private $platforms;

    /**
     *
     * @ORM\ManyToMany(targetEntity="GroupClient", inversedBy="clients")
     * @Serializer\Type("ArrayCollection<Sogedial\ApiBundle\Entity\GroupClient>")
     */
    private $groupClients;

     /**
     * @ORM\OneToMany(targetEntity="GroupItem", mappedBy="client")
     * @Serializer\Type("ArrayCollection<Sogedial\OAuthBundle\Entity\GroupItem>")     
     */
    private $groupItems;

    /**
     * @ORM\ManyToMany(targetEntity="Sogedial\ApiBundle\Entity\Term", inversedBy="clients")
     * @Serializer\MaxDepth(2)
     * @Serializer\Expose
     */
    private $terms;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=255)
     * @Serializer\Expose
     * @Serializer\Groups({"search"})
     */
    private $name;

    /**
     * @var string
     *
     * @ORM\Column(name="siret", type="string", length=255, unique=true)
     * @Serializer\Expose
     */
    private $siret;

    /**
     * @var string|null
     *
     * @ORM\Column(name="zipcode", type="string", length=255, nullable=true)
     * @Serializer\Expose
     */
    private $zipcode;

    /**
     * @var string|null
     *
     * @ORM\Column(name="address", type="string", length=255, nullable=true)
     * @Serializer\Expose
     */
    private $address;

    /**
     * @var string|null
     *
     * @ORM\Column(name="address2", type="string", length=255, nullable=true)
     * @Serializer\Expose
     */
    private $address2;

    
    /**
     * @var string|null
     *
     * @ORM\Column(name="contact", type="string", length=255, nullable=true)
     * @Serializer\Expose
     */
    private $contact;

    /**
     * @var string|null
     *
     * @ORM\Column(name="contact2", type="string", length=255, nullable=true)
     * @Serializer\Expose
     */
    private $contact2;

    /**
     * @var string
     *
     * @ORM\Column(name="country", type="string", length=255, nullable=true)
     * @Serializer\Expose
     */
    private $country;

    /**
     * @var string|null
     *
     * @ORM\Column(name="city", type="string", length=255, nullable=true)
     * @Serializer\Expose
     */
    private $city;

    /**
     * @var string|null
     *
     * @ORM\Column(name="ext_code", type="string", length=255, unique=true)
     * @Serializer\Expose
     * @Serializer\Groups({"search"})
     */
    private $extCode;


    /**
     * @var string|null
     *
     * @ORM\Column(name="is_activated", type="boolean", options={"default" : false}, nullable=true)
     * @Serializer\Expose     
     */
    private $isActivated;


    /**
     * @var string|null
     *
     * @ORM\Column(name="status", type="string", length=255, nullable=true, options={"default" : Client::STATUS_INACTIVE})
     * @Serializer\Expose
     */
    private $status;


    /**
     * @var \DateTime
     *
     * @ORM\Column(name="validity_begin_date", type="datetime", nullable=true)
     * @Serializer\Expose
     */
    private $validityBeginDate;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="validity_end_date", type="datetime", nullable=true)
     * @Serializer\Expose
     */
    private $validityEndDate;


    /**
     * @var string
     *
     * @ORM\Column(name="typology", type="string", length=255, nullable=true)
     * @Serializer\Expose
     */
    private $typology;

    /**
     * @var string
     *
     * @ORM\Column(name="amount_customer_in_progress", type="string", length=255, nullable=true)
     * @Serializer\Expose
     */
    private $amountCustomerInProgress;

    /**
     * @ORM\OneToMany(targetEntity="Invoice", mappedBy="client")
     */
    private $invoicesClient;

    /**
     * @ORM\OneToMany(targetEntity="Sogedial\ApiBundle\Entity\ClientDeliveryMode", mappedBy="client", cascade={"remove", "persist"})
     * @Serializer\Expose()
     * @Serializer\MaxDepth(5)
     */
    private $clientDeliveryModes;

    /**
     * @ORM\ManyToMany(targetEntity="Brand", mappedBy="clients")
     */
    private $brands;

    /**
     * Constructor
     */
    public function __construct() {
        $this->commercials = new \Doctrine\Common\Collections\ArrayCollection();
        $this->clientStatus = new \Doctrine\Common\Collections\ArrayCollection();
        $this->platforms = new \Doctrine\Common\Collections\ArrayCollection();
        $this->deliveryPlannings = new \Doctrine\Common\Collections\ArrayCollection();
        $this->customers = new \Doctrine\Common\Collections\ArrayCollection();
        $this->terms = new \Doctrine\Common\Collections\ArrayCollection();
        $this->clientDeliveryModes = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Add commercial.
     *
     * @param \Sogedial\OAuthBundle\Entity\UserCommercial $commercial
     *
     * @return Client
     */
    public function addCommercial(\Sogedial\OAuthBundle\Entity\UserCommercial $commercial) {
        $this->commercials[] = $commercial;

        return $this;
    }

    /**
     * Remove commercial.
     *
     * @param \Sogedial\OAuthBundle\Entity\UserCommercial $commercial
     *
     * @return boolean TRUE if this collection contained the specified element, FALSE otherwise.
     */
    public function removeCommercial(\Sogedial\OAuthBundle\Entity\UserCommercial $commercial) {
        return $this->commercials->removeElement($commercial);
    }

    /**
     * Get commercials.
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getCommercials() {
        return $this->commercials;
    }

    public function getCommercialsByPlatform($platform) {
        return $this->commercials->filter(function($commercial) use ($platform){           
           return ($commercial->getPlatform()->getId() == $platform->getId());
        });

    }

    /**
     * Set name.
     *
     * @param string $name
     *
     * @return Client
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
     * Set siret.
     *
     * @param string $siret
     *
     * @return Client
     */
    public function setSiret($siret) {
        $this->siret = $siret;

        return $this;
    }

    /**
     * Get siret.
     *
     * @return string
     */
    public function getSiret() {
        return $this->siret;
    }

    /**
     * Set zipcode.
     *
     * @param string|null $zipcode
     *
     * @return Client
     */
    public function setZipcode($zipcode = null) {
        $this->zipcode = $zipcode;

        return $this;
    }

    /**
     * Get zipcode.
     *
     * @return string|null
     */
    public function getZipcode() {
        return $this->zipcode;
    }

    /**
     * Set address.
     *
     * @param string|null $address
     *
     * @return Client
     */
    public function setAddress($address = null) {
        $this->address = $address;

        return $this;
    }

    /**
     * Get address.
     *
     * @return string|null
     */
    public function getAddress() {
        return $this->address;
    }

    /**
     * Set country.
     *
     * @param string $country
     *
     * @return Client
     */
    public function setCountry($country) {
        $this->country = $country;

        return $this;
    }

    /**
     * Get country.
     *
     * @return string
     */
    public function getCountry() {
        return $this->country;
    }

    /**
     * Set city.
     *
     * @param string|null $city
     *
     * @return Client
     */
    public function setCity($city = null) {
        $this->city = $city;

        return $this;
    }

    /**
     * Get city.
     *
     * @return string|null
     */
    public function getCity() {
        return $this->city;
    }

    /**
     * Set extCode.
     *
     * @param string|null $extCode
     *
     * @return Client
     */
    public function setExtCode($extCode = null) {
        $this->extCode = $extCode;

        return $this;
    }

    /**
     * Get extCode.
     *
     * @return string|null
     */
    public function getExtCode() {
        return $this->extCode;
    }

    /**
     * Add platform.
     *
     * @param \Sogedial\ApiBundle\Entity\Platform $platform
     *
     * @return Client
     */
    public function addPlatform(\Sogedial\ApiBundle\Entity\Platform $platform) {
        $this->platforms[] = $platform;

        return $this;
    }

    /**
     * Remove platform.
     *
     * @param \Sogedial\ApiBundle\Entity\Platform $platform
     *
     * @return boolean TRUE if this collection contained the specified element, FALSE otherwise.
     */
    public function removePlatform(\Sogedial\ApiBundle\Entity\Platform $platform) {
        return $this->platforms->removeElement($platform);
    }

    /**
     * Get platforms.
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getPlatforms() {
        return $this->platforms;
    }

    /**
     * Set dateCreate.
     *
     * @param \DateTime|null $dateCreate
     *
     * @return Client
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
     * @return Client
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
     * Add customer.
     *
     * @param \Sogedial\OAuthBundle\Entity\UserCustomer $customer
     *
     * @return Client
     */
    public function addCustomer(\Sogedial\OAuthBundle\Entity\UserCustomer $customer) {
        $this->customers[] = $customer;

        return $this;
    }

    /**
     * Remove customer.
     *
     * @param \Sogedial\OAuthBundle\Entity\UserCustomer $customer
     *
     * @return boolean TRUE if this collection contained the specified element, FALSE otherwise.
     */
    public function removeCustomer(\Sogedial\OAuthBundle\Entity\UserCustomer $customer) {
        return $this->customers->removeElement($customer);
    }

    /**
     * Get customers.
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getCustomers() {
        return $this->customers;
    }

    /**
     * Set status.
     *
     * @param string|null $status
     *
     * @return Client
     */
    public function setStatus($status = null) {
        $this->status = $status;

        return $this;
    }

    /**
     * Get status.
     *
     * @return string|null
     */
    public function getStatus() {
        return $this->status;
    }

    /**
     * Add deliveryPlanning.
     *
     * @param \Sogedial\ApiBundle\Entity\DeliveryPlanning $deliveryPlanning
     *
     * @return Client
     */
    public function addDeliveryPlanning(\Sogedial\ApiBundle\Entity\DeliveryPlanning $deliveryPlanning) {
        $this->deliveryPlannings[] = $deliveryPlanning;

        return $this;
    }

    /**
     * Remove deliveryPlanning.
     *
     * @param \Sogedial\ApiBundle\Entity\DeliveryPlanning $deliveryPlanning
     *
     * @return boolean TRUE if this collection contained the specified element, FALSE otherwise.
     */
    public function removeDeliveryPlanning(\Sogedial\ApiBundle\Entity\DeliveryPlanning $deliveryPlanning) {
        return $this->deliveryPlannings->removeElement($deliveryPlanning);
    }

    /**
     * Get deliveryPlannings.
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getDeliveryPlannings() {
        return $this->deliveryPlannings;
    }

    /**
     * Set validityBeginDate.
     *
     * @param \DateTime|null $validityBeginDate
     *
     * @return Client
     */
    public function setValidityBeginDate($validityBeginDate = null)
    {
        $this->validityBeginDate = $validityBeginDate;

        return $this;
    }

    /**
     * Get validityBeginDate.
     *
     * @return \DateTime|null
     */
    public function getValidityBeginDate()
    {
        return $this->validityBeginDate;
    }

    /**
     * Set validityEndDate.
     *
     * @param \DateTime|null $validityEndDate
     *
     * @return Client
     */
    public function setValidityEndDate($validityEndDate = null)
    {
        $this->validityEndDate = $validityEndDate;

        return $this;
    }

    /**
     * Get validityEndDate.
     *
     * @return \DateTime|null
     */
    public function getValidityEndDate()
    {
        return $this->validityEndDate;
    }


    /**
     * Set typology.
     *
     * @param string|null $typology
     * @return $this
     */
    public function setTypology($typology = null)
    {
        $this->typology = $typology;

        return $this;
    }

    /**
     * Get typology.
     *
     * @return string|null
     */
    public function getTypology()
    {
        return $this->typology;
    }

    /**
     * Add clientStatus.
     *
     * @param \Sogedial\ApiBundle\Entity\ClientStatus $clientStatus
     *
     * @return Client
     */
    public function addClientStatus(\Sogedial\ApiBundle\Entity\ClientStatus $clientStatus)
    {
        $this->clientStatus[] = $clientStatus;

        return $this;
    }

    /**
     * Remove clientStatus.
     *
     * @param \Sogedial\ApiBundle\Entity\ClientStatus $clientStatus
     *
     * @return boolean TRUE if this collection contained the specified element, FALSE otherwise.
     */
    public function removeClientStatus(\Sogedial\ApiBundle\Entity\ClientStatus $clientStatus)
    {
        return $this->clientStatus->removeElement($clientStatus);
    }

    /**
     * Get clientStatus.
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getClientStatus()
    {
        return $this->clientStatus;
    }


    public function getStatusPreorder()
    {
        $clientStatus = $this->getClientStatus();
        $active = 0;
        $inactive = 0;
        $blocked = 0;
        $count = $clientStatus->count();

        if($count == 0){
            return ClientStatus::STATUS_PREORDER_BLOCKED;
        }
        foreach ($clientStatus as $elem) {
            /** @var ClientStatus $elem  */
            $statusPreorder = $elem->getStatusPreorder();
            if ($statusPreorder === ClientStatus::STATUS_PREORDER_ACTIVE) {
                $active++;
            } elseif ($statusPreorder === ClientStatus::STATUS_PREORDER_INACTIVE ) {
                $inactive++;
            } elseif($statusPreorder === ClientStatus::STATUS_PREORDER_BLOCKED){
                $blocked++;
            }
        }

        if ($active == $count) {
            return ClientStatus::STATUS_PREORDER_ACTIVE;
        } elseif ($inactive == $count) {
            return ClientStatus::STATUS_PREORDER_INACTIVE;
        } elseif ($blocked == $count) {
            return ClientStatus::STATUS_PREORDER_BLOCKED;
        } else {
            return ClientStatus::STATUS_PREORDER_PARTIAL;
        }

    }

    public function getStatusCatalog()
    {
        $clientStatus = $this->getClientStatus();
        $active = 0;
        $inactive = 0;
        $blocked = 0;
        $count = $clientStatus->count();

        if($count == 0){
            return ClientStatus::STATUS_CATALOG_BLOCKED;
        }
        foreach ($clientStatus as $elem){
            $statusCatalog = $elem->getStatusCatalog();
            /** @var ClientStatus $elem */
            if($statusCatalog === ClientStatus::STATUS_CATALOG_ACTIVE){
                $active++;
            }elseif ($statusCatalog === ClientStatus::STATUS_CATALOG_INACTIVE ){
                $inactive++;
            }elseif($statusCatalog === ClientStatus::STATUS_CATALOG_BLOCKED){
                $blocked++;
            }
        }

        if($count == $active){
            return ClientStatus::STATUS_CATALOG_ACTIVE;
        }elseif ($count == $inactive){
            return ClientStatus::STATUS_CATALOG_INACTIVE;
        }elseif($count == $blocked){
            return ClientStatus::STATUS_CATALOG_BLOCKED;
        }else{
            return ClientStatus::STATUS_CATALOG_PARTIAL;
        }
    }


    public function getClientStatusByPlatform($idPlatform)
    {
        $clientStatus = $this->getClientStatus();
        foreach ($clientStatus as $elem) {
            /** @var ClientStatus $elem */
            if($elem->getPlatform()->getId() == $idPlatform){
                return $elem;
            }
        }

        return null;
    }

    /**
     * Add clientFranco.
     *
     * @param \Sogedial\ApiBundle\Entity\ClientFranco $clientFranco
     *
     * @return Client
     */
    public function addClientFranco(\Sogedial\ApiBundle\Entity\ClientFranco $clientFranco)
    {
        $this->clientFranco[] = $clientFranco;

        return $this;
    }

    /**
     * Remove clientFranco.
     *
     * @param \Sogedial\ApiBundle\Entity\ClientFranco $clientFranco
     *
     * @return boolean TRUE if this collection contained the specified element, FALSE otherwise.
     */
    public function removeClientFranco(\Sogedial\ApiBundle\Entity\ClientFranco $clientFranco)
    {
        return $this->clientFranco->removeElement($clientFranco);
    }

    /**
     * Get clientFranco.
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getClientFranco()
    {
        return $this->clientFranco;
    }
   

    /**
     * Add term.
     *
     * @param \Sogedial\ApiBundle\Entity\Term $term
     *
     * @return Client
     */
    public function addTerm(\Sogedial\ApiBundle\Entity\Term $term)
    {
        if($this->getTermByPlatform($term->getPlatform())){
            $this->removeTerm($this->getTermByPlatform($term->getPlatform()));
        }

        $this->terms[] = $term;

        return $this;
    }

    public function getTermByPlatform($platform){
        foreach($this->terms as $term){
            if( $term->getPlatformId() == $platform->getId() ){
                return $term;
            }
        }
        return null;
    }

    /**
     * Remove term.
     *
     * @param \Sogedial\ApiBundle\Entity\Term $term
     *
     * @return boolean TRUE if this collection contained the specified element, FALSE otherwise.
     */
    public function removeTerm(\Sogedial\ApiBundle\Entity\Term $term)
    {
        return $this->terms->removeElement($term);
    }

    /**
     * Get terms.
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getTerms()
    {
        return $this->terms;
    }

    /**
     * Add groupItem.
     *
     * @param \Sogedial\ApiBundle\Entity\GroupItem $groupItem
     *
     * @return Client
     */
    public function addGroupItem(\Sogedial\ApiBundle\Entity\GroupItem $groupItem)
    {
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
    public function removeGroupItem(\Sogedial\ApiBundle\Entity\GroupItem $groupItem)
    {
        return $this->groupItems->removeElement($groupItem);
    }

    /**
     * Get groupItems.
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getGroupItems()
    {
        return $this->groupItems;
    }


    public function getAllGroupItems($platform = null, $status = '', $dateRestriction = true){
        $groupItems = $this->getGroupItems();
        $groupClients = $this->getGroupClients();

        foreach($groupClients as $groupClient){
            if( $platform == null || $groupClient->getPlatform()->getId() == $platform->getId() ){
                $groupItems = new \Doctrine\Common\Collections\ArrayCollection(
                    array_merge($groupClient->getGroupItems( $status, $dateRestriction )->toArray(), $groupItems->toArray())
                );
            }
        }

        return $groupItems;
    }


    /**
     * Add groupClient.
     *
     * @param \Sogedial\ApiBundle\Entity\GroupClient $groupClient
     *
     * @return Client
     */
    public function addGroupClient(\Sogedial\ApiBundle\Entity\GroupClient $groupClient)
    {
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
    public function removeGroupClient(\Sogedial\ApiBundle\Entity\GroupClient $groupClient)
    {
        return $this->groupClients->removeElement($groupClient);
    }

    /**
     * Get groupClients.
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getGroupClients()
    {
        return $this->groupClients;
    }

    /**
     * Get groupClient.
     * Retourne l'enseigne
     * @return Sogedial\ApiBundle\Entity\GroupClient
     * @Serializer\Groups({"search"})
     * @Serializer\VirtualProperty()
     * @Serializer\MaxDepth(1)
     */     
    public function getGroupClient( $platform = null )
    {
        $groupClients = $this->groupClients;

        if($this->getCurrentUser()
        && $this->getCurrentUser() instanceof UserAdmin
        || $this->getCurrentUser() instanceof UserCommercial ){
            $platform = $this->getCurrentUser()->getPlatform();
        }
        
        foreach($groupClients as $groupClient){
            if( (( $platform !== null && $groupClient->getPlatform()->getId() == $platform->getId() )
                 || $platform == null)
                && 
                $groupClient->getStatus() == \Sogedial\ApiBundle\Entity\GroupClient::STATUS_ENSEIGNE
            ){                
                return $groupClient;                
            }
        }
        return null;
    }

    /**
     * Set amountCustomerInProgress.
     *
     * @param string|null $amountCustomerInProgress
     *
     * @return Client
     */
    public function setAmountCustomerInProgress($amountCustomerInProgress = null)
    {
        $this->amountCustomerInProgress = $amountCustomerInProgress;

        return $this;
    }

    /**
     * Get amountCustomerInProgress.
     *
     * @return string|null
     */
    public function getAmountCustomerInProgress()
    {
        return $this->amountCustomerInProgress;
    }


    /**
     * @param $idPlatform
     * @return null|ClientDeliveryMode
     */
    public function getClientDeliveryModeByPlatform($idPlatform)
    {
        $clientDeliveryModes = $this->getClientDeliveryModes();
        /** @var ClientDeliveryMode $clientDeliveryMode */
        foreach ($clientDeliveryModes as $clientDeliveryMode){
            if($clientDeliveryMode->getPlatform()->getId() == $idPlatform){

                return $clientDeliveryMode;
            }
        }

        return null;
    }

    /**
     * Add clientDeliveryMode.
     *
     * @param \Sogedial\ApiBundle\Entity\ClientDeliveryMode $clientDeliveryMode
     *
     * @return Client
     */
    public function addClientDeliveryMode(\Sogedial\ApiBundle\Entity\ClientDeliveryMode $clientDeliveryMode)
    {
        $this->clientDeliveryModes[] = $clientDeliveryMode;

        return $this;
    }

    /**
     * Remove clientDeliveryMode.
     *
     * @param \Sogedial\ApiBundle\Entity\ClientDeliveryMode $clientDeliveryMode
     *
     * @return boolean TRUE if this collection contained the specified element, FALSE otherwise.
     */
    public function removeClientDeliveryMode(\Sogedial\ApiBundle\Entity\ClientDeliveryMode $clientDeliveryMode)
    {
        return $this->clientDeliveryModes->removeElement($clientDeliveryMode);
    }

    /**
     * Get clientDeliveryModes.
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getClientDeliveryModes()
    {
        return $this->clientDeliveryModes;
    }


    /**
     * Set address2.
     *
     * @param string|null $address2
     *
     * @return Client
     */
    public function setAddress2($address2 = null)
    {
        $this->address2 = $address2;

        return $this;
    }

    /**
     * Get address2.
     *
     * @return string|null
     */
    public function getAddress2()
    {
        return $this->address2;
    }

    /**
     * Set contact.
     *
     * @param string|null $contact
     *
     * @return Client
     */
    public function setContact($contact = null)
    {
        $this->contact = $contact;

        return $this;
    }

    /**
     * Get contact.
     *
     * @return string|null
     */ 
    public function getContact()
    {
        return $this->contact;
    }

    /**
     * Set contact2.
     *
     * @param string|null $contact2
     *
     * @return Client
     */
    public function setContact2($contact2 = null)
    {
        $this->contact2 = $contact2;

        return $this;
    }

    /**
     * Get contact2.
     *
     * @return string|null
     */
    public function getContact2()
    {
        return $this->contact2;
    }

    /**
     * Add invoicesClient.
     *
     * @param \Sogedial\ApiBundle\Entity\Invoice $invoicesClient
     *
     * @return Client
     */
    public function addInvoicesClient(\Sogedial\ApiBundle\Entity\Invoice $invoicesClient)
    {
        $this->invoicesClient[] = $invoicesClient;

        return $this;
    }

    /**
     * Remove invoicesClient.
     *
     * @param \Sogedial\ApiBundle\Entity\Invoice $invoicesClient
     *
     * @return boolean TRUE if this collection contained the specified element, FALSE otherwise.
     */
    public function removeInvoicesClient(\Sogedial\ApiBundle\Entity\Invoice $invoicesClient)
    {
        return $this->invoicesClient->removeElement($invoicesClient);
    }

    /**
     * Get invoicesClient.
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getInvoicesClient()
    {
        return $this->invoicesClient;
    }

    /**
     * Set isActivated.
     *
     * @param bool|null $isActivated
     *
     * @return Client
     */
    public function setIsActivated($isActivated = null)
    {
        $this->isActivated = $isActivated;

        return $this;
    }

    /**
     * Get isActivated.
     *
     * @return bool|null
     */
    public function getIsActivated()
    {
        return $this->isActivated;
    }

    /**
     * Add brand.
     *
     * @param \Sogedial\ApiBundle\Entity\Brand $brand
     *
     * @return Client
     */
    public function addBrand(\Sogedial\ApiBundle\Entity\Brand $brand)
    {
        $this->brands[] = $brand;

        return $this;
    }

    /**
     * Remove brand.
     *
     * @param \Sogedial\ApiBundle\Entity\Brand $brand
     *
     * @return boolean TRUE if this collection contained the specified element, FALSE otherwise.
     */
    public function removeBrand(\Sogedial\ApiBundle\Entity\Brand $brand)
    {
        return $this->brands->removeElement($brand);
    }

    /**
     * Get brands.
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getBrands()
    {
        return $this->brands;
    }

    /**
     * Get brand  by platform.
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getBrandByPlatform($platform)
    {
        $brands = $this->brands->filter(function($brand) use ($platform){            
            return ($platform && $brand->getPlatform() && $brand->getPlatform()->getId() == $platform->getId());
        });        
        return ($brands)?$brands->first():null;
    }

    /**
     * Get Brand.
     * @Serializer\VirtualProperty()
     * @Serializer\Groups({"search", "list"})
     */    
    public function getBrand()
    {
        $user = $this->getCurrentUser();
        if($user instanceof UserAdmin || $user instanceof UserCommercial){

            return $this->getBrandByPlatform($user->getPlatform());
            
        }

        return null;
      
    }

}
