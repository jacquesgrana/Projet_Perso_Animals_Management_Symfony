<?php
// src/Security/UserChecker.php

namespace App\Security;

use Symfony\Component\Security\Core\Exception\CustomUserMessageAuthenticationException;
use Symfony\Component\Security\Core\User\UserCheckerInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use App\Service\MailerService;


class UserChecker implements UserCheckerInterface
{

    public function __construct(
        private MailerService $mailer
    )
    {}

    public function checkPreAuth(UserInterface $user): void
    {
        if (!$user->isActive()) {
            $this->mailer->GenerateAnsSendConfirmSigninEmail($user);
            // Vous pouvez personnaliser le message d'erreur selon vos besoins
            throw new CustomUserMessageAuthenticationException('Votre compte n\'est pas actif. Un email de confirmation vous a été envoyé. Veuillez le confirmer.');
        }
    }

    public function checkPostAuth(UserInterface $user): void
    {
        // Rien à faire ici
    }
}


?>