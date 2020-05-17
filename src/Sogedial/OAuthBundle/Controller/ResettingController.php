<?php

namespace Sogedial\OAuthBundle\Controller;

use FOS\RestBundle\Controller\Annotations as Rest;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sogedial\ApiBundle\Exception\BadRequestException;
use Sogedial\ApiBundle\Exception\EntityNotFoundException;
use Sogedial\ApiBundle\Exception\ForbiddenException;
use Sogedial\ApiBundle\Exception\ParametersException;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

/**
 * Controller managing the resetting of the password.
 *
 * @Rest\Route("oauth/resetting")
 */
class ResettingController extends Controller
{
    /**
     * @var int
     */
    private $retryTtl = 300;

    public function __contruct(){
        if($this->getParameter('resettingRetryTtl'))
            $this->retryTtl = $this->getParameter('resettingRetryTtl');

        parent::__contruct();
    }

    /**
     * @Rest\Post(path="/sendemail", defaults={"_format": "json"}, name="send_mail_reset")
     * @Method({"POST", "OPTIONS"})
     * @Rest\View(statusCode=200)
     */
    public function sendEmailAction(Request $request)
    {
        $username = $request->get('username');
        $callback = $request->get('callback');

        if ($callback === null || $callback == "") {
            throw new ParametersException(array('callback'));
        }

        $em = $this->getDoctrine()->getManager();
        $user = $em->getRepository('OAuthBundle:User')->getUserByUsername($username);
        
        if ($user === null) {
            throw new EntityNotFoundException("the username does not exist");
        }

        $ttl = $this->retryTtl;
        if($user->isPasswordRequestNonExpired($ttl)){
            //confirmation token est encore disponible, redirection vers la page check mail (selon fos user)
            throw new ForbiddenException('An email has been sent to you to change your password');
        }

        //generate confirmation token
        if (null === $user->getConfirmationToken() || strlen($user->getConfirmationToken()) == 0) {
            $user->setConfirmationToken($this->generateToken());
        }

        if(substr($callback, -1) !== '/')$callback = $callback.'/';
        $confirmationLink = $callback.$user->getConfirmationToken();
        
        $this->get('sogedial.oauth.mailer')->sendResettingEmailMessage(array('username' => $username, 'confirmationLink' => $confirmationLink, 'toEmail' => $user->getEmail()));
        $user->setPasswordRequestedAt(new \DateTime());

        $em->persist($user);
        $em->flush();

        return;
    }


    private function generateToken()
    {
        return rtrim(strtr(base64_encode(random_bytes(32)), '+/', '-_'), '=');
    }


    /**
     * @Rest\Post(path="/reset/{token}", defaults={"_format": "json"}, name="reset_password")
     * @Rest\QueryParam(name="isfirstLogin", default= "false", nullable= true)
     * @Method({"POST", "OPTIONS"})
     * @Rest\View(statusCode=200)
     */
    public function resetAction(Request $request, $token, $isfirstLogin )
    {
        $user = $this->getDoctrine()->getRepository('OAuthBundle:User')->findOneBy(array('confirmationToken' => $token));

        if (null === $user) {
            throw new EntityNotFoundException(sprintf("The user with confirmation token does not exist for value '%s' ", $token));
        }

        if($isfirstLogin == "false"){
            $ttl = $this->retryTtl;
            if (!$user->isPasswordRequestNonExpired($ttl)) {
                //Le token est expiré,on a depassé la duree ttl
                throw new ForbiddenException('Your session has expired');
            }
        }

        $newPassword = $request->get('newPassword');
        $newPasswordConfirmation = $request->get('newPasswordConfirmation');

        if ($newPassword !== $newPasswordConfirmation) {
            throw new BadRequestException('new password and confirm password do not match');
        }

        $user->setPlainPassword($newPasswordConfirmation);
        $passwordUpdater = $this->get('sogedial.oauth.utils.password_updater');
        $user->setConfirmationToken(null);
        $user->setPasswordRequestedAt(null);
        $user->setEnabled(true);
        $passwordUpdater->hashPassword($user);
        $em = $this->getDoctrine()->getManager();
        $em->persist($user);
        $em->flush();

        return;
    }

}
