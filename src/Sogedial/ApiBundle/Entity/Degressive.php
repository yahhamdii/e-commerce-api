<?php

namespace Sogedial\ApiBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as Serializer;

/**
 * Degressive
 *
 * @ORM\Table(name="degressive")
 * @ORM\Entity(repositoryClass="Sogedial\ApiBundle\Repository\DegressiveRepository")
 * @Serializer\ExclusionPolicy("all")
 */
class Degressive extends AbstractEntity {

    /**
     * @var string
     * @Serializer\Expose
     * @ORM\Column(name="amount", type="decimal", precision=8, scale=2)
     */
    private $amount;

    /**
     * @var string
     * @Serializer\Expose
     * @ORM\Column(name="step", type="string", length=255)
     */
    private $step;

    /**
     * @var string|null
     * @Serializer\Expose
     * @ORM\Column(name="code", type="string", length=255, nullable=true)
     */
    private $code;

    /**
     * @ORM\ManyToOne(targetEntity="Item", inversedBy="degressives")
     * @ORM\JoinColumn(name="item_id", referencedColumnName="id")
     */
    private $item;

    /**
     * Set amount.
     *
     * @param string $amount
     *
     * @return Degressive
     */
    public function setAmount($amount) {
        $this->amount = $amount;

        return $this;
    }

    /**
     * Get amount.
     *
     * @return string
     */
    public function getAmount() {
        return $this->amount;
    }

    /**
     * Set step.
     *
     * @param string $step
     *
     * @return Degressive
     */
    public function setStep($step) {
        $this->step = $step;

        return $this;
    }

    /**
     * Get step.
     *
     * @return string
     */
    public function getStep() {
        return $this->step;
    }

    /**
     * Set price.
     *
     * @param string $price
     *
     * @return Degressive
     */
    public function setPrice($price) {
        $this->price = $price;

        return $this;
    }

    /**
     * Get price.
     *
     * @return string
     */
    public function getPrice() {
        return $this->price;
    }

    /**
     * Set item.
     *
     * @param \Sogedial\ApiBundle\Entity\Item|null $item
     *
     * @return Degressive
     */
    public function setItem(\Sogedial\ApiBundle\Entity\Item $item = null) {
        $this->item = $item;

        return $this;
    }

    /**
     * Get item.
     *
     * @return \Sogedial\ApiBundle\Entity\Item|null
     */
    public function getItem() {
        return $this->item;
    }

    /**
     * Set dateCreate.
     *
     * @param \DateTime|null $dateCreate
     *
     * @return Degressive
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
     * @return Degressive
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
     * Set code.
     *
     * @param string|null $code
     *
     * @return Degressive
     */
    public function setCode($code = null) {
        $this->code = $code;

        return $this;
    }

    /**
     * Get code.
     *
     * @return string|null
     */
    public function getCode() {
        return $this->code;
    }

}
