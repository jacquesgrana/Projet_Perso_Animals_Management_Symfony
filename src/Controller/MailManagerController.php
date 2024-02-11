<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use App\Repository\EventRepository;
use Doctrine\ORM\EntityManagerInterface;
use App\Library\WeekPatternLibrary;
use App\Service\MailerService;

class MailManagerController extends AbstractController
{
    #[Route('/mail/manager', name: 'app_mail_manager')]
    public function index(): Response
    {
        // recuperer email et pseudo de l'user
        $user = $this->getUser();
        $email = $user->getEmail();
        $pseudo = $user->getPseudo();
        $currentDate = new \DateTime();
        return $this->render('mail_manager/index.html.twig', [
            'userEmail' => $email, 
            'userPseudo' => $pseudo,
            'currentDate' => $currentDate->format('Y-m-d'),
        ]);
    }

    #[Route('/mail/day/send', name: 'app_mail_day_send')]
    public function sendDayEmail(
        Request $request, 
        MailerService $mailer, 
        EventRepository $eventRepository
        ): Response
    {
        // Convertir le contenu de la requête en tableau PHP
        $data = json_decode($request->getContent(), true);

        // Récupérer emailDest et day depuis le tableau PHP
        $emailDest = $data['emailDest'] ?? null;
        $day = $data['day'] ?? null;
        //dd($emailDest, $day);

        // récupérer les events du jour selon $day
        $user = $this->getUser();
        // recuperer 'day' de la query string

        $eventsSource = $eventRepository->findEventsForUser($user);

        // générer les $events à partir de $eventsSource
        // appeler fonction de WeekPatternLibrary

        $eventsToFilter = [];
        $eventsToFilter = WeekPatternLibrary::getEventsArrayFromSource($eventsSource);

        $dayObject = new \DateTime($day);
        $dayObjectPlus1 = clone $dayObject;
        $dayObjectPlus1->modify('+1 day');

        // filtrer les $events par $dayObject et $dayObjectPlus1
        $events = [];
        foreach ($eventsToFilter as $event) {
            if ($event['start'] >= $dayObject->format('Y-m-d') && $event['start'] < $dayObjectPlus1->format('Y-m-d')) {
                $events[] = $event;
            }
        }
        //dd($emailDest, $events);
        $result = $mailer->GenerateAndSendEmail($emailDest, $events, $day, 'DAY');
        // appeler service MailerService($events)

        return new JsonResponse(['message' => $result]);
    }

    #[Route('/mail/week/send', name: 'app_mail_week_send')]
    public function sendWeekEmail(Request $request, MailerService $mailer, EventRepository $eventRepository): Response
    {
        $data = json_decode($request->getContent(), true);
        $emailDest = $data['emailDest'] ?? null;
        $day = $data['day'] ?? null;
        $user = $this->getUser();
        $eventsSource = $eventRepository->findEventsForUser($user);
        $eventsToFilter = [];
        $eventsToFilter = WeekPatternLibrary::getEventsArrayFromSource($eventsSource);

        $dayObject = new \DateTime($day);
        $monday = clone $dayObject;
        $monday->modify('monday this week');
        $newtMonday = clone $monday;
        $newtMonday->modify('+7 day');

        $events = [];
        foreach ($eventsToFilter as $event) {
            if ($event['start'] >= $monday->format('Y-m-d') && $event['start'] < $newtMonday->format('Y-m-d')) {
                $events[] = $event;
            }
        }
        $result = $mailer->GenerateAndSendEmail($emailDest, $events, $day, 'WEEK');

        return new JsonResponse(['message' => $result]);
    }

    #[Route('/mail/month/send', name: 'app_mail_month_send')]
    public function sendMonthEmail(Request $request, MailerService $mailer, EventRepository $eventRepository): Response
    {
        $data = json_decode($request->getContent(), true);
        $emailDest = $data['emailDest'] ?? null;
        $day = $data['day'] ?? null;
        $user = $this->getUser();
        $eventsSource = $eventRepository->findEventsForUser($user);
        $eventsToFilter = [];
        $eventsToFilter = WeekPatternLibrary::getEventsArrayFromSource($eventsSource);

        $dayObject = new \DateTime($day);
        $firstDayOfTheMonth = clone $dayObject;
        $firstDayOfTheMonth->modify('first day of this month');
        $firstDayOfTheNextMonth = clone $firstDayOfTheMonth;
        $firstDayOfTheNextMonth->modify('+1 month');

        $events = [];
        foreach ($eventsToFilter as $event) {
            if ($event['start'] >= $firstDayOfTheMonth->format('Y-m-d') && $event['start'] < $firstDayOfTheNextMonth->format('Y-m-d')) {
                $events[] = $event;
            }
        }
        $result = $mailer->GenerateAndSendEmail($emailDest, $events, $day, 'MONTH');

        return new JsonResponse(['message' => $result]);

    }

    #[Route('/mail/day/get', name: 'app_mail_day_events_by_day')]
    public function getDayEventsByDayAndUser(EventRepository $eventRepository): JsonResponse
    {
        // Récupérer email et pseudo de l'utilisateur
        $user = $this->getUser();
        // recuperer 'day' de la query string
        $day = $_GET['day'];

        $eventsSource = $eventRepository->findEventsForUser($user);

        // générer les $events à partir de $eventsSource
        // appeler fonction de WeekPatternLibrary

        $eventsToFilter = [];
        $eventsToFilter = WeekPatternLibrary::getEventsArrayFromSource($eventsSource);

        $dayObject = new \DateTime($day);
        $dayObjectPlus1 = clone $dayObject;
        $dayObjectPlus1->modify('+1 day');

        // filtrer les $events par $dayObject et $dayObjectPlus1
        $events = [];
        foreach ($eventsToFilter as $event) {
            if ($event['start'] >= $dayObject->format('Y-m-d') && $event['start'] < $dayObjectPlus1->format('Y-m-d')) {
                $events[] = $event;
            }
        }
        return new JsonResponse($events);
        
        //return new JsonResponse(['message' => 'tout est ok!']);
    }
    
    #[Route('/mail/week/get', name: 'app_mail_week_events_by_day')]
    public function getWeekEventsByDayAndUser(EventRepository $eventRepository): JsonResponse
    {
        // Récupérer email et pseudo de l'utilisateur
        $user = $this->getUser();
        // recuperer 'day' de la query string
        $day = $_GET['day'];

        $eventsSource = $eventRepository->findEventsForUser($user);

        $eventsToFilter = [];
        $eventsToFilter = WeekPatternLibrary::getEventsArrayFromSource($eventsSource);

        $dayObject = new \DateTime($day);
        $mondayOfTheWeek = clone $dayObject;
        $mondayOfTheWeek->modify('monday this week');
        $dayObjectPlus7 = clone $mondayOfTheWeek;
        $dayObjectPlus7->modify('+7 day');
        //dd($dayObject, $mondayOfTheWeek, $dayObjectPlus7);
        $events = [];
        foreach ($eventsToFilter as $event) {
            if ($event['start'] >= $mondayOfTheWeek->format('Y-m-d') && $event['start'] < $dayObjectPlus7->format('Y-m-d')) {
                $events[] = $event;
            }
        }
        return new JsonResponse($events);

        //return new JsonResponse(['message' => 'tout est ok!']);
    }

    #[Route('/mail/month/get', name: 'app_mail_month_events_by_day')]
    public function getMonthEventsByDayAndUser(EventRepository $eventRepository): JsonResponse
    {
        // Récupérer email et pseudo de l'utilisateur
        $user = $this->getUser();
        // recuperer 'day' de la query string
        $day = $_GET['day'];

        $eventsSource = $eventRepository->findEventsForUser($user);

        $eventsToFilter = [];
        $eventsToFilter = WeekPatternLibrary::getEventsArrayFromSource($eventsSource);

        $dayObject = new \DateTime($day);
        // calculate the first day of the month

        $startMonth = clone $dayObject;
        $startMonth->modify('first day of this month');
        $dayObjectPlus1Month = clone $startMonth;
        $dayObjectPlus1Month->modify('+1 month');
        //dd($dayObject, $mondayOfTheWeek, $dayObjectPlus7);
        $events = [];
        foreach ($eventsToFilter as $event) {
            if ($event['start'] >= $startMonth->format('Y-m-d') && $event['start'] < $dayObjectPlus1Month->format('Y-m-d')) {
                $events[] = $event;
            }
        }
        return new JsonResponse($events);

        //return new JsonResponse(['message' => 'tout est ok!']);
    }
}
