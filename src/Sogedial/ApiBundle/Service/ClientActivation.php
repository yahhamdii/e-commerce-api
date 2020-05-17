<?php


namespace Sogedial\ApiBundle\Service;


use Doctrine\ORM\EntityManagerInterface;
use Sogedial\ApiBundle\Entity\Client;
use Sogedial\ApiBundle\Entity\ClientStatus;
use Sogedial\OAuthBundle\Entity\User;
use Sogedial\OAuthBundle\Entity\UserCustomer;
use Sogedial\OAuthBundle\Mailer\Mailer;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class ClientActivation
{
    private $em;
    private $requestStack;
    private $mailer;
    private $tokenStorage;
    private $updatePasswordLink;

    public function __construct(EntityManagerInterface $em,RequestStack $requestStack, Mailer $mailer, TokenStorageInterface $tokenStorage, $updatePasswordLink)
    {
        $this->em = $em;
        $this->requestStack = $requestStack;
        $this->mailer = $mailer;
        $this->tokenStorage = $tokenStorage;
        $this->updatePasswordLink = $updatePasswordLink;
    }

    public function activate($client, $platform){       
        if(!$client->getIsActivated()){
            if($client->getStatus() != Client::STATUS_ACTIVE){
                $client->setStatus(Client::STATUS_ACTIVE);
            }
            $client->setIsActivated(true);
            $this->em->persist($client);
            $this->sendConfigPasswordMail($client);
            $this->em->flush();
        }
    }


    public function sendConfigPasswordMail(Client $client, UserCustomer $userCustomer = null)
    {
        // get customers who are not connected yet
        $customers = [];
        if(is_null($userCustomer)){
            $customers = $client->getCustomers()->filter(
                function ($elem) {
                    /** @var User $elem */
                    if ($elem->getLastLogin() == null && $elem->getConfirmationToken() == null) {

                        return true;
                    }

                    return false;
                }
            )->toArray();
        }else{
            //check si le client de ce userCustomer n est pas encore active
            if($userCustomer->getClient()->getStatus() != Client::STATUS_ACTIVE){

                return ;
            }

            $customers[] = $userCustomer;
        }

        if (count($customers) > 0) {
            if (substr($this->updatePasswordLink, -1) !== '/') $this->updatePasswordLink = $this->updatePasswordLink . '/';

            //send mail to all customers to configure  their password
            /** @var UserCustomer $customer */
            foreach ($customers as $customer) {
                $customer->setConfirmationToken($this->generateToken());
                $customer->setPasswordRequestedAt(new \DateTime());
                $infoUser = array(
                    'identifiant' => $customer->getUsername(),
                    'link' => $this->updatePasswordLink . $customer->getConfirmationToken(),
                    'receiver' => $customer->getEmail()
                );

                if(filter_var($infoUser['receiver'], FILTER_VALIDATE_EMAIL)){
                    $this->mailer->sendFirstLoginEmailMessage($infoUser);
                }

                $this->em->merge($customer);
            }

            $this->em->flush();
        }

    }

    private function generateToken()
    {
        return rtrim(strtr(base64_encode(random_bytes(32)), '+/', '-_'), '=');
    }

}