<?php

namespace Sogedial\ApiBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use JMS\Serializer\Annotation as Serializer;

/**
 * Term
 *
 * @ORM\Table(name="term")
 * @ORM\Entity(repositoryClass="Sogedial\ApiBundle\Repository\TermRepository")
 * @Serializer\ExclusionPolicy("all")
 */
class Term extends AbstractEntity {

    const STATUS_ACTIVE = 'ACTIVE';
    const STATUS_INACTIVE = 'INACTIVE';

    /**
     * @ORM\ManyToMany(targetEntity="Sogedial\ApiBundle\Entity\Client", mappedBy="terms")            
     */
    private $clients;

    /**
     * @var string
     *
     * @ORM\Column(name="ext", type="string", length=255, nullable=true)
     * @Serializer\Groups({"add", "update","list", "detail"})
     * @Serializer\Expose
     */
    private $ext;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=255, nullable=true)
     * @Serializer\Groups({"add", "update","list", "detail"})
     * @Serializer\Expose
     */
    private $name;

    /**
     * @var string
     *
     * @ORM\Column(name="type", type="string", length=255, nullable=true)
     * @Serializer\Groups({"add", "update","list", "detail"})
     * @Serializer\Expose
     */
    private $type;

    /**
     * @var string|null
     *
     * @ORM\Column(name="status", type="string", length=255, nullable=true, options={"default" : TERM::STATUS_ACTIVE})
     * @Serializer\Groups({"add", "update","list", "detail"}) 
     * @Serializer\Expose
     */
    private $status;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="date_unactivated", type="datetime", nullable=true)
     * @Serializer\Groups({"add", "update","list", "detail"})     
     * @Serializer\Expose
     * @Serializer\Type("DateTime<'Y-m-d'>")
     */
    private $dateUnactivated;

    /**
     * @var string
     * @Gedmo\Slug(fields={"id","name"}, unique=true)
     * @ORM\Column(name="filename", type="string", length=255, nullable=true)
     * @Serializer\Groups({"add", "update","list", "detail"})
     * @Serializer\Expose
     */
    private $filename;

    /**
     * @var string     
     * @ORM\Column(name="title", type="string", length=255, nullable=true)
     * @Serializer\Groups({"add", "update","list", "detail"})
     * @Serializer\Expose
     */
    private $title;

    /**
     *
     * @ORM\ManyToOne(targetEntity="Sogedial\ApiBundle\Entity\Platform")
     * @ORM\JoinColumn(name="platform_id", referencedColumnName="id")
     * @Serializer\Groups({"add", "update", "detail"})
     * @Serializer\Expose
     * @Serializer\MaxDepth(1)
     */
    private $platform;

    

    
    /**
     * Constructor
     */
    public function __construct() {        
        $this->clients = new \Doctrine\Common\Collections\ArrayCollection();        
    }

    /**
     * Set ext.
     *
     * @param string $ext
     *
     * @return Term
     */
    public function setExt($ext)
    {
        $this->ext = $ext;

        return $this;
    }

    /**
     * Get ext.
     *
     * @return string
     */
    public function getExt()
    {
        return $this->ext;
    }

    /**
     * Set name.
     *
     * @param string $name
     *
     * @return Term
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name.
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set type.
     *
     * @param string $type
     *
     * @return Term
     */
    public function setType($type)
    {
        $this->type = $type;

        return $this;
    }

    /**
     * Get type.
     *
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Set filename.
     *
     * @param string $filename
     *
     * @return Term
     */
    public function setFilename($filename)
    {
        $this->filename = $filename;

        return $this;
    }

    /**
     * Get filename.
     *
     * @return string
     */
    public function getFilename()
    {
        return $this->filename;
    }

    /**
     * Set code.
     *
     * @param string|null $code
     *
     * @return Term
     */
    public function setCode($code = null)
    {
        $this->code = $code;

        return $this;
    }

    /**
     * Get code.
     *
     * @return string|null
     */
    public function getCode()
    {
        return $this->code;
    }

   

    /**
     * Set dateCreate.
     *
     * @param \DateTime|null $dateCreate
     *
     * @return Term
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
     * @return Term
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
     * Add client.
     *
     * @param \Sogedial\ApiBundle\Entity\Client $client
     *
     * @return Term
     */
    public function addClient(\Sogedial\ApiBundle\Entity\Client $client)
    {
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
    public function removeClient(\Sogedial\ApiBundle\Entity\Client $client)
    {
        return $this->clients->removeElement($client);
    }

    /**
     * Get clients.
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getClients()
    {
        return $this->clients;
    }

   /**     
     * @Serializer\VirtualProperty()
     * @Serializer\Groups({"add", "update","list", "detail"})
     */
    public function getFullFilename(){

        return $this->getFilename().'.'.$this->getExt();

    }

    /**
     * Set title.
     *
     * @param string|null $title
     *
     * @return Term
     */
    public function setTitle($title = null)
    {
        $this->title = $title;

        return $this;
    }

    /**
     * Get title.
     *
     * @return string|null
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Set platform.
     *
     * @param \Sogedial\ApiBundle\Entity\Platform|null $platform
     *
     * @return Term
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
     * @Serializer\VirtualProperty()
     * @Serializer\Groups({"add", "update","list", "detail"})
     */
    public function getPlatformId()
    {
        return ($this->getPlatform())?$this->getPlatform()->getId():null;
    }

    /**
     * @Serializer\VirtualProperty()
     * @Serializer\Groups({"add", "update","list", "detail"})
     */
    public function getCountClients()
    {
        return ($this->getClients())?$this->getClients()->count():0;
    }

    /**
     * Set status.
     *
     * @param string|null $status
     *
     * @return Term
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
     * Set dateBegin.
     *
     * @param \DateTime|null $dateBegin
     *
     * @return Term
     */
    public function setDateBegin($dateBegin = null)
    {
        $this->dateBegin = $dateBegin;

        return $this;
    }

    /**
     * Get dateBegin.
     *
     * @return \DateTime|null
     */
    public function getDateBegin()
    {
        return $this->dateBegin;
    }

    /**
     * Set dateEnd.
     *
     * @param \DateTime|null $dateEnd
     *
     * @return Term
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
     * Set dateUnactivated.
     *
     * @param \DateTime|null $dateUnactivated
     *
     * @return Term
     */
    public function setDateUnactivated($dateUnactivated = null)
    {
        $this->dateUnactivated = $dateUnactivated;

        return $this;
    }

    /**
     * Get dateUnactivated.
     *
     * @return \DateTime|null
     */
    public function getDateUnactivated()
    {
        return $this->dateUnactivated;
    }
}
