<?php

namespace Sogedial\ApiBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as Serializer;

/**
 * ContainerCartItem
 *
 * @ORM\Table(name="container_cartItem")
 * @ORM\Entity(repositoryClass="Sogedial\ApiBundle\Repository\ContainerCartItemRepository")
 * @Serializer\ExclusionPolicy("all")
 */
class ContainerCartItem extends AbstractEntity
{

    /**
     * @ORM\ManyToOne(targetEntity="Sogedial\ApiBundle\Entity\Container", inversedBy="containerCartItems")
     * @ORM\JoinColumn(nullable= false)
     */
    private $container;


    /**
     * @ORM\ManyToOne(targetEntity="Sogedial\ApiBundle\Entity\CartItem", inversedBy="containerCartItems")
     * @ORM\JoinColumn(nullable= false)
     */
    private $cartItem;

    /**
     * @var int
     *
     * @ORM\Column(name="quantity", type="integer")
     */
    private $quantity;


    /**
     * Set quantity.
     *
     * @param int $quantity
     *
     * @return ContainerCartItem
     */
    public function setQuantity($quantity)
    {
        $this->quantity = $quantity;

        return $this;
    }

    /**
     * Get quantity.
     *
     * @return int
     */
    public function getQuantity()
    {
        return $this->quantity;
    }

    /**
     * Set dateCreate.
     *
     * @param \DateTime|null $dateCreate
     *
     * @return ContainerCartItem
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
     * @return ContainerCartItem
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
     * Set container.
     *
     * @param \Sogedial\ApiBundle\Entity\Container $container
     *
     * @return ContainerCartItem
     */
    public function setContainer(\Sogedial\ApiBundle\Entity\Container $container)
    {
        $this->container = $container;

        return $this;
    }

    /**
     * Get container.
     *
     * @return \Sogedial\ApiBundle\Entity\Container
     */
    public function getContainer()
    {
        return $this->container;
    }

    /**
     * Set cartItem.
     *
     * @param \Sogedial\ApiBundle\Entity\CartItem $cartItem
     *
     * @return ContainerCartItem
     */
    public function setCartItem(\Sogedial\ApiBundle\Entity\CartItem $cartItem)
    {
        $this->cartItem = $cartItem;

        return $this;
    }

    /**
     * Get cartItem.
     *
     * @return \Sogedial\ApiBundle\Entity\CartItem
     */
    public function getCartItem()
    {
        return $this->cartItem;
    }


    /**
     * @return int|null
     */
    public function getVolumePackageLoaded($quantity = null)
    {
        if(is_null($quantity)){
            $quantity = $this->getQuantity();
        }
        $volumePackage = $this->getCartItem()->getItem()->getPackage()->getVolumePackage();
        if(strlen($volumePackage) == 0){
            $item = $this->getCartItem()->getItem();
            $pcb = $item->getPcb();
            $volumeUnit = $item->getPackage()->getVolumeUc();

            //TODO a verifier unite de mesure pour volume unit soit cm3 soit m3 pout savoir si on va diviser par 100 ou nn
            $volumePackage = ($volumeUnit * $pcb) / 100;
        }

        return $quantity * $volumePackage;
    }

    /**
     * @return int|null
     */
    public function getWeightPackageLoaded($quantity = null)
    {
        if(is_null($quantity)){
            $quantity = $this->getQuantity();
        }
        $weightPackage = $this->getCartItem()->getItem()->getPackage()->getWeightGrossPackage();
        if(isset($weightPackage)){

            return $quantity * $weightPackage;
        }

        return null;
    }
}
