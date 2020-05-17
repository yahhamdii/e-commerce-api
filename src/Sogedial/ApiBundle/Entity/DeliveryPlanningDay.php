<?php

namespace Sogedial\ApiBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as Serializer;

/**
 * DeliveryPlanningDay
 *
 * @ORM\Table(name="delivery_planning_day")
 * @ORM\Entity(repositoryClass="Sogedial\ApiBundle\Repository\DeliveryPlanningDayRepository")
 */
class DeliveryPlanningDay extends AbstractEntity
{
    //TODO check type of weekDay field

    /**
     *
     * @ORM\Column(name="week_day", type="string", length=45)
     * @Serializer\Groups({"add", "update", "list", "detail"})
     * @Serializer\Expose
     */
    private $weekDay;
	
	
	/**
     *
     * @ORM\Column(name="hour", type="string", nullable=true, length=45)
     * @Serializer\Groups({"add", "update", "list", "detail"})
     * @Serializer\Expose
     */
    private $hour;


    /**
     * @var \DateTime
     *
     * @ORM\Column(name="begin", type="time", nullable=true, options={"default" : "00:00:00"})
     * @Serializer\Expose
     * @Serializer\Groups({"add", "update", "list", "detail"})
     * @Serializer\Type("DateTime<'H:i:s'>")
     */
    private $begin;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="end", type="time", nullable=true, options={"default" : "23:59:59"})
     * @Serializer\Type("DateTime<'H:i:s'>")
     * @Serializer\Groups({"add", "update", "list", "detail"})
     * @Serializer\Expose
     */
    private $end;

    /**
     * @ORM\ManyToOne(targetEntity="DeliveryPlanning", inversedBy="days", cascade={"persist", "remove", "merge"})
     */
    private $deliveryPlanning;


    /**
     * Set begin.
     *
     * @param \DateTime $begin
     *
     * @return DeliveryPlanningDay
     */
    public function setBegin($begin)
    {
        $this->begin = $begin;

        return $this;
    }

    /**
     * Get begin.
     *
     * @return \DateTime
     */
    public function getBegin()
    {
        return $this->begin;
    }



    /**
     * Set weekDay.
     *
     * @param string $weekDay
     *
     * @return DeliveryPlanningDay
     */
    public function setWeekDay($weekDay)
    {
        $this->weekDay = $weekDay;

        return $this;
    }
	
	 /**
     * Get weekDay.
     *
     * @return string
     */
    public function getWeekDay()
    {
        return $this->weekDay;
    }

    /**
     * Get Hour.
     *
     * @return int
     */
    public function getHour()
    {
        return $this->hour;
    }
	
	  /**
     * Set weekDay.
     *
     * @param string $weekDay
     *
     * @return DeliveryPlanningDay
     */
    public function setHour($hour)
    {
        $this->hour = $hour;

        return $this;
    }


    /**
     * Set end.
     *
     * @param \DateTime $end
     *
     * @return DeliveryPlanningDay
     */
    public function setEnd($end)
    {
        $this->end = $end;

        return $this;
    }

    /**
     * Get end.
     *
     * @return \DateTime
     */
    public function getEnd()
    {
        return $this->end;
    }


    /**
     * Set dateCreate.
     *
     * @param \DateTime|null $dateCreate
     *
     * @return DeliveryPlanningDay
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
     * @return DeliveryPlanningDay
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
     * Set deliveryPlanning.
     *
     * @param \Sogedial\ApiBundle\Entity\DeliveryPlanning|null $deliveryPlanning
     *
     * @return DeliveryPlanningDay
     */
    public function setDeliveryPlanning(\Sogedial\ApiBundle\Entity\DeliveryPlanning $deliveryPlanning = null)
    {
        $this->deliveryPlanning = $deliveryPlanning;

        return $this;
    }

    /**
     * Get deliveryPlanning.
     *
     * @return \Sogedial\ApiBundle\Entity\DeliveryPlanning|null
     */
    public function getDeliveryPlanning()
    {
        return $this->deliveryPlanning;
    }
}
