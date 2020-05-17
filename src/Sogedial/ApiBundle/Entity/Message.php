<?php

namespace Sogedial\ApiBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as Serializer;

/**
 * Message
 *
 * @ORM\Table(name="message")
 * @ORM\Entity(repositoryClass="Sogedial\ApiBundle\Repository\MessageRepository")
 */
class Message extends AbstractEntity
{
    /**
     * @var string
     *
     * @ORM\Column(name="subject", type="string", length=100)
     */
    private $subject;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="date_begin", type="datetime")
	 * @Serializer\Type("DateTime<'Y-m-d'>")
     */
    private $dateBegin;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="date_end", type="datetime")
	 * @Serializer\Type("DateTime<'Y-m-d'>")
     */
    private $dateEnd;

    /**
     * @var string
     *
     * @ORM\Column(name="content", type="string", length=255)
     */
    private $content;

    /**
     * @ORM\ManyToOne(targetEntity="Sogedial\ApiBundle\Entity\Platform")
     * @ORM\JoinColumn(nullable= true)
     * @Serializer\Exclude()
     */
    private $platform;

    /**
     * Set subject.
     *
     * @param string $subject
     *
     * @return Message
     */
    public function setSubject($subject)
    {
        $this->subject = $subject;

        return $this;
    }

    /**
     * Get subject.
     *
     * @return string
     */
    public function getSubject()
    {
        return $this->subject;
    }

    /**
     * Set dateBegin.
     *
     * @param \DateTime $dateBegin
     *
     * @return Message
     */
    public function setDateBegin($dateBegin)
    {
        $this->dateBegin = $dateBegin;

        return $this;
    }

    /**
     * Get dateBegin.
     *
     * @return \DateTime
     */
    public function getDateBegin()
    {
        return $this->dateBegin;
    }

    /**
     * Set dateEnd.
     *
     * @param \DateTime $dateEnd
     *
     * @return Message
     */
    public function setDateEnd($dateEnd)
    {
        $this->dateEnd = $dateEnd;

        return $this;
    }

    /**
     * Get dateEnd.
     *
     * @return \DateTime
     */
    public function getDateEnd()
    {
        return $this->dateEnd;
    }

    /**
     * Set content.
     *
     * @param string $content
     *
     * @return Message
     */
    public function setContent($content)
    {
        $this->content = $content;

        return $this;
    }

    /**
     * Get content.
     *
     * @return string
     */
    public function getContent()
    {
        return $this->content;
    }


    /**
     * Set dateCreate.
     *
     * @param \DateTime|null $dateCreate
     *
     * @return Message
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
     * @return Message
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
     * @param \Sogedial\ApiBundle\Entity\Platform|null $platform
     *
     * @return Message
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
