<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use App\Repository\EventRepository;
use Doctrine\ORM\EntityManagerInterface;
use App\Library\WeekPatternLibrary;

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

    // /mail/day/get

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
}
