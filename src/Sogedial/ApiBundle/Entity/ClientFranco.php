<?php

namespace Sogedial\ApiBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as Serializer;

/**
 * ClientFranco
 *
 * @ORM\Table(name="client_franco")
 * @ORM\Entity(repositoryClass="Sogedial\ApiBundle\Repository\ClientFrancoRepository")
 * @Serializer\ExclusionPolicy("all")
 */
class ClientFranco extends AbstractEntity
{

    const STATUS_BLOCKED = 'BLOCKED';
    const STATUS_NORMAL = 'NORMAL';


    /**
     * @ORM\ManyToOne(targetEntity="Sogedial\ApiBundle\Entity\Client", inversedBy="clientFranco")
     * @ORM\JoinColumn(nullable=false)
     * @Serializer\MaxDepth(2)
     * @Serializer\Expose()
     */
    private $client;


    /**
     * @ORM\ManyToOne(targetEntity="Sogedial\ApiBundle\Entity\Platform", inversedBy="clientFranco")
     * @ORM\JoinColumn(nullable=false)
     * @Serializer\MaxDepth(2)
     * @Serializer\Expose()
     */
    private $platform;


    /**
     * @var string|null
     *
     * @ORM\Column(type="decimal", precision=8, scale=2, nullable=true)
     * @Serializer\Expose()
     */
    private $amount;

    
    /**
     * @var string|null
     *
     * @ORM\Column(name="status", type="string", length=255, nullable=true, options={"default" : ClientFranco::STATUS_NORMAL})
     * @Serializer\Expose()
     */
    private $status;


        
    /**
     * @var string|null
     *
     * @ORM\Column(name="temperature", type="string", length=255, nullable=true)
     * @Serializer\Expose()
     */
    private $temperature;


    /**
     * @ORM\Column(name="enable" ,type="boolean" , options={"default" : false})
     * @Serializer\Expose()
     */
    private $enable;

    /**
     * Get id.
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }


    /**
     * Set client.
     *
     * @param \Sogedial\ApiBundle\Entity\Client $client
     *
     * @return ClientFranco
     */
    public function setClient(\Sogedial\ApiBundle\Entity\Client $client)
    {
        $this->client = $client;

        return $this;
    }

    /**
     * Get client.
     *
     * @return \Sogedial\ApiBundle\Entity\Client
     */
    public function getClient()
    {
        return $this->client;
    }

    /**
     * Set platform.
     *
     * @param \Sogedial\ApiBundle\Entity\Platform $platform
     *
     * @return ClientFranco
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
     * @Serializer\VirtualProperty()
     */
    public function getPlatformId()
    {
        return ($this->getPlatform())?$this->getPlatform()->getId():null;
    }

    /**
     * Set amount.
     *
     * @param string|null $amount
     *
     * @return ClientFranco
     */
    public function setAmount($amount = null)
    {
        $this->amount = $amount;

        return $this;
    }

    /**
     * Get amount.
     *
     * @return string|null
     */
    public function getAmount()
    {
        return $this->amount;
    }

    /**
     * Set dateCreate.
     *
     * @param \DateTime|null $dateCreate
     *
     * @return ClientFranco
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
     * @return ClientFranco
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
     * Set status.
     *
     * @param string|null $status
     *
     * @return ClientFranco
     */
    public function setStatus($status = null)
    {
        $this->status = $status;

        return $this;
    }

    /**
     * Get status.
     *
     * @return string|null
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * Set temperature.
     *
     * @param string|null $temperature
     *
     * @return ClientFranco
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
     * Set enable.
     *
     * @param bool $enable
     *
     * @return ClientFranco
     */
    public function setEnable($enable)
    {
        $this->enable = $enable;

        return $this;
    }

    /**
     * Get enable.
     *
     * @return bool
     */
    public function getEnable()
    {
        return $this->enable;
    }
}
