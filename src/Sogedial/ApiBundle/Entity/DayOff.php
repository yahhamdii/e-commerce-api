<?php

namespace Sogedial\ApiBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use JMS\Serializer\Annotation as Serializer;

/**
 * DayOff
 *
 * @ORM\Table(name="dayoff")
 * @ORM\Entity(repositoryClass="Sogedial\ApiBundle\Repository\DayOffRepository")
 * @Serializer\ExclusionPolicy("all")
 */
class DayOff extends AbstractEntity
{
    /**
     * @ORM\ManyToOne(targetEntity="Platform")
     * @ORM\JoinColumn(nullable=false)
     * @Serializer\Groups({"add", "update","list", "detail"})
     * @Serializer\MaxDepth(1)
     * @Serializer\Expose
     */
    private $platform;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="date", type="datetime")
     * @Serializer\Type("DateTime<'Y-m-d'>")
     * @Gedmo\Timestampable(on="create")
     * @Serializer\Groups({"add", "update","list", "detail"})
     * @Serializer\Expose
     */
    private $date;
    

    /**
     * @var string
     *
     * @ORM\Column(name="time_zone", type="string", length=45, nullable=true)
     * @Serializer\Groups({"add", "update","list", "detail"})
     * @Serializer\Expose
     */
    private $timeZone;    

    /**
     * Set date.
     *
     * @param \DateTime $date
     *
     * @return DayOff
     */
    public function setDate($date)
    {
        $this->date = $date;

        return $this;
    }

    /**
     * Get date.
     *
     * @return \DateTime
     */
    public function getDate()
    {
        return $this->date;
    }

    /**
     * Set timeZone.
     *
     * @param string|null $timeZone
     *
     * @return DayOff
     */
    public function setTimeZone($timeZone = null)
    {
        $this->timeZone = $timeZone;

        return $this;
    }

    /**
     * Get timeZone.
     *
     * @return string|null
     */
    public function getTimeZone()
    {
        return $this->timeZone;
    }

    /**
     * Set dateCreate.
     *
     * @param \DateTime|null $dateCreate
     *
     * @return DayOff
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
     * @return DayOff
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
     * Set platform.
     *
     * @param \Sogedial\ApiBundle\Entity\Platform $platform
     *
     * @return DayOff
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
}
