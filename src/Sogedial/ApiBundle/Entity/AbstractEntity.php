<?php

namespace Sogedial\ApiBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\Mapping\EntityListeners;
use Gedmo\Mapping\Annotation as Gedmo;
use JMS\Serializer\Annotation as Serializer;

/**
 * Abstract
 *
 * @ORM\MappedSuperclass
 */
abstract class AbstractEntity {

    /**
     * @var int
     * @Serializer\Type(name="int")
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @Serializer\Groups({"add", "update", "list", "detail", "listitems", "moq", "search", "credit", "list_credit", "list_pricing"})
     * @Serializer\Type("integer")
     */
    protected $id;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     * @Gedmo\Timestampable(on="create")
     * @Serializer\Groups({"add", "update", "list", "detail", "list_credit"})
     * @Serializer\Type("DateTime")
     * @Serializer\Expose
     */
    protected $dateCreate;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     * @Gedmo\Timestampable(on="update")
     * @Serializer\Expose
     * @Serializer\Groups({"add", "update", "list", "detail", "list_credit"})
     * @Serializer\Type("DateTime")
     */
    protected $dateUpdate;

    /**
     * @Serializer\Exclude()
     * @var \Sogedial\OAuthBundle\Entity\User
     */
    private $currentUser;

    /**
     *
     * @return \Sogedial\OAuthBundle\Entity\User|null
     */
    function getCurrentUser() {
        return $this->currentUser;
    }

    /**
     *
     * @param \Sogedial\OAuthBundle\Entity\User|null $currentUser
     */
    function setCurrentUser($currentUser = null) {
        $this->currentUser = $currentUser;
    }

    /**
     * Get id.
     *
     * @return int
     */
    public function getId() {
        return $this->id;
    }

}
