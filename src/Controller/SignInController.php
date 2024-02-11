<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\ConfirmToken;
use App\Form\UserTypeSignin;
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
        $form = $this->createForm(UserTypeSignin::class, $user);
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
                    $this->addFlash('success', 'Email envoyé');
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
        // récupérer le token dans l'url
        $token = $request->query->get('token');
        // récupérer le ConfirmToken dans la base de données à partir du token
        //dd($confirmTokenRepository);
        //findByToken
        //$confirmToken = $confirmTokenRepository->findByToken($token);
        $isActivated = false;
        $errorText = '';
        $pseudo = '';
        $confirmToken = $confirmTokenRepository->findOneBy(['token' => $token]);
        //dd($confirmToken);
        // si ConfirmToken n'existe pas, renvoyer une erreur
        if ($confirmToken === null) {
            //throw new \Exception('ConfirmToken does not exist');

            $errorText = 'Token introuvable.';
            $isActivated = false;

        }
        // sinon si le ConfirmToken est expire, renvoyer une erreur
        else if ($confirmToken->getExpireAt() < new \DateTimeImmutable()) {
            //throw new \Exception('ConfirmToken expired');
            $errorText = 'Token expiré.';
            $isActivated = false;
        }

        else {
            $isActivated = true;
            $user = $confirmToken->getUser();
            $pseudo = $user->getPseudo();
            $user->setActive(true);
            $user->removeConfirmToken($confirmToken);
            $entityManager->persist($user);
            $entityManager->remove($confirmToken);
            $entityManager->flush();
        }

        
        
        
        // sinon

            // récupérer l'utilisateur correspondant au ConfirmToken
            // récupérer le pseudo de l'utilisateur
            // set user active
            // set user confirmToken a null
            // supprimer le ConfirmToken de la base de données
            // persist user et flush
        

        return $this->render('signin/confirm_signin.html.twig', [
            'controller_name' => 'SignInController',
            'pseudo' => $pseudo,
            'isActivated' => $isActivated,
            'errorText' => $errorText
        ]);
    }

}

?>