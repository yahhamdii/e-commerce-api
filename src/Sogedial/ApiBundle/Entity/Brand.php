<?php

namespace Sogedial\ApiBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use JMS\Serializer\Annotation as Serializer;

/**
 * Brand
 *
 * @ORM\Table(name="`brand`")
 * @ORM\Entity(repositoryClass="Sogedial\ApiBundle\Repository\BrandRepository")
 * @Serializer\ExclusionPolicy("all")
 */
class Brand extends AbstractEntity
{

    /**
     * @var string|null
     * @Serializer\Expose
     * @Serializer\Groups({"search"})
     * @ORM\Column(name="code", type="string", length=255, unique=true)
     */
    private $code;

    /**
     * @var string|null
     * @Serializer\Expose
     * @Serializer\Groups({"search"})
     * @ORM\Column(name="label", type="string", length=255, nullable=true)
     */
    private $label;

    /**
     * @ORM\Column(type="string", length=255)
     * @Serializer\Expose
     * @Serializer\Groups({"search"})
     */
    protected $name;

    /**
     * @ORM\Column(type="string", length=255, unique=true, nullable=true)
     * @Gedmo\Slug(fields={"label","id"}, unique=true)
     * @Serializer\Expose
     * @Serializer\Groups({"search"})
     */
    protected $slug;

    /**
     * @ORM\OneToMany(targetEntity="Promotion", mappedBy="brand")
     */
    private $promotions;

    /**
     * @ORM\ManyToMany(targetEntity="Client", inversedBy="brands")
     */
    private $clients;

    /**
     * @ORM\ManyToOne(targetEntity="Platform")     
     *
     */
    private $platform;

    /**
     * Set code.
     *
     * @param string|null $code
     *
     * @return Brand
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
     * Set label.
     *
     * @param string|null $label
     *
     * @return Brand
     */
    public function setLabel($label = null)
    {
        $this->label = $label;

        return $this;
    }

    /**
     * Get label.
     *
     * @return string|null
     */
    public function getLabel()
    {
        return $this->label;
    }

    /**
     * Set name.
     *
     * @param string $name
     *
     * @return Brand
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
     * Set slug.
     *
     * @param string|null $slug
     *
     * @return Brand
     */
    public function setSlug($slug = null)
    {
        $this->slug = $slug;

        return $this;
    }

    /**
     * Get slug.
     *
     * @return string|null
     */
    public function getSlug()
    {
        return $this->slug;
    }

    /**
     * Set dateCreate.
     *
     * @param \DateTime|null $dateCreate
     *
     * @return Brand
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
     * @return Brand
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
     * Constructor
     */
    public function __construct()
    {
        $this->promotions = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Add promotion.
     *
     * @param \Sogedial\ApiBundle\Entity\Promotion $promotion
     *
     * @return Brand
     */
    public function addPromotion(\Sogedial\ApiBundle\Entity\Promotion $promotion)
    {
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
    public function removePromotion(\Sogedial\ApiBundle\Entity\Promotion $promotion)
    {
        return $this->promotions->removeElement($promotion);
    }

    /**
     * Get promotions.
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getPromotions()
    {
        return $this->promotions;
    }

    /**
     * Add client.
     *
     * @param \Sogedial\ApiBundle\Entity\Client $client
     *
     * @return Brand
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
     * Set platform.
     *
     * @param \Sogedial\ApiBundle\Entity\Platform|null $platform
     *
     * @return Brand
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
}
