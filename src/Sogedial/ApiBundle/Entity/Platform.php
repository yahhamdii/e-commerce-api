<?php
namespace Sogedial\ApiBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as Serializer;

/**
 * Plateform
 *
 * @ORM\Table(name="platform")
 * @ORM\Entity(repositoryClass="Sogedial\ApiBundle\Repository\PlatformRepository")
 * @Serializer\ExclusionPolicy("all")
 */
class Platform extends AbstractEntity { 

    /**
     *
     * @ORM\ManyToMany(targetEntity="Client", mappedBy="platforms")
     */
    private $clients;

    /**
     *
     * @ORM\OneToMany(targetEntity="Attribut", mappedBy="platform", cascade={"persist"})
     * @Serializer\Expose
     * @Serializer\MaxDepth(3)
     */
    private $attributs;

    /**
     *
     * @ORM\ManyToMany(targetEntity="Category")
     */
    private $categories;

    /**
     *
     * @ORM\ManyToMany(targetEntity="Product",mappedBy="platforms")
     */
    private $products;

    /**
     * @ORM\OneToMany(targetEntity="Sogedial\OAuthBundle\Entity\UserAdmin", mappedBy="platform")
     */
    private $admins;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=255)
     * @Serializer\Expose
     */
    private $name;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Serializer\Expose
     */
    private $siret;

    /**
     * @var string|null
     *
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Serializer\Expose
     */
    private $zipcode;

    /**
     * @var string|null
     *
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Serializer\Expose
     */
    private $address;

     /**
     * @var string|null
     *
     * @ORM\Column(type="text", nullable=true)
     * @Serializer\Expose
     */
    private $legals;

     /**
     * @var string|null
     *
     * @ORM\Column(name="cookie_path", type="string", length=255, nullable=true)
     * @Serializer\Expose
     */
    private $cookiePath;

    /**
     * @var string|null
     *
     * @ORM\Column(name="privacy_path", type="string", length=255, nullable=true)
     * @Serializer\Expose
     */
    private $privacyPath;
    

    /**
     * @var string|null
     *
     * @ORM\Column(name="short_name", type="string", length=255, nullable=true)
     * @Serializer\Expose
     */
    private $shortName;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=255, nullable=true)
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
     */
    private $extCode;

    /**
     * @ORM\Column(name="email_credit", type="string", length=255, nullable=true)
     * @Serializer\Expose
     */
    private $emailCredit;

    /**
     * @ORM\Column(name="email_commitment", type="string", length=255, nullable=true)
     * @Serializer\Expose
     */
    private $emailCommitment;

    /**
     * @ORM\Column(name="tel_accounting", type="string", length=255, nullable=true)
     * @Serializer\Expose
     */
    private $telAccounting;

    /**
     * @ORM\Column(name="fax_accounting", type="string", length=255, nullable=true)
     * @Serializer\Expose
     */
    private $faxAccounting;

    /**
     * @ORM\Column(name="email_accounting", type="string", length=255, nullable=true)
     * @Serializer\Expose
     */
    private $emailAccounting;

        /**
     * @ORM\Column(name="tel_technical", type="string", length=255, nullable=true)
     * @Serializer\Expose
     */
    private $telTechnical;

    /**
     * @ORM\Column(name="fax_technical", type="string", length=255, nullable=true)
     * @Serializer\Expose
     */
    private $faxTechnical;

    /**
     * @ORM\Column(name="email_technical", type="string", length=255, nullable=true)
     * @Serializer\Expose
     */
    private $emailTechnical;

    /**
     * @ORM\OneToMany(targetEntity="Supplier", mappedBy="platform")
     */
    private $suppliers;

    /**
     * @ORM\OneToMany(targetEntity="ClientStatus", mappedBy="platform")
     */
    private $clientStatus;

    /**
     * @ORM\OneToMany(targetEntity="ClientFranco", mappedBy="platform")
     */
    private $clientFranco;

    /**
     * @ORM\ManyToMany(targetEntity="Sogedial\OAuthBundle\Entity\UserSuperAdmin", mappedBy="platforms", cascade={"persist"})     
     */
    private $superAdmins;

    public function __construct() {
        $this->categories = new \Doctrine\Common\Collections\ArrayCollection();
        $this->clients = new \Doctrine\Common\Collections\ArrayCollection();
        $this->products = new \Doctrine\Common\Collections\ArrayCollection();
        $this->suppliers = new \Doctrine\Common\Collections\ArrayCollection();
        $this->clientStatus = new \Doctrine\Common\Collections\ArrayCollection();
        $this->clientFranco = new \Doctrine\Common\Collections\ArrayCollection();
        $this->superAdmins = new \Doctrine\Common\Collections\ArrayCollection();
        $this->admins = new \Doctrine\Common\Collections\ArrayCollection();
        $this->attributs = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Set name.
     *
     * @param string $name
     *
     * @return Platform
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
     * @return Platform
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
     * @return Platform
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
     * @return Platform
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
     * @return Platform
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
     * @return Platform
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
     * @return Platform
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
     * Add category.
     *
     * @param \Sogedial\ApiBundle\Entity\Category $category
     *
     * @return Platform
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
     * Get categorys.
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getCategories() {
        return $this->categories;
    }

    /**
     * Add client.
     *
     * @param \Sogedial\ApiBundle\Entity\Client $client
     *
     * @return Platform
     */
    public function addClient(\Sogedial\ApiBundle\Entity\Client $client) {
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
    public function removeClient(\Sogedial\ApiBundle\Entity\Client $client) {
        return $this->clients->removeElement($client);
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
     * Add product.
     *
     * @param \Sogedial\ApiBundle\Entity\Product $product
     *
     * @return Platform
     */
    public function addProduct(\Sogedial\ApiBundle\Entity\Product $product) {
        $this->products[] = $product;

        return $this;
    }

    /**
     * Remove product.
     *
     * @param \Sogedial\ApiBundle\Entity\Product $product
     *
     * @return boolean TRUE if this collection contained the specified element, FALSE otherwise.
     */
    public function removeProduct(\Sogedial\ApiBundle\Entity\Product $product) {
        return $this->products->removeElement($product);
    }

    /**
     * Get products.
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getProducts() {
        return $this->products;
    }

    /**
     * Set dateCreate.
     *
     * @param \DateTime|null $dateCreate
     *
     * @return Platform
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
     * @return Platform
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
     * Get attribut By key.
     *
     * @return \Sogedial\ApiBundle\Entity\Attribut
     */
    public function getAttributByKey($key) {

        $attributs = $this->attributs;

        if ($attributs) {
            foreach ($attributs as $index => $value) {
                if ($value->getKey() == $key) {
                    return $value;
                }
            }
        }

        return false;
    }

    /**
     * Add supplier.
     *
     * @param \Sogedial\ApiBundle\Entity\Supplier $supplier
     *
     * @return Platform
     */
    public function addSupplier(\Sogedial\ApiBundle\Entity\Supplier $supplier) {
        $this->suppliers[] = $supplier;

        return $this;
    }

    /**
     * Remove supplier.
     *
     * @param \Sogedial\ApiBundle\Entity\Supplier $supplier
     *
     * @return boolean TRUE if this collection contained the specified element, FALSE otherwise.
     */
    public function removeSupplier(\Sogedial\ApiBundle\Entity\Supplier $supplier) {
        return $this->suppliers->removeElement($supplier);
    }

    /**
     * Get suppliers.
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getSuppliers() {
        return $this->suppliers;
    }

    /**
     * Set shortName.
     *
     * @param string|null $shortName
     *
     * @return Platform
     */
    public function setShortName($shortName = null) {
        $this->shortName = $shortName;

        return $this;
    }

    /**
     * Get shortName.
     *
     * @return string|null
     */
    public function getShortName() {
        return $this->shortName;
    }

    /**
     * Add clientStatus.
     *
     * @param \Sogedial\ApiBundle\Entity\ClientStatus $clientStatus
     *
     * @return Platform
     */
    public function addClientStatus(\Sogedial\ApiBundle\Entity\ClientStatus $clientStatus) {
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
    public function removeClientStatus(\Sogedial\ApiBundle\Entity\ClientStatus $clientStatus) {
        return $this->clientStatus->removeElement($clientStatus);
    }

    /**
     * Get clientStatus.
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getClientStatus() {
        return $this->clientStatus;
    }

    /**
     * Add clientFranco.
     *
     * @param \Sogedial\ApiBundle\Entity\ClientFranco $clientFranco
     *
     * @return Platform
     */
    public function addClientFranco(\Sogedial\ApiBundle\Entity\ClientFranco $clientFranco) {
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
    public function removeClientFranco(\Sogedial\ApiBundle\Entity\ClientFranco $clientFranco) {
        return $this->clientFranco->removeElement($clientFranco);
    }

    /**
     * Get clientFranco.
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getClientFranco() {
        return $this->clientFranco;
    }


    /**
     * Set legals.
     *
     * @param string|null $legals
     *
     * @return Platform
     */
    public function setLegals($legals = null)
    {
        $this->legals = $legals;

        return $this;
    }

    /**
     * Get legals.
     *
     * @return string|null
     */
    public function getLegals()
    {
        return $this->legals;
    }

    /**
     * Set cookiePath.
     *
     * @param string|null $cookiePath
     *
     * @return Platform
     */
    public function setCookiePath($cookiePath = null)
    {
        $this->cookiePath = $cookiePath;

        return $this;
    }

    /**
     * Get cookiePath.
     *
     * @return string|null
     */
    public function getCookiePath()
    {
        return $this->cookiePath;
    }

    /**
     * Set privacyPath.
     *
     * @param string|null $privacyPath
     *
     * @return Platform
     */
    public function setPrivacyPath($privacyPath = null)
    {
        $this->privacyPath = $privacyPath;

        return $this;
    }

    /**
     * Get privacyPath.
     *
     * @return string|null
     */
    public function getPrivacyPath()
    {
        return $this->privacyPath;
    }


    /**
     * Add superAdmin.
     *
     * @param \Sogedial\OAuthBundle\Entity\UserSuperAdmin $superAdmin
     *
     * @return Platform
     */
    public function addSuperAdmin(\Sogedial\OAuthBundle\Entity\UserSuperAdmin $superAdmin)
    {
        $this->superAdmins[] = $superAdmin;

        return $this;
    }

    /**
     * Remove superAdmin.
     *
     * @param \Sogedial\OAuthBundle\Entity\UserSuperAdmin $superAdmin
     *
     * @return boolean TRUE if this collection contained the specified element, FALSE otherwise.
     */
    public function removeSuperAdmin(\Sogedial\OAuthBundle\Entity\UserSuperAdmin $superAdmin)
    {
        return $this->superAdmins->removeElement($superAdmin);
    }

    /**
     * Get superAdmins.
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getSuperAdmins()
    {
        return $this->superAdmins;
    }

    /**
     * Set emailCredit.
     *
     * @param string|null $emailCredit
     *
     * @return Platform
     */
    public function setEmailCredit($emailCredit = null)
    {
        $this->emailCredit = $emailCredit;

        return $this;
    }

    /**
     * Get emailCredit.
     *
     * @return string|null
     */
    public function getEmailCredit()
    {
        return $this->emailCredit;
    }


    /**
     * Set emailCommitment.
     *
     * @param string|null $emailCommitment
     *
     * @return Platform
     */
    public function setEmailCommitment($emailCommitment = null)
    {
        $this->emailCommitment = $emailCommitment;

        return $this;
    }

    /**
     * Get emailCommitment.
     *
     * @return string|null
     */
    public function getEmailCommitment()
    {
        return $this->emailCommitment;
    }

    /**
     * Add attribut.
     *
     * @param \Sogedial\ApiBundle\Entity\Attribut $attribut
     *
     * @return Platform
     */
    public function addAttribut(\Sogedial\ApiBundle\Entity\Attribut $attribut)
    {
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
    public function removeAttribut(\Sogedial\ApiBundle\Entity\Attribut $attribut)
    {
        return $this->attributs->removeElement($attribut);
    }

    /**
     * Get attributs.
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getAttributs()
    {
        return $this->attributs;
    }

    /**
     * Add admin.
     *
     * @param \Sogedial\OAuthBundle\Entity\UserAdmin $admin
     *
     * @return Platform
     */
    public function addAdmin(\Sogedial\OAuthBundle\Entity\UserAdmin $admin)
    {
        $this->admins[] = $admin;

        return $this;
    }

    /**
     * Remove admin.
     *
     * @param \Sogedial\OAuthBundle\Entity\UserAdmin $admin
     *
     * @return boolean TRUE if this collection contained the specified element, FALSE otherwise.
     */
    public function removeAdmin(\Sogedial\OAuthBundle\Entity\UserAdmin $admin)
    {
        return $this->admins->removeElement($admin);
    }

    /**
     * Get admins.
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getAdmins()
    {
        return $this->admins;
    }

    /**
     * Set telAccounting.
     *
     * @param string|null $telAccounting
     *
     * @return Platform
     */
    public function setTelAccounting($telAccounting = null)
    {
        $this->telAccounting = $telAccounting;

        return $this;
    }

    /**
     * Get telAccounting.
     *
     * @return string|null
     */
    public function getTelAccounting()
    {
        return $this->telAccounting;
    }

    /**
     * Set faxAccounting.
     *
     * @param string|null $faxAccounting
     *
     * @return Platform
     */
    public function setFaxAccounting($faxAccounting = null)
    {
        $this->faxAccounting = $faxAccounting;

        return $this;
    }

    /**
     * Get faxAccounting.
     *
     * @return string|null
     */
    public function getFaxAccounting()
    {
        return $this->faxAccounting;
    }

    /**
     * Set emailAccounting.
     *
     * @param string|null $emailAccounting
     *
     * @return Platform
     */
    public function setEmailAccounting($emailAccounting = null)
    {
        $this->emailAccounting = $emailAccounting;

        return $this;
    }

    /**
     * Get emailAccounting.
     *
     * @return string|null
     */
    public function getEmailAccounting()
    {
        return $this->emailAccounting;
    }

    /**
     * Set telTechnical.
     *
     * @param string|null $telTechnical
     *
     * @return Platform
     */
    public function setTelTechnical($telTechnical = null)
    {
        $this->telTechnical = $telTechnical;

        return $this;
    }

    /**
     * Get telTechnical.
     *
     * @return string|null
     */
    public function getTelTechnical()
    {
        return $this->telTechnical;
    }

    /**
     * Set faxTechnical.
     *
     * @param string|null $faxTechnical
     *
     * @return Platform
     */
    public function setFaxTechnical($faxTechnical = null)
    {
        $this->faxTechnical = $faxTechnical;

        return $this;
    }

    /**
     * Get faxTechnical.
     *
     * @return string|null
     */
    public function getFaxTechnical()
    {
        return $this->faxTechnical;
    }

    /**
     * Set emailTechnical.
     *
     * @param string|null $emailTechnical
     *
     * @return Platform
     */
    public function setEmailTechnical($emailTechnical = null)
    {
        $this->emailTechnical = $emailTechnical;

        return $this;
    }

    /**
     * Get emailTechnical.
     *
     * @return string|null
     */
    public function getEmailTechnical()
    {
        return $this->emailTechnical;
    }
}
