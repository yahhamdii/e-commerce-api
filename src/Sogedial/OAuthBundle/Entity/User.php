<?php

namespace Sogedial\OAuthBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

use Sogedial\ApiBundle\Entity\AbstractEntity;

use JMS\Serializer\Annotation as Serializer;

/**
 * User
 *
 * @ORM\Table(name="user")
 * @ORM\Entity(repositoryClass="Sogedial\OAuthBundle\Repository\UserRepository")
 * @ORM\InheritanceType("JOINED")
 * @ORM\DiscriminatorColumn(name="type", type="string")
 * @ORM\DiscriminatorMap( {"user" = "User", "customer" = "UserCustomer", "commercial" = "UserCommercial", "admin" = "UserAdmin", "superAdmin" = "UserSuperAdmin" } )
 * @Serializer\ExclusionPolicy("all")
 */
class User extends AbstractEntity implements UserInterface {


    /**
     * @ORM\Column(type="string", length=255, unique=true)
     * @Serializer\Expose
     * @Serializer\Groups({"search"})
     */
    protected $username;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Serializer\Expose
     */
    protected $email;

    /**
     * @ORM\Column(type="string", length=4096)
     */
    protected $password;

    /**
     * Plain password. Used for model validation. Must not be persisted.     
     *
     */
    protected $plainPassword;

    /**
     * @ORM\Column(type="boolean", options={"default": 0 })
     * @Serializer\Expose
     */
    protected $enabled;

    /**
     * @ORM\Column(type="string", length=4096)
     * @Serializer\Expose
     */
    protected $salt;

    /**
     * @ORM\Column(type="array")
     * @Serializer\Expose
     */
    protected $roles;

    /**
     * @ORM\Column(name="last_login", type="datetime", nullable=true)
     * @Serializer\Expose
     */
    protected $lastLogin;

    /**
     * Random string sent to the user email address in order to verify it.
     *
     * @ORM\Column(name="confirmation_token", type="string", length=4096, nullable=true)
     * @Serializer\Expose
     */
    protected $confirmationToken;

    /**
     * @ORM\Column(name="password_requested_at", type="datetime", nullable=true)
     * @Serializer\Expose
     */
    protected $passwordRequestedAt;

    /* Safo datas */

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Serializer\Expose
     */
    private $lastname;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Serializer\Expose
     */
    private $firstname;

    /**
     * @var string
     *
     * @ORM\Column(name="country_sale", type="string", length=255, nullable=true)
     * @Serializer\Expose
     */
    private $countrySale;

    /**
     * @var integer
     *
     * @ORM\Column(name="turnover", type="string", length=255, nullable=true)
     * @Serializer\Expose
     */
    private $turnover;

    /**
     * @var string
     *
     * @ORM\Column(name="job_position", type="string", length=255, nullable=true)
     * @Serializer\Expose
     */
    private $jobPosition;

    /**
     * @var string
     *
     * @ORM\Column(name="tel_number1", type="string", length=255, nullable=true)
     * @Serializer\Expose
     */
    private $telNumber1;

    /**
     * @var string
     *
     * @ORM\Column(name="tel_number2", type="string", length=255, nullable=true)
     * @Serializer\Expose
     */
    private $telNumber2;

    /**
     * @var string
     *
     * @ORM\Column(name="fax", type="string", length=255, nullable=true)
     * @Serializer\Expose
     */
    private $fax;

    /**
     * @var Datetime
     *
     * @ORM\Column(name="cgv_cpv_signed_at", type="datetime", nullable=true)
     * @Serializer\Expose
     */
    private $cgvCpvSignedAt;

    /**
     * @var Datetime
     *
     * @ORM\Column(name="cgv_cpv_updated_at", type="datetime", nullable=true)
     * @Serializer\Expose
     */
    private $cgvCpvUpdatedAt;

    /**
     * @var boolean
     *
     * @ORM\Column(name="already_signed", type="boolean", nullable=true)
     * @Serializer\Expose
     */
    private $alreadySigned;

    /**
     * @var boolean
     *
     * @ORM\Column(name="flag_franco", type="boolean", nullable=true)
     * @Serializer\Expose
     */
    private $flagFranco;

    /**
     * @var float
     *
     * @ORM\Column(name="amount_franco", type="float", nullable=true )
     * @Serializer\Expose
     */
    private $amountFranco;

