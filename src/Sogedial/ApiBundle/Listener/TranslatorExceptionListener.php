<?php


namespace Sogedial\ApiBundle\Listener;


use Symfony\Component\HttpKernel\Event\GetResponseForExceptionEvent;
use Symfony\Component\Translation\TranslatorInterface;

class TranslatorExceptionListener
{

    private $translator;

    const FRENCH = 'fr';

    public function __construct(TranslatorInterface $translator)
    {
        $this->translator = $translator;
    }

    protected function supports($langage)
    {
        //if language isn't one we support, return false, ... we can add many languages
        if (!in_array($langage, [self::FRENCH])) {

            return false;
        }

        return true;
    }

    public function translate(GetResponseForExceptionEvent $event): void
    {
        $language = $event->getRequest()->headers->get('Language');
        if (!$this->supports($language)) {
            return;
        }

        $exception = $event->getException();
        $translatedMesssage = $this->translator->trans($exception->getMessage(), array(), null, $language);
        $exception->setMessage($translatedMesssage);
    }

}