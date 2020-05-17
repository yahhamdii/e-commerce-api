<?php

namespace Sogedial\ApiBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Doctrine\Common\Collections\ArrayCollection;
use JMS\Serializer\Annotation\ExclusionPolicy;
use JMS\Serializer\Annotation\Expose;
use JMS\Serializer\Annotation\VirtualProperty;
use JMS\Serializer\Annotation\Groups;


/**
 * Group
 *
 * @ORM\Table(name="`group`")
 * @ORM\Entity(repositoryClass="Sogedial\ApiBundle\Repository\GroupRepository")
 * @ORM\InheritanceType("JOINED")
 * @ORM\DiscriminatorColumn(name="type", type="string")
 * @ORM\DiscriminatorMap({"client" = "GroupClient", "item" = "GroupItem", "group" = "Group"})
 * @ExclusionPolicy("all")
 */
class Group extends AbstractEntity
{

    /**
     * @var string|null
     * @Expose
     * @ORM\Column(name="code", type="string", length=255, nullable=true)
     * @Groups({"add", "update","list", "detail", "listitems"})
     */
    private $code;

    /**
     * @var string|null
     * @Expose
     * @ORM\Column(name="label", type="string", length=255, nullable=true)
     * @Groups({"add", "update","list", "detail",  "listitems"})
     */
    private $label;

    /**
     * @ORM\Column(type="string", length=255)
     * @Expose
     * @Groups({"add", "update","list", "detail","search", "listitems"})
     */
    protected $name;

    /**
     * @ORM\Column(type="string", length=255, unique=true, nullable=true)
     * @Gedmo\Slug(fields={"label","id", "status"}, unique=true)
     * @Expose
     * @Groups({"add", "update","list", "detail", "listitems"})
     */
    protected $slug;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Groups({"add", "update","list", "detail", "listitems"})
     * @Expose
     */
    protected $status;


    /**
     * @ORM\ManyToOne(targetEntity="Sogedial\ApiBundle\Entity\Pricing", inversedBy="groups")
     * @ORM\JoinColumn(nullable= true)
     */
    private $pricing;



    /**
     * Set name.
     *
     * @param string $name
     *
     * @return Group
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
     * @param string $slug
     *
     * @return Group
     */
    public function setSlug($slug)
    {
        $this->slug = $slug;

        return $this;
    }

    /**
     * Get slug.
     *
     * @return string
     */
    public function getSlug()
    {
        return $this->slug;
    }

    /**
     * Set code
     *
     * @param string $code
     *
     * @return Group
     */
    public function setCode($code)
    {
        $this->code = $code;

        return $this;
    }

    /**
     * Get code
     *
     * @return string
     */
    public function getCode()
    {
        return $this->code;
    }

    /**
     * Set label
     *
     * @param string $label
     *
     * @return Group
     */
    public function setLabel($label)
    {
        $this->label = $label;

        return $this;
    }

    /**
     * Get label
     *
     * @return string
     */
    public function getLabel()
    {
        return $this->label;
    }

    /**
     * Set dateCreate.
     *
     * @param \DateTime $dateCreate
     *
     * @return Group
     */
    public function setDateCreate($dateCreate)
    {
        $this->dateCreate = $dateCreate;

        return $this;
    }

    /**
     * Get dateCreate.
     *
     * @return \DateTime
     */
    public function getDateCreate()
    {
        return $this->dateCreate;
    }

    /**
     * Set dateUpdate.
     *
     * @param \DateTime $dateUpdate
     *
     * @return Group
     */
    public function setDateUpdate($dateUpdate)
    {
        $this->dateUpdate = $dateUpdate;

        return $this;
    }

    /**
     * Get dateUpdate.
     *
     * @return \DateTime
     */
    public function getDateUpdate()
    {
        return $this->dateUpdate;
    }

    /**
     * Set pricing.
     *
     * @param \Sogedial\ApiBundle\Entity\Pricing|null $pricing
     *
     * @return Group
     */
    public function setPricing(\Sogedial\ApiBundle\Entity\Pricing $pricing = null)
    {
        $this->pricing = $pricing;

        return $this;
    }

    /**
     * Get pricing.
     *
     * @return \Sogedial\ApiBundle\Entity\Pricing|null
     */
    public function getPricing()
    {
        return $this->pricing;
    }
}
