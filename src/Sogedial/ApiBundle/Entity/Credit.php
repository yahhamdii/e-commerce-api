<?php

namespace Sogedial\ApiBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as Serializer;
use Sogedial\OAuthBundle\Entity\UserCustomer;

/**
 * Credit
 *
 * @ORM\Table(name="credit")
 * @ORM\Entity(repositoryClass="Sogedial\ApiBundle\Repository\CreditRepository")
 */
class Credit extends AbstractEntity
{

    /**
     * @var int
     *
     * @ORM\Column(name="quantity", type="integer")
     * @Serializer\Expose()
     * @Serializer\Groups({"credit", "list_credit"})
     */
    private $quantity;

    /**
     * @var string
     *
     * @ORM\Column(name="reason", type="string", length=255)
     * @Serializer\Expose()
     * @Serializer\Groups({"credit", "list_credit"})
     */
    private $reason;

    /**
     * @var string
     *
     * @ORM\Column(name="comment", type="text")
     * @Serializer\Groups({"credit", "list_credit"})
     */
    private $comment;


    /**
     * @var boolean
     *
     * @ORM\Column(name="checked", type="boolean", nullable= true)
     * @Serializer\Groups({"credit", "list_credit"})
     */
    private $checked;

    /**
     * @ORM\Column(name="sended", type="boolean", options={"default": false})
     */
    private $sended = false;


    /**
     * @ORM\ManyToOne(targetEntity="Sogedial\ApiBundle\Entity\InvoiceItem", inversedBy="credits")
     * @ORM\JoinColumn(nullable= false)
     * @Serializer\Expose()
     * @Serializer\Groups({"credit", "list_credit"})
     */
    private $invoiceItem;


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
     * Set quantity.
     *
     * @param int $quantity
     *
     * @return Credit
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
     * Set reason.
     *
     * @param string $reason
     *
     * @return Credit
     */
    public function setReason($reason)
    {
        $this->reason = $reason;

        return $this;
    }

    /**
     * Get reason.
     *
     * @return string
     */
    public function getReason()
    {
        return $this->reason;
    }

    /**
     * Set comment.
     *
     * @param string $comment
     *
     * @return Credit
     */
    public function setComment($comment)
    {
        $this->comment = $comment;

        return $this;
    }

    /**
     * Get comment.
     *
     * @return string
     */
    public function getComment()
    {
        return $this->comment;
    }

    /**
     * Set checked.
     *
     * @param bool|null $checked
     *
     * @return Credit
     */
    public function setChecked($checked = null)
    {
        $this->checked = $checked;

        return $this;
    }

    /**
     * Get checked.
     *
     * @return bool|null
     */
    public function getChecked()
    {
        return $this->checked;
    }

     
    /**
     * @Serializer\VirtualProperty()
     * @Serializer\Expose()
     * @Serializer\Groups({"list_credit"})
     */
    public function getClientName()
    {
        $client = $this->getInvoiceItem()->getInvoice()->getClient();

        return ($client)?$client->getName():"";
    }


     /**
     * @Serializer\VirtualProperty()
     * @Serializer\Expose()
     * @Serializer\Groups({"list_credit"})
     */
    public function getClientCode()
    {
        $client = $this->getInvoiceItem()->getInvoice()->getClient();
                   
        return ($client)?$client->getExtCode():"";
    }
    
    /**
     * @Serializer\VirtualProperty()
     * @Serializer\Expose()
     * @Serializer\Groups({"list_credit"})
     */
    public function getUserFirstname()
    {   
        $order = $this->getInvoiceItem()->getInvoice()->getOrder();
        
        return ($order)?$order->getUser()->getFirstname():"";
    }

    /**
     * @Serializer\VirtualProperty()
     * @Serializer\Expose()
     * @Serializer\Groups({"list_credit"})
     */
    public function getUserLastname()
    {             
        $order = $this->getInvoiceItem()->getInvoice()->getOrder();

        return ($order)?$order->getUser()->getLastname():"";
    }


    /**
     * Set sended.
     *
     * @param bool $sended
     *
     * @return Credit
     */
    public function setSended($sended)
    {
        $this->sended = $sended;

        return $this;
    }

    /**
     * Get sended.
     *
     * @return bool
     */
    public function getSended()
    {
        return $this->sended;
    }

    /**
     * Set dateCreate.
     *
     * @param \DateTime|null $dateCreate
     *
     * @return Credit
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
     * @return Credit
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
     * Set invoiceItem.
     *
     * @param \Sogedial\ApiBundle\Entity\InvoiceItem $invoiceItem
     *
     * @return Credit
     */
    public function setInvoiceItem(\Sogedial\ApiBundle\Entity\InvoiceItem $invoiceItem)
    {
        $this->invoiceItem = $invoiceItem;

        return $this;
    }

    /**
     * Get invoiceItem.
     *
     * @return \Sogedial\ApiBundle\Entity\InvoiceItem
     */
    public function getInvoiceItem()
    {
        return $this->invoiceItem;
    }

    /**
     * @Serializer\VirtualProperty()
     * @Serializer\Expose()
     * @Serializer\Groups({"list_credit"})
     */
    public function getInvoiceNumber()
    {   
        return $this->getInvoiceItem()->getInvoice()->getNumber();
    }
}
