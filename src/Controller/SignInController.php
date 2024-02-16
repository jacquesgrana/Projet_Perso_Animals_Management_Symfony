<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\ConfirmToken;
use App\Form\UserSigninType;
use App\Repository\UserRepository;
use App\Repository\ConfirmTokenRepository;
use App\Service\MailerService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class SignInController extends AbstractController
{
    
    #[Route('/signin', name: 'app_signin', methods: ['GET', 'POST'])]
    public function signin(
        Request $request, 
        EntityManagerInterface $entityManager,
        UserRepository $userRepository,
        MailerService $mailerService
        ): Response
    {
        $user = new User();
        $form = $this->createForm(UserSigninType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $newUser = $form->getData();
            $email = $newUser->getEmail();
            $pseudo = $newUser->getPseudo();

            $isUniqueEmail = $userRepository->findOneByEmail($email) === null;
            $isUniquePseudo = $userRepository->findOneByPseudo($pseudo) === null;

            if (!$isUniqueEmail || !$isUniquePseudo) {
                if(!$isUniqueEmail) {
                    //$form->get('email')->addError(new \Symfony\Component\Form\FormError('Email non unique'));
                    $this->addFlash('error', 'Email non unique');
                }
                if(!$isUniquePseudo) {
                    //$form->get('pseudo')->addError(new \Symfony\Component\Form\FormError('Pseudo non unique'));
                    $this->addFlash('error', 'Pseudo non unique');
                }
                return $this->redirectToRoute('app_signin', [], Response::HTTP_SEE_OTHER);
            }
            else {
                // récupérer le mot de passe
                $passwordToHash = $newUser->getPassword();
                $passwortHashed = password_hash($passwordToHash, PASSWORD_BCRYPT);
                $user->setPassword($passwortHashed);

                // set role à ROLE_USER
                $user->setRoles(['ROLE_USER']);
                $user->setActive(false);
                $entityManager->persist($user);
                $entityManager->flush();
                // appeler fonction du service de mail pour envoyer un email de confirmation qui contient le lien de confirmation du compte

                // commenter tant entité ConfirmToken n'est pas reecrite
                $result = $mailerService->GenerateAnsSendConfirmSigninEmail($user);
                if ($result === 'Email ok') {
                    $this->addFlash('success', 'Email de confirmation envoyé');
                }
                else {
                    $this->addFlash('error', $result);
                }
                return $this->redirectToRoute('app_login', [], Response::HTTP_SEE_OTHER);  
            }

            
        }

        return $this->render('signin/signin.html.twig', [
            'controller_name' => 'SignInController',
            'user' => $user,
            'form' => $form,
        ]);
    }


    #[Route('/signin/confirm', name: 'app_confirm_signin', methods: ['GET', 'POST'])]
    public function confirm(
        Request $request,
        ConfirmTokenRepository $confirmTokenRepository,
        EntityManagerInterface $entityManager,
    ): Response
    {
        //$userId = 0;
        $isActivated = false;
        $errorText = '';
        $pseudo = '';
        //$urlNewConfirmMail = '';
        $token = $request->query->get('token');
        $confirmToken = $confirmTokenRepository->findOneBy(['token' => $token]);

        if ($confirmToken === null) {
            $errorText = 'Token introuvable.';
            $isActivated = false;
            //$userId = -1;
        }
        else if ($confirmToken->getExpireAt() < new \DateTimeImmutable()) {
            $errorText = 'Token expiré.';
            $isActivated = false;
            $entityManager->remove($confirmToken);
            $entityManager->flush();
            //$userId = $confirmToken->getUser()->getId();
        }
        else {
            $isActivated = true;
            $user = $confirmToken->getUser();
            $pseudo = $user->getPseudo();
            $user->setActive(true);
            //$userId = $user->getId();
            //$user->removeConfirmToken($confirmToken);
            foreach ($user->getConfirmTokens() as $ct) {
                $user->removeConfirmToken($ct);
                $entityManager->remove($ct);
            }
            $entityManager->persist($user);
            //$entityManager->remove($confirmToken);
            $entityManager->flush();
        }       

        return $this->render('signin/confirm_signin.html.twig', [
            'controller_name' => 'SignInController',
            'pseudo' => $pseudo,
            'isActivated' => $isActivated,
            'errorText' => $errorText,
            //'userId' => $userId
        ]);
    }

}

?>