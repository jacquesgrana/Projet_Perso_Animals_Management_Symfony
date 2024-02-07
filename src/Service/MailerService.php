<?php

namespace App\Service;

use Symfony\Component\Mailer\MailerInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mime\Address;
use Symfony\Component\Mime\Email;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use App\Library\CustomLibrary;

class MailerService
{
    private $mailer;
    private $tokenStorage;


    public function __construct(TokenStorageInterface $tokenStorage, MailerInterface $mailer)
    {
        $this->tokenStorage = $tokenStorage;
        $this->mailer = $mailer;
    }

    public function GenerateAndSendEmail($emailDest, $events, $day, $type): string
    {
        $typeName = CustomLibrary::getMailTypeTitle($type);
        $user = $this->tokenStorage->getToken()->getUser();
        $pseudo = $user->getPseudo();
        $dayDate = new \DateTime($day);
        $subject = 'Evénement(s) ' . $typeName . ' ' . $dayDate->format('d/m/Y');
        //$message = 'Hello!';
    
        // Create a TemplatedEmail instead of Email
        $email = (new TemplatedEmail())
            ->from(new Address('inbox.test.jac@free.fr'))
            ->to($emailDest)
            ->subject($subject)
            ->htmlTemplate('mail/send_mail.html.twig')
            ->context([
                'pseudo' => $pseudo,
                'events' => $events,
                'day' => $day,
                'typeName' => $typeName,
        ]);

        try {
            $this->mailer->send($email);
            return ('Email ok');
        } 
        catch (TransportExceptionInterface $e) {
            return ('Email ko : ' . $e->getMessage());
        } 
    }
}


/*
namespace App\Service;

use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mailer\MailerInterface;

class MailerService
{
    private $mailer;

    public function __construct(MailerInterface $mailer)
    {
        $this->mailer = $mailer;
    }

    public function GenerateAndSendEmail($emailDest, $events)
    {
        //dd($emailDest, $events);
        // Création de l'email
        // recuperer l'user depuis le token


        //$pseudo = $user->getPseudo();

        try {
            $email = (new TemplatedEmail())
            
                ->from('inbox.test.jac@gmail.com')
                ->to($emailDest)
                ->subject('Email de test')
                ->text('Hello!')
                ->html('<p>Hello!</p>')
            //->htmlTemplate('registration/confirmation_email.html.twig')
                ->context([
                    //'pseudo' => $pseudo,
                    'events' => $events]);

            // Envoi de l'email
            $this->mailer->send($email);
            } 
            catch (\Exception $e) {
                dd($e);
            }
    }
}
*/
?>