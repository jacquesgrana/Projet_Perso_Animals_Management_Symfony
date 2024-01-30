<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class MailManagerController extends AbstractController
{
    #[Route('/mail/manager', name: 'app_mail_manager')]
    public function index(): Response
    {
        return $this->render('mail_manager/index.html.twig', [
            'controller_name' => 'MailManagerController',
        ]);
    }
}
