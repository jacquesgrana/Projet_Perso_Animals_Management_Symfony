<?php

namespace App\Controller;

use App\Entity\User;
use App\Repository\UserRepository;
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
                    $this->addFlash('error', 'Email non envoyé');
                }
            return $this->redirectToRoute('app_logout', [], Response::HTTP_SEE_OTHER);
        }


        /*
        $user->setEmail($email);
        $entityManager->persist($user);
        $entityManager->flush();*/
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
        // recuperer ancien et nouveau mdp a partir du body de la requete
        
        $oldPassword = $request->request->get('old-password');
        $newPassword1 = $request->request->get('new-password-1');
        $newPassword2 = $request->request->get('new-password-2');
        $csrf_token = $request->request->get('_csrf_token');
        // verifier le csrf_token('change-password')
        if (!$this->isCsrfTokenValid('change-password', $csrf_token)) {
            $this->addFlash('error', 'Token invalide');
            return $this->redirectToRoute('app_account_edit_password', [], Response::HTTP_SEE_OTHER);
        }


        // verifier ancien mdp
        if (!password_verify($oldPassword, $user->getPassword())) {
            $this->addFlash('error', 'Ancien mot de passe incorrect');
            return $this->redirectToRoute('app_account_edit_password', [], Response::HTTP_SEE_OTHER);
        }

        // verifier si les deux nouveau mdp sont les memes
        if ($newPassword1 != $newPassword2) {
            $this->addFlash('error', 'Nouveaux mots de passe différents');
            return $this->redirectToRoute('app_account_edit_password', [], Response::HTTP_SEE_OTHER);
        }

        $passwortHashed = password_hash($newPassword1, PASSWORD_BCRYPT);
        $user->setPassword($passwortHashed);
        $entityManager->flush();
        $this->addFlash('success', 'Mot de passe mis à jour');
        return $this->redirectToRoute('app_logout', [], Response::HTTP_SEE_OTHER);
    }
}
?>
