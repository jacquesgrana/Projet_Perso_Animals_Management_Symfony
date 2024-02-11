<?php

namespace App\Service;

use Symfony\Component\Mailer\MailerInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mime\Address;
use Symfony\Component\Mime\Email;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Doctrine\ORM\EntityManagerInterface;
use App\Library\CustomLibrary;
use App\Entity\User;
use App\Entity\ConfirmToken;
use App\Repository\ConfirmTokenRepository;
use App\Repository\UserRepository;

class MailerService
{
    private $mailer;
    private $tokenStorage;
    private $entityManager;
    private $userRepository;
    private $confirmTokenRepository;
    private $router;


    public function __construct(
        TokenStorageInterface $tokenStorage, 
        MailerInterface $mailer, 
        EntityManagerInterface $entityManager,
        UserRepository $userRepository,
        ConfirmTokenRepository $confirmTokenRepository,
        RouterInterface $router,
        )
    {
        $this->tokenStorage = $tokenStorage;
        $this->mailer = $mailer;
        $this->entityManager = $entityManager;
        $this->userRepository = $userRepository;
        $this->confirmTokenRepository = $confirmTokenRepository;
        $this->router = $router;
    }

    // TODO changer nom GenerateAndSendEventsEmail
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

    public function GenerateAnsSendConfirmSigninEmail($user): string {
        $email = $user->getEmail();
        $pseudo = $user->getPseudo();
        $userId = $user->getId();
        $subject = 'Bienvenue ' . $pseudo;
        // générer un token de 32 caractéres de confirmation pour l'utilisateur $user


        do {
            $token = bin2hex(random_bytes(16));
            $confirmTokenDB = $this->confirmTokenRepository->findOneBy(['token' => $token]);
        } while ($confirmTokenDB !== null);

        //$token = bin2hex(random_bytes(16));
        $confirmToken = new ConfirmToken();
        $confirmToken->setUser($user);
        $confirmToken->setToken($token);
        $confirmToken->setExpireAt(new \DateTimeImmutable('+1 day'));
        //$user->setConfirmToken($confirmToken);
        $user->addConfirmToken($confirmToken);
        //dd($confirmToken);
        // stocker le token dans la base de données
        $this->entityManager->persist($confirmToken);
        $this->entityManager->persist($user);
        $this->entityManager->flush();

        // ajouter token au context du template de l'email
        $url = $this->router->generate('app_confirm_signin', ['token' => $token], UrlGeneratorInterface::ABSOLUTE_URL);

        $email = (new TemplatedEmail())
            ->from(new Address('inbox.test.jac@free.fr'))
            ->to($email)
            ->subject($subject)
            ->htmlTemplate('mail/send_confirm_signin.html.twig')
            ->context([
                'pseudo' => $pseudo,
                'url' => $url,
        ]);
        try {
            $this->mailer->send($email);
            return ('Email ok');
        } 
        catch (TransportExceptionInterface $e) {
            return ('Email ko : ' . $e->getMessage());
        }
        return ('Email ok');
    }
}
?>