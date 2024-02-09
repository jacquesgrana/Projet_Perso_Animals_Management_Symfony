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
        //$dayString = $dayDate->format('d/m/Y');
        // TODO améliorer : switch case sur $type : 'DAY', 'WEEK', 'MONTH'
        //$subject = 'Evénement(s) ' . $typeName . ' ' . $day;
        $subject = '';
        switch ($type) {
            case 'DAY':
                $subject = 'Evénement(s) ' . $typeName . ' ' . $day;
                break;
            case 'WEEK':
                // calculer la date du lundi et du dimanche de la semaine de $day
                $monday = clone $dayDate;
                $monday->modify('monday this week');
                $sunday = clone $dayDate;
                $sunday->modify('sunday this week');
                $subject = 'Evénement(s) ' . $typeName . ' du ' . $monday->format('d/m/Y') . ' au ' . $sunday->format('d/m/Y');
                //$subject = 'Evénement(s) ' . $typeName . ' ' . $dayString;
                break;
            case 'MONTH':
                // récupérer le nom du mois de $dayDate avec le filtre frenchDateMonth
                //$monthName = $this->frenchDateExtension->frenchMonthFormat($dayDate);
                //$monthName = $dayDate->format('F Y');
                setlocale(LC_TIME, 'fr_FR.UTF-8');
                $monthName = strftime('%B %Y', $dayDate->getTimestamp());
                $subject = 'Evénement(s) ' . $typeName . ' de ' . $monthName;
                break;
            default:
                # code...
                break;
        }

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