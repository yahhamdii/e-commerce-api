<?php

namespace Sogedial\OAuthBundle\Entity;

use JMS\Serializer\Annotation as Serializer;
use Doctrine\ORM\Mapping as ORM;

/**
 * UserAdmin
 *
 * @ORM\Table(name="user_admin")
 * @ORM\Entity()
 * @Serializer\ExclusionPolicy("all")
 */
class UserAdmin extends User {

    /**
     *
     * @ORM\ManyToOne(targetEntity="Sogedial\ApiBundle\Entity\Platform", inversedBy="admins")
     * @ORM\JoinColumn(name="platform_id", referencedColumnName="id")
     * @Serializer\Expose
     * @Serializer\MaxDepth(3)
     */
    private $platform;

    public function setDefaultRoles() {

        $this->addRole(User::ROLE_ADMIN);

        return $this;
    }

    /**
     * Set platform.
     *
     * @param \Sogedial\ApiBundle\Entity\Platform|null $platform
     *
     * @return UserAdmin
     */
    public function setPlatform(\Sogedial\ApiBundle\Entity\Platform $platform = null) {
        $this->platform = $platform;

        return $this;
    }

    /**
     * Get platform.
     *
     * @return \Sogedial\ApiBundle\Entity\Platform|null
     */
    public function getPlatform() {
        return $this->platform;
    }

}
