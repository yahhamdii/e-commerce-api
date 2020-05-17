<?php

namespace Sogedial\OAuthBundle\Mailer;

use Swift_Mailer;
use Symfony\Bundle\FrameworkBundle\Templating\EngineInterface;
use Sogedial\ApiBundle\Entity\Promotion;
use Sogedial\OAuthBundle\Entity\UserCustomer;
use Sogedial\ApiBundle\Service\GeneratePdf;

class Mailer
{

    private $mailer;
    private $templating;
    private $fromEmail;
    private $generatePdf;


    public function __construct(Swift_Mailer $mailer, EngineInterface $templating, $fromEmail, GeneratePdf $generatePdf)
    {
        $this->mailer = $mailer;
        $this->templating = $templating;
        $this->fromEmail = $fromEmail;
        $this->generatePdf = $generatePdf;
    }


    protected function setTemplate($template, array $option)
    {

        return $this->templating->render($template, $option);
    }

    public function sendResettingEmailMessage(array $infoUser)
    {
        $subject = '[commande.com] Réinitialisation de votre mot de passe';
        $body = $this->setTemplate('@OAuth/Mailer/reset_password.html.twig', array('username' => $infoUser['username'], 'confirmationLink' => $infoUser['confirmationLink'], 'isfirstLogin' => false));
        $this->sendEmailMessage($infoUser['toEmail'], $subject, $body);
    }


    public function sendFirstLoginEmailMessage(array $infoUser)
    {
        $subject = 'Bienvenue sur commande.com !';
        $body = $this->setTemplate('@OAuth/Mailer/reset_password.html.twig', array('isfirstLogin' => true, 'identifiant' => $infoUser['identifiant'], 'link' => $infoUser['link']));
        $this->sendEmailMessage($infoUser['receiver'], $subject, $body);
    }

    public function sendCreditAlert(array $newCredits, $clientName, $clientSiret, $invoiceNumber, $emailCredit, $pathFile = null)
    {
        $subject = "[commande.com] Demande d'avoir Facture N° " . $invoiceNumber;
        $body = $this->setTemplate('@SogedialApi/Credit/creditAlert.html.twig', array('newCredits' => $newCredits, 'clientName' => $clientName, 'clientSiret' => $clientSiret, 'invoiceNumber' => $invoiceNumber));
        $this->sendEmailMessage($emailCredit, $subject, $body, array(), array($pathFile));
    }

    public function sendPromotionAlert(Promotion $promotion, $emailCommitment)
    {
        $clientName =  $promotion->getClient()->getExtCode().' '.$promotion->getClient()->getName();  
        $toEmails = [];
        $platform = $promotion->getPlatform();
        foreach($promotion->getClient()->getCommercialsByPlatform($platform) as $commercial){
            $toEmails[] = $commercial->getEmail();
        }        

        $items = $promotion->getItems();        
        if($items && $items->count()>0){
            $itemName = $items->first()->getName();
            $subject = "[commande.com] - Demande de stock engagement";               
            $body = $this->setTemplate('@SogedialApi/Promotion/commitmentAlert.html.twig', 
            array('commitmentRequest'=>$promotion->getStockCommitmentRequest(), 'itemName' => $itemName, 'clientName' => $clientName));
            $this->sendEmailMessage($toEmails, $subject, $body, $emailCommitment);
        }
    }


    protected function sendEmailMessage($toEmail, $subject, $body, $ccEmail=[], $pathFiles = array())
    {
        $message = \Swift_Message::newInstance()
            ->setSubject($subject)
            ->setFrom($this->fromEmail)
            ->setTo($toEmail)
            ->setBody($body, 'text/html');

            if(!empty($pathFiles)){
                foreach($pathFiles as $pathFile){
                    if(isset($pathFile) && is_readable($pathFile)){
                        $message->attach(\Swift_Attachment::fromPath($pathFile));
                    }
                }
            }
        
        if($ccEmail)
        $message->setCc($ccEmail);

        $this->mailer->send($message);
    }

    public function sendBDC(array $orders, UserCustomer $user, $isPreorder)
    {
        $emails = [];
        $platform = current($orders)->getPlatform();
        foreach($user->getClient()->getCommercialsByPlatform($platform) as $commercial){
            $emails[] =  $commercial->getEmail();
        }
        $emailClient = $user->getEmail();
        $emailAdmin = $platform->getAdmins()->first()->getEmail();
        if($isPreorder){
            array_push($emails, $emailClient, $emailAdmin);
        }else{
            array_push($emails, $emailAdmin);
        }

        foreach($orders as $order){
            $attachmentPdf = $this->generatePdf->orderToPdf($order, true);
            $attachmentExcel = $this->generatePdf->orderToExcel($order);
            $attachments = array($attachmentPdf, $attachmentExcel);
            $body = 'Bonjour, le client '.$user->getClient()->getExtCode().' vient de valider la commande n°'.$order->getNumber().' d’un montant de '.$order->getTotalPrice().'€ HT';
            $subject = "[commande.com] Bon De Commande N° " . $order->getNumber();
           
            $this->sendEmailMessage($emails, $subject, $body, array(), $attachments);
            foreach($attachments as $attachment){
                 unlink($attachment); 
            }
        }

    }

}