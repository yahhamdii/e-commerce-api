<?php

namespace Sogedial\OAuthBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use FOS\OAuthServerBundle\Entity\AccessToken as BaseAccessToken;
use FOS\OAuthServerBundle\Model\ClientInterface;

/**
 * AccesToken
 *
 * @ORM\Table(name="oauth2_accestoken")
 * @ORM\Entity()
 * @ORM\HasLifecycleCallbacks()
 */
class AccessToken extends BaseAccessToken
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

    public function getClient()
    {
        return $this->client;
    }

    public function setClient(ClientInterface $client)
    {
        $this->client = $client;
    }

    /**
     * @ORM\PostPersist()
     */
    public function updateLastLogin()
    {
        $this->getUser()->setLastLogin(new \DateTime());
    }
}
