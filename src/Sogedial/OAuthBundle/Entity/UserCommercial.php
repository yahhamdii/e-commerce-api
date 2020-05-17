<?php

namespace Sogedial\OAuthBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as Serializer;

/**
 * UserCommercial
 *
 * @ORM\Table(name="user_commercial")
 * @ORM\Entity()
 * @Serializer\ExclusionPolicy("all")
 */
class UserCommercial extends User {
    

    /**
     *
     * @ORM\ManyToMany(targetEntity="\Sogedial\ApiBundle\Entity\Client", mappedBy="commercials")     
     */
    protected $clients;

    /**
     *
     * @ORM\ManyToOne(targetEntity="Sogedial\ApiBundle\Entity\Platform")
     * @ORM\JoinColumn(name="platform_id", referencedColumnName="id")
     * @Serializer\Expose
     * @Serializer\MaxDepth(3)
     */
    private $platform;

    /**
     * @var int
     *
     * @ORM\Column(name="year", type="integer", nullable= true)
     * @Serializer\Expose
     */
    private $year;

    /**
     * @var int
     *
     * @ORM\Column(name="month", type="integer", nullable= true)
     * @Serializer\Expose
     */
    private $month;

    /**
     * @var float
     *
     * @ORM\Column(name="monthly_turnover", type="float", nullable= true)
     * @Serializer\Expose
     */
    private $monthlyTurnover;

    /**
     * @var float
     *
     * @ORM\Column(name="month_margin", type="float", nullable= true)
     * @Serializer\Expose
     */
    private $monthMargin;

    /**
     * @var float
     *
     * @ORM\Column(name="goal_turnover", type="float", nullable= true)
     * @Serializer\Expose
     */
    private $goalTurnover;

    /**
     * @var float
     *
     * @ORM\Column(name="goal_store", type="float", nullable= true)
     * @Serializer\Expose
     */
    private $goalStore;

    /**
     * Constructor
     */
    public function __construct() {
        $this->clients = new \Doctrine\Common\Collections\ArrayCollection();
    }

    public function setDefaultRoles() {

        $this->addRole(User::ROLE_COMMERCIAL);

        return $this;
    }

    /**
     * Add client.
     *
     * @param \Sogedial\ApiBundle\Entity\Client $client
     *
     * @return UserCommercial
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
     * Set dateCreate.
     *
     * @param \DateTime|null $dateCreate
     *
     * @return UserCommercial
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
     * @return UserCommercial
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
     * Set platform.
     *
     * @param \Sogedial\ApiBundle\Entity\Platform|null $platform
     *
     * @return UserCommercial
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
     * Set year.
     *
     * @param int $year
     *
     * @return UserCommercial
     */
    public function setYear($year)
    {
        $this->year = $year;

        return $this;
    }

    /**
     * Get year.
     *
     * @return int
     */
    public function getYear()
    {
        return $this->year;
    }

    /**
     * Set month.
     *
     * @param int $month
     *
     * @return UserCommercial
     */
    public function setMonth($month)
    {
        $this->month = $month;

        return $this;
    }

    /**
     * Get month.
     *
     * @return int
     */
    public function getMonth()
    {
        return $this->month;
    }

    /**
     * Set monthlyTurnover.
     *
     * @param float $monthlyTurnover
     *
     * @return UserCommercial
     */
    public function setMonthlyTurnover($monthlyTurnover)
    {
        $this->monthlyTurnover = $monthlyTurnover;

        return $this;
    }

    /**
     * Get monthlyTurnover.
     *
     * @return float
     */
    public function getMonthlyTurnover()
    {
        return $this->monthlyTurnover;
    }

    /**
     * Set monthMargin.
     *
     * @param float $monthMargin
     *
     * @return UserCommercial
     */
    public function setMonthMargin($monthMargin)
    {
        $this->monthMargin = $monthMargin;

        return $this;
    }

    /**
     * Get monthMargin.
     *
     * @return float
     */
    public function getMonthMargin()
    {
        return $this->monthMargin;
    }

    /**
     * Set goalTurnover.
     *
     * @param float $goalTurnover
     *
     * @return UserCommercial
     */
    public function setGoalTurnover($goalTurnover)
    {
        $this->goalTurnover = $goalTurnover;

        return $this;
    }

    /**
     * Get goalTurnover.
     *
     * @return float
     */
    public function getGoalTurnover()
    {
        return $this->goalTurnover;
    }

    /**
     * Set goalStore.
     *
     * @param float $goalStore
     *
     * @return UserCommercial
     */
    public function setGoalStore($goalStore)
    {
        $this->goalStore = $goalStore;

        return $this;
    }

    /**
     * Get goalStore.
     *
     * @return float
     */
    public function getGoalStore()
    {
        return $this->goalStore;
    }

     /**
     * @Serializer\VirtualProperty()
     */
    public function getCode() {
        return substr($this->getUsername(), strrpos($this->getUsername(), '-') + 1); 
    }
}
