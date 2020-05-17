<?php

namespace Sogedial\OAuthBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

use JMS\Serializer\Annotation as Serializer;

/**
 * UserCustomer
 *
 * @ORM\Table(name="user_customer")
 * @ORM\Entity()
 * @Serializer\ExclusionPolicy("all")
 */
class UserCustomer extends User
{
    const STATUS_PROSPECT = 'PROSPECT';

    /**
     * @var Orders
     *
     * @ORM\OneToMany(targetEntity="\Sogedial\ApiBundle\Entity\Order", mappedBy="user", fetch="LAZY")          
     */
    protected $orders;

    /**
     * @var Client
     *
     * @ORM\ManyToOne(targetEntity="\Sogedial\ApiBundle\Entity\Client", inversedBy="customers")
     * @ORM\JoinColumn(name="client_id", referencedColumnName="id" )
     * @Serializer\Expose
     * @Serializer\MaxDepth(7)
     */
    protected $client;

    /**
     * @ORM\Column(name="status", type="string", length=255, nullable=true)
     * @Serializer\Expose
     */
    protected $status;

    /**
     * @Serializer\VirtualProperty()
     * @Serializer\Groups({"moq"})
     */
    public function getClientName() {

        $client = $this->client;
        
        return ($client)?$client->getName():false;
    }

    /**
     * @Serializer\VirtualProperty()
     */
    public function getClientExtCode() {

        $client = $this->client;
        
        return ($client)?$client->getExtCode():false;
    }


    public function setDefaultRoles(){

        $this->addRole(User::ROLE_CUSTOMER);

        return $this;

    }

    /**
     * Set client.
     *
     * @param \Sogedial\ApiBundle\Entity\Client|null $client
     *
     * @return UserCustomer
     */
    public function setClient(\Sogedial\ApiBundle\Entity\Client $client = null)
    {
        $this->client = $client;

        return $this;
    }

    /**
     * Get client.
     *
     * @return \Sogedial\ApiBundle\Entity\Client|null     
     */
    public function getClient()
    {
        return $this->client;
    }

    /**
     * Set dateCreate.
     *
     * @param \DateTime|null $dateCreate
     *
     * @return UserCustomer
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
     * @return UserCustomer
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
     * @return UserCustomer
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
     * Constructor
     */
    public function __construct()
    {
        $this->orders = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Add order.
     *
     * @param \Sogedial\ApiBundle\Entity\Order $order
     *
     * @return UserCustomer
     */
    public function addOrder(\Sogedial\ApiBundle\Entity\Order $order)
    {
        $this->orders[] = $order;

        return $this;
    }

    /**
     * Remove order.
     *
     * @param \Sogedial\ApiBundle\Entity\Order $order
     *
     * @return boolean TRUE if this collection contained the specified element, FALSE otherwise.
     */
    public function removeOrder(\Sogedial\ApiBundle\Entity\Order $order)
    {
        return $this->orders->removeElement($order);
    }

    /**
     * Get orders.
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getOrders()
    {
        return $this->orders;
    }
}
