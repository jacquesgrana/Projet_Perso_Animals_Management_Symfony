<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\ConfirmToken;
use App\Repository\UserRepository;
use App\Repository\ConfirmTokenRepository;
use App\Service\MailerService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use App\Form\UserAccountType;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Validation;

#[Route('/user')]
class AccountController extends AbstractController
{
    #[Route('/account', name: 'app_account', methods: ['GET'])]
    public function show(): Response
    {
        $user = $this->getUser();
        return $this->render('account/index.html.twig', [
            'user' => $user
        ]);
    }

    #[Route('/account/{id}/edit', name: 'app_account_edit', methods: ['GET', 'POST'])]
    public function edit(
        Request $request, 
        User $user, 
        EntityManagerInterface $entityManager
        ): Response
    {
        $form = $this->createForm(UserAccountType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($user);
            $entityManager->flush();

            return $this->redirectToRoute('app_account', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('account/edit.html.twig', [
            'user' => $user,
            'form' => $form,
        ]);
    }

    #[Route('/account/edit/email', name: 'app_account_edit_email', methods: ['GET', 'POST'])]
    public function modifyEmail (
        Request $request, 
        EntityManagerInterface $entityManager
        ): Response
    {
        // recuperer l'email a partir de la query string
        $email = $request->query->get('email');
        return $this->render('account/change_email.html.twig', [
            'email' => $email
        ]);
    }

    #[Route('/account/change/email', name: 'app_account_change_email', methods: ['GET', 'POST'])]
    public function setEmail (
        Request $request, 
        EntityManagerInterface $entityManager,
        UserRepository $userRepository,
        MailerService $mailerService
        ): Response
    {
        $user = $this->getUser();
        $oldEmail = $user->getEmail();
        $email = $request->request->get('email');
        $csrf_token = $request->request->get('_csrf_token');
        $validator = Validation::createValidator();
        $violations = $validator->validate($email, new Email());
        $isUniqueEmail = $userRepository->findOneByEmail($email) === null;
        $isEmailValid = 0 === count($violations);
        $isTokenValid = $this->isCsrfTokenValid('change-email', $csrf_token);
        $isEmailsNotTheSame = $email != $oldEmail;

        if (!$isUniqueEmail || !$isEmailValid || !$isTokenValid || !$isEmailsNotTheSame) {
            if(!$isUniqueEmail) {
                $this->addFlash('error', 'Email non unique');
            }
            if(!$isEmailValid) {
                $this->addFlash('error', 'Email invalide');
            }
            if(!$isTokenValid) {
                $this->addFlash('error', 'Token invalide');
            }
            if(!$isEmailsNotTheSame) {
                $this->addFlash('error', 'Emails identiques');
            }
            return $this->redirectToRoute('app_account_edit_email', [], Response::HTTP_SEE_OTHER);
        }
        else {
            $user->setEmail($email);
            $user->setActive(false);
            $entityManager->persist($user);
            $entityManager->flush();

            $result = $mailerService->GenerateAnsSendConfirmSigninEmail($user);
            if ($result === 'Email ok') {
                $this->addFlash('success', 'Email de confirmation envoyé');
            }
            else {
                $this->addFlash('error', $result);
            }
            return $this->redirectToRoute('app_logout', [], Response::HTTP_SEE_OTHER);
        }
    }

    #[Route('/account/edit/password', name: 'app_account_edit_password', methods: ['GET', 'POST'])]
    public function modifyPassword (
        Request $request, 
        EntityManagerInterface $entityManager
        ): Response
    {
        return $this->render('account/change_password.html.twig');
    }

    #[Route('/account/change/password', name: 'app_account_change_password', methods: ['GET', 'POST'])]
    public function setPassword (
        Request $request, 
        EntityManagerInterface $entityManager
        ): Response
    {
        $user = $this->getUser();        
        $oldPassword = $request->request->get('old-password');
        $newPassword1 = $request->request->get('new-password-1');
        $newPassword2 = $request->request->get('new-password-2');
        $csrf_token = $request->request->get('_csrf_token');
        $isTokenValid = $this->isCsrfTokenValid('change-password', $csrf_token);
        $isOldPasswordVerified = password_verify($oldPassword, $user->getPassword());
        $isNewPasswordsTheSame = $newPassword1 == $newPassword2;

        if (!$isTokenValid || !$isOldPasswordVerified || !$isNewPasswordsTheSame) {
            if(!$isTokenValid) {
                $this->addFlash('error', 'Token invalide');
            }
            if(!$isOldPasswordVerified) {
                $this->addFlash('error', 'Ancien mot de passe incorrect');
            }
            if(!$isNewPasswordsTheSame) {
                $this->addFlash('error', 'Nouveaux mots de passe différents');
            }
            return $this->redirectToRoute('app_account_edit_password', [], Response::HTTP_SEE_OTHER);
        }
        else {
            $passwortHashed = password_hash($newPassword1, PASSWORD_BCRYPT);
            $user->setPassword($passwortHashed);
            $entityManager->persist($user);
            $entityManager->flush();
            $this->addFlash('success', 'Mot de passe mis à jour');
            return $this->redirectToRoute('app_logout', [], Response::HTTP_SEE_OTHER);
        }
    }

    #[Route('/send-email-form/reset/password', name: 'app_account_send_mail_form_reset_password', methods: ['GET', 'POST'])]
    public function sendMailFormResetPassword (
        Request $request, 
        EntityManagerInterface $entityManager
        ): Response
    {
        return $this->render('account/reset_password_send_mail.html.twig');
    }

    #[Route('/send-email-action/reset/password', name: 'app_account_send_mail_action_reset_password', methods: ['GET', 'POST'])]
    public function sendMailActionResetPassword (
        Request $request, 
        EntityManagerInterface $entityManager,
        UserRepository $userRepository,
        MailerService $mailerService
        ): Response
    {
        // recuperer l'email du body de la request
        $email = $request->request->get('email');
        $csrf_token = $request->request->get('_csrf_token');
        // vérifier que l'email existe bien
        $isUsedEmail = $userRepository->findBy(['email' => $email]) !== [];
        $isTokenValid = $this->isCsrfTokenValid('reset-password-send-mail', $csrf_token);
        if (!$isUsedEmail || !$isTokenValid) {
            if(!$isUsedEmail) {
                $this->addFlash('error', 'Email non existant');
            }
            if(!$isTokenValid) {
                $this->addFlash('error', 'Token invalide');
            }
            return $this->redirectToRoute('app_account_send_mail_form_reset_password', [], Response::HTTP_SEE_OTHER);
        }
        else {
            $user = $userRepository->findOneBy(['email' => $email]);
            $user->setActive(false);
            $entityManager->persist($user);
            $entityManager->flush();
            $result = $mailerService->generateAndSendResetPasswordEmail($user);
            if ($result === 'Email ok') {
                $this->addFlash('success', 'Email de réinitialisation envoyé');
            }
            else {
                $this->addFlash('error', $result);
            }
            return $this->redirectToRoute('app_login', [], Response::HTTP_SEE_OTHER);
        }
        
        //return $this->render('account/reset_password_send_mail.html.twig');
    }

    #[Route('/reset/password', name: 'app_reset_password', methods: ['GET', 'POST'])]
    public function renderResetPassword (
        Request $request, 
        EntityManagerInterface $entityManager,
        UserRepository $userRepository,
        ConfirmTokenRepository $confirmTokenRepository,
        ): Response
    {
        // recuperer le token dans la query string
        $token = $request->query->get('token');
        $confirmToken = $confirmTokenRepository->findOneBy(['token' => $token]);
        $isTokenValid = $confirmToken !== null;
        if (!$isTokenValid) {
            $this->addFlash('error', 'Token invalide');
            return $this->redirectToRoute('app_login', [], Response::HTTP_SEE_OTHER);
        }
        else {
            $user = $confirmToken->getUser();
            return $this->render('account/reset_password.html.twig', [
                'token' => $token,
                'user' => $user
            ]);
        }
       
    }

    #[Route('/reset/password/action', name: 'app_reset_password_action', methods: ['GET', 'POST'])]
    public function resetPassword (
        Request $request, 
        EntityManagerInterface $entityManager,
        UserRepository $userRepository,
        ConfirmTokenRepository $confirmTokenRepository,
        ): Response
    {
        $csrf_token = $request->request->get('_csrf_token');
        $token = $request->request->get('_token');
        $newPassword1 = $request->request->get('new-password-1');
        $newPassword2 = $request->request->get('new-password-2');

        $isCsrfTokenValid = $this->isCsrfTokenValid('reset-password', $csrf_token);
        $confirmToken = $confirmTokenRepository->findOneBy(['token' => $token]);
        $isTokenValid = $confirmToken !== null;
        $isTokenNotExpired = $isTokenValid ? $confirmToken->getExpireAt() > new \DateTimeImmutable() : false;
        $isPasswordsTheSame = $newPassword1 == $newPassword2;

        // ajouter gestion de l'expiration du token $token
        if (!$isCsrfTokenValid || !$isTokenValid || !$isPasswordsTheSame || !$isTokenNotExpired) {
            if(!$isCsrfTokenValid) {
                $this->addFlash('error', 'Csrf Token invalide');
            }
            if(!$isTokenValid) {
                $this->addFlash('error', 'Token invalide');
            }
            if(!$isTokenNotExpired) {
                $this->addFlash('error', 'Token expiré');
            }
            if(!$isPasswordsTheSame) {
                $this->addFlash('error', 'Nouveaux mots de passe différents');
            }
            return $this->redirectToRoute('app_login', [], Response::HTTP_SEE_OTHER);
        }
        else {
            $user = $confirmToken->getUser();
            $passwortHashed = password_hash($newPassword1, PASSWORD_BCRYPT);
            $user->setPassword($passwortHashed);
            $user->setActive(true);
            foreach ($user->getConfirmTokens() as $ct) {
                $user->removeConfirmToken($ct);
                $entityManager->remove($ct);
            }
            $entityManager->persist($user);
            $entityManager->flush();
            $this->addFlash('success', 'Mot de passe réinitialisé');
            return $this->redirectToRoute('app_login', [], Response::HTTP_SEE_OTHER);
        }

    }
}
?>