    /**
     * @var Datetime
     *
     * @ORM\Column(name="validity_begin_date", type="datetime", nullable=true)
     * @Serializer\Expose
     */
    private $validityBeginDate;

    /**
     * @var Datetime
     *
     * @ORM\Column(name="validity_end_date", type="datetime", nullable=true)
     * @Serializer\Expose
     */
    private $validityEndDate;

    /**
     * @var boolean
     *
     * @ORM\Column(name="first_visit", type="boolean", nullable=true, options={"default": 0 })
     * @Serializer\Expose
     */
    private $firstVisit;

    
    public function __toString()
    {
        return (string) $this->getUsername();
    }


    /**
     * Set username.
     *
     * @param string $username
     *
     * @return User
     */
    public function setUsername($username) {
        $this->username = $username;

        return $this;
    }

    /**
     * Get username.
     *
     * @return string
     */
    public function getUsername() {
        return $this->username;
    }

    /**
     * Set email.
     *
     * @param string|null $email
     *
     * @return User
     */
    public function setEmail($email = null) {
        $this->email = $email;

        return $this;
    }

    /**
     * Get email.
     *
     * @return string|null
     */
    public function getEmail() {
        return $this->email;
    }

    /**
     * Set password.
     *
     * @param string $password
     *
     * @return User
     */
    public function setPassword($password) {
        $this->password = $password;

        return $this;
    }

    /**
     * Get password.
     *
     * @return string
     */
    public function getPassword() {
        return $this->password;
    }

    /**
     * Set enabled.
     *
     * @param bool|null $enabled
     *
     * @return User
     */
    public function setEnabled($enabled = null) {
        $this->enabled = $enabled;

        return $this;
    }

    /**
     * Get enabled.
     *
     * @return bool|null
     */
    public function getEnabled() {
        return ($this->enabled == null)?false:$this->enabled;
    }

    /**
     * Set salt.
     *
     * @param string $salt
     *
     * @return User
     */
    public function setSalt($salt) {
        $this->salt = $salt;

        return $this;
    }

    /**
     * Get salt.
     *
     * @return string
     */
    public function getSalt() {
        return $this->salt;
    }


    /**
     * Set lastLogin.
     *
     * @param \DateTime|null $lastLogin
     *
     * @return User
     */
    public function setLastLogin(\DateTime $lastLogin = null) {
        $this->lastLogin = $lastLogin;

        return $this;
    }

    /**
     * Get lastLogin.
     *
     * @return \DateTime|null
     */
    public function getLastLogin() {
        return $this->lastLogin;
    }

    /**
     * Set confirmationToken.
     *
     * @param string|null $confirmationToken
     *
     * @return User
     */
    public function setConfirmationToken($confirmationToken = null) {
        $this->confirmationToken = $confirmationToken;

        return $this;
    }

    /**
     * Get confirmationToken.
     *
     * @return string|null
     */
    public function getConfirmationToken() {
        return $this->confirmationToken;
    }

    /**
     * Set passwordRequestedAt.
     *
     * @param \DateTime|null $passwordRequestedAt
     *
     * @return User
     */
    public function setPasswordRequestedAt(\DateTime $passwordRequestedAt = NULL) {
        $this->passwordRequestedAt = $passwordRequestedAt;

        return $this;
    }

    /**
     * Get passwordRequestedAt.
     *
     * @return \DateTime|null
     */
    public function getPasswordRequestedAt() {
        return $this->passwordRequestedAt;
    }

    /**
     * Set lastname.
     *
     * @param string|null $lastname
     *
     * @return User
     */
    public function setLastname($lastname = null) {
        $this->lastname = $lastname;

        return $this;
    }

    /**
     * Get lastname.
     *
     * @return string|null
     */
    public function getLastname() {
        return $this->lastname;
    }

    /**
     * Set firstname.
     *
     * @param string|null $firstname
     *
     * @return User
     */
    public function setFirstname($firstname = null) {
        $this->firstname = $firstname;

        return $this;
    }

    /**
     * Get firstname.
     *
     * @return string|null
     */
    public function getFirstname() {
        return $this->firstname;
    }

    /**
     * Set countrySale.
     *
     * @param string|null $countrySale
     *
     * @return User
     */
    public function setCountrySale($countrySale = null) {
        $this->countrySale = $countrySale;

        return $this;
    }

    /**
     * Get countrySale.
     *
     * @return string|null
     */
    public function getCountrySale() {
        return $this->countrySale;
    }

    /**
     * Set turnover.
     *
     * @param string|null $turnover
     *
     * @return User
     */
    public function setTurnover($turnover = null) {
        $this->turnover = $turnover;

        return $this;
    }

    /**
     * Get turnover.
     *
     * @return string|null
     */
    public function getTurnover() {
        return $this->turnover;
    }

    /**
     * Set jobPosition.
     *
     * @param string|null $jobPosition
     *
     * @return User
     */
    public function setJobPosition($jobPosition = null) {
        $this->jobPosition = $jobPosition;

        return $this;
    }

    /**
     * Get jobPosition.
     *
     * @return string|null
     */
    public function getJobPosition() {
        return $this->jobPosition;
    }

    /**
     * Set telNumber1.
     *
     * @param string|null $telNumber1
     *
     * @return User
     */
    public function setTelNumber1($telNumber1 = null) {
        $this->telNumber1 = $telNumber1;

        return $this;
    }

    /**
     * Get telNumber1.
     *
     * @return string|null
     */
    public function getTelNumber1() {
        return $this->telNumber1;
    }

    /**
     * Set telNumber2.
     *
     * @param string|null $telNumber2
     *
     * @return User
     */
    public function setTelNumber2($telNumber2 = null) {
        $this->telNumber2 = $telNumber2;

        return $this;
    }

    /**
     * Get telNumber2.
     *
     * @return string|null
     */
    public function getTelNumber2() {
        return $this->telNumber2;
    }

    /**
     * Set fax.
     *
     * @param string|null $fax
     *
     * @return User
     */
    public function setFax($fax = null) {
        $this->fax = $fax;

        return $this;
    }

    /**
     * Get fax.
     *
     * @return string|null
     */
    public function getFax() {
        return $this->fax;
    }
    
    /**
     * Set cgvCpvSignedAt.
     *
     * @param \DateTime|null $cgvCpvSignedAt
     *
     * @return User
     */
    public function setCgvCpvSignedAt($cgvCpvSignedAt = null) {
        $this->cgvCpvSignedAt = $cgvCpvSignedAt;

        return $this;
    }

    /**
     * Get cgvCpvSignedAt.
     *
     * @return \DateTime|null
     */
    public function getCgvCpvSignedAt() {
        return $this->cgvCpvSignedAt;
    }

    /**
     * Set cgvCpvUpdatedAt.
     *
     * @param \DateTime|null $cgvCpvUpdatedAt
     *
     * @return User
     */
    public function setCgvCpvUpdatedAt($cgvCpvUpdatedAt = null) {
        $this->cgvCpvUpdatedAt = $cgvCpvUpdatedAt;

        return $this;
    }

    /**
     * Get cgvCpvUpdatedAt.
     *
     * @return \DateTime|null
     */
    public function getCgvCpvUpdatedAt() {
        return $this->cgvCpvUpdatedAt;
    }

    /**
     * Set alreadySigned.
     *
     * @param bool|null $alreadySigned
     *
     * @return User
     */
    public function setAlreadySigned($alreadySigned = null) {
        $this->alreadySigned = $alreadySigned;

        return $this;
    }

    /**
     * Get alreadySigned.
     *
     * @return bool|null
     */
    public function getAlreadySigned() {
        return $this->alreadySigned;
    }

    /**
     * Set flagFranco.
     *
     * @param bool|null $flagFranco
     *
     * @return User
     */
    public function setFlagFranco($flagFranco = null) {
        $this->flagFranco = $flagFranco;

        return $this;
    }

    /**
     * Get flagFranco.
     *
     * @return bool|null
     */
    public function getFlagFranco() {
        return $this->flagFranco;
    }

    /**
     * Set amountFranco.
     *
     * @param float|null $amountFranco
     *
     * @return User
     */
    public function setAmountFranco($amountFranco = null) {
        $this->amountFranco = $amountFranco;

        return $this;
    }

    /**
     * Get amountFranco.
     *
     * @return float|null
     */
    public function getAmountFranco() {
        return $this->amountFranco;
    }

    /**
     * Set validityBeginDate.
     *
     * @param \DateTime|null $validityBeginDate
     *
     * @return User
     */
    public function setValidityBeginDate($validityBeginDate = null) {
        $this->validityBeginDate = $validityBeginDate;

        return $this;
    }

    /**
     * Get validityBeginDate.
     *
     * @return \DateTime|null
     */
    public function getValidityBeginDate() {
        return $this->validityBeginDate;
    }

    /**
     * Set validityEndDate.
     *
     * @param \DateTime|null $validityEndDate
     *
     * @return User
     */
    public function setValidityEndDate($validityEndDate = null) {
        $this->validityEndDate = $validityEndDate;

        return $this;
    }

    /**
     * Get validityEndDate.
     *
     * @return \DateTime|null
     */
    public function getValidityEndDate() {
        return $this->validityEndDate;
    }

    /**
     * Set firstVisit.
     *
     * @param bool|null $firstVisit
     *
     * @return User
     */
    public function setFirstVisit($firstVisit = null) {
        $this->firstVisit = $firstVisit;

        return $this;
    }

    /**
     * Get firstVisit.
     *
     * @return bool|null
     */
    public function getFirstVisit() {
        return $this->firstVisit;
    }

    public function addRole($role)
    {
        $role = strtoupper($role);
        if ($role === static::ROLE_DEFAULT) {
            return $this;
        }
        if (!is_array($this->roles) || !in_array($role, $this->roles, true)) {
            $this->roles[] = $role;
        }
        return $this;
    }

    public function getRoles()
    {
        $roles = $this->roles;
        // we need to make sure to have at least one role
        $roles[] = static::ROLE_DEFAULT;
        return array_unique($roles);
    }


    public function setRoles(array $roles)
    {
        $this->roles = array();
        foreach ($roles as $role) {
            $this->addRole($role);
        }
        return $this;
    }


    public function removeRole($role)
    {
        if (false !== $key = array_search(strtoupper($role), $this->roles, true)) {
            unset($this->roles[$key]);
            $this->roles = array_values($this->roles);
        }
        return $this;
    }

     public function eraseCredentials()
    {
        $this->plainPassword = null;
    }

    public function getPlainPassword(): string {
        return $this->plainPassword;
    }

    public function setPlainPassword($password) {
        $this->plainPassword=$password;
        return $this;
    }

    public function isAccountNonExpired(): bool {
        return $this->enabled;
        
    }

    public function isAccountNonLocked(): bool {
        return true;
    }

    public function isCredentialsNonExpired(): bool {
        return true;
        
    }

    public function isEnabled(): bool {
        return $this->enabled;
    }

    public function isPasswordRequestNonExpired($ttl)
    {
        return $this->getPasswordRequestedAt() instanceof \DateTime &&
               $this->getPasswordRequestedAt()->getTimestamp() + $ttl > time();
    }


    public function isSuperAdmin()
    {
        return $this->hasRole(static::ROLE_SUPER_ADMIN);
    }


    public function setSuperAdmin($boolean)
    {
        if (true === $boolean) {
            $this->addRole(static::ROLE_SUPER_ADMIN);
        } else {
            $this->removeRole(static::ROLE_SUPER_ADMIN);
        }
        return $this;
    }





    public function serialize()
    {
        return serialize(array(
            $this->password,
            $this->salt,
            $this->username,
            $this->enabled,
            $this->id,
            $this->email,
        ));
    }

    public function unserialize($serialized)
    {
        $data = unserialize($serialized);

        list(
            $this->password,
            $this->salt,
            $this->username,
            $this->enabled,
            $this->id,
            $this->email
        ) = $data;
    }


    public function hasRole($role)
    {
        return in_array(strtoupper($role), $this->getRoles(), true);
    }


    /**
     * Set dateCreate
     *
     * @param \DateTime $dateCreate
     *
     * @return User
     */
    public function setDateCreate($dateCreate)
    {
        $this->dateCreate = $dateCreate;

        return $this;
    }

    /**
     * Get dateCreate
     *
     * @return \DateTime
     */
    public function getDateCreate()
    {
        return $this->dateCreate;
    }

    /**
     * Set dateUpdate
     *
     * @param \DateTime $dateUpdate
     *
     * @return User
     */
    public function setDateUpdate($dateUpdate)
    {
        $this->dateUpdate = $dateUpdate;

        return $this;
    }

    /**
     * Get dateUpdate
     *
     * @return \DateTime
     */
    public function getDateUpdate()
    {
        return $this->dateUpdate;
    }
}
