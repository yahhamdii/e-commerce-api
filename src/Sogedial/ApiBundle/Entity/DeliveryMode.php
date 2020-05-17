<?php

namespace Sogedial\ApiBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * DeliveryMode
 *
 * @ORM\Table(name="delivery_mode")
 * @ORM\Entity(repositoryClass="Sogedial\ApiBundle\Repository\DeliveryModeRepository")
 */
class DeliveryMode extends AbstractEntity
{

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=255)
     */
    private $name;


    /**
     * Set name.
     *
     * @param string $name
     *
     * @return DeliveryMode
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
     * Set dateCreate.
     *
     * @param \DateTime|null $dateCreate
     *
     * @return DeliveryMode
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
     * @return DeliveryMode
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
