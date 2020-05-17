<?php

namespace Sogedial\ApiBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as Serializer;

/**
 * ClientStatus
 *
 * @ORM\Table(name="client_status", uniqueConstraints={ @ORM\UniqueConstraint(name="client_platform_unique", columns={"client_id", "platform_id"})})
 * @ORM\Entity(repositoryClass="Sogedial\ApiBundle\Repository\ClientStatusRepository")
 * @Serializer\ExclusionPolicy("all")
 */
class ClientStatus extends AbstractEntity
{

    const STATUS_ACTIVE = 'ACTIVE';
    const STATUS_INACTIVE = 'INACTIVE';
    const STATUS_BANISH = 'BANISH';
    const STATUS_BLOCKED = 'BLOCKED';
    const STATUS_PREORDER_ACTIVE = 'ACTIVE';
    const STATUS_PREORDER_INACTIVE = 'INACTIVE';
    const STATUS_PREORDER_BLOCKED = 'BLOCKED';
    const STATUS_PREORDER_PARTIAL = 'PARTIAL';
    const STATUS_CATALOG_ACTIVE = 'ACTIVE';
    const STATUS_CATALOG_INACTIVE = 'INACTIVE';
    const STATUS_CATALOG_BLOCKED = 'BLOCKED';
    const STATUS_CATALOG_PARTIAL = 'PARTIAL';



    /**
     * @ORM\ManyToOne(targetEntity="Sogedial\ApiBundle\Entity\Client", inversedBy="clientStatus")
     * @ORM\JoinColumn(nullable=false)
     * @Serializer\MaxDepth(2)
     * @Serializer\Expose()
     */
    private $client;


    /**
     * @ORM\ManyToOne(targetEntity="Sogedial\ApiBundle\Entity\Platform", inversedBy="clientStatus")
     * @ORM\JoinColumn(nullable=false)
     * @Serializer\MaxDepth(2)
     * @Serializer\Expose()
     * @Serializer\Groups({"search"})
     */
    private $platform;


    /**
     * @var string|null
     *
     * @ORM\Column(name="status_preorder", type="string", length=255, nullable=true)
     * @Serializer\Expose()
     * @Serializer\Groups({"search"})
     */
    private $statusPreorder = self::STATUS_PREORDER_BLOCKED;


    /**
     * @var string|null
     *
     * @ORM\Column(name="status_catalog", type="string", length=255, nullable=true)
     * @Serializer\Expose
     * @Serializer\Groups({"search"})
     */
    private $statusCatalog = self::STATUS_CATALOG_BLOCKED;


    /**
     * @var string|null
     *
     * @ORM\Column(name="status", type="string", length=255, nullable=true)
     * @Serializer\Expose
     */
    private $status = self::STATUS_ACTIVE;


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
     * Set statusPreorder.
     *
     * @param string|null $statusPreorder
     *
     * @return ClientStatus
     */
    public function setStatusPreorder($statusPreorder = null)
    {
        $this->statusPreorder = $statusPreorder;

        return $this;
    }

    /**
     * Get statusPreorder.
     *
     * @return string|null
     */
    public function getStatusPreorder()
    {

        return $this->statusPreorder;
    }

    /**
     * Set statusCatalog.
     *
     * @param string|null $statusCatalog
     *
     * @return ClientStatus
     */
    public function setStatusCatalog($statusCatalog = null)
    {
        $this->statusCatalog = $statusCatalog;

        return $this;
    }

    /**
     * Get statusCatalog.
     *
     * @return string|null
     */
    public function getStatusCatalog()
    {
        return $this->statusCatalog;
    }

    /**
     * Set client.
     *
     * @param \Sogedial\ApiBundle\Entity\Client $client
     *
     * @return ClientStatus
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
     * @return ClientStatus
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
     * Set status.
     *
     * @param string|null $status
     *
     * @return ClientStatus
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
     * Set dateCreate.
     *
     * @param \DateTime|null $dateCreate
     *
     * @return ClientStatus
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
     * @return ClientStatus
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
}
