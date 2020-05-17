<?php

namespace Sogedial\OAuthBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use FOS\OAuthServerBundle\Entity\AuthCode as BaseAuthCode;

/**
 * RefreshToken
 *
 * @ORM\Table(name="oauth2_authcode")
 * @ORM\Entity()
 */
class AuthCode extends BaseAuthCode
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\ManyToOne(targetEntity="Sogedial\OAuthBundle\Entity\Client")
     * @ORM\JoinColumn(nullable=false)
     */
    protected $client;


    /**
     * @ORM\ManyToOne(targetEntity="Sogedial\OAuthBundle\Entity\User")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id", onDelete="CASCADE")
     */
    protected $user;



    /**
     * Get id
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }


}
