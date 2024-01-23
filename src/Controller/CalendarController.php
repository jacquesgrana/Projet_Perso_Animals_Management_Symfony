<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
//use CalendarBundle\CalendarEvents;
//use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use App\Repository\EventRepository;
use App\Repository\AnimalRepository;

class CalendarController extends AbstractController
{
    //private $eventDispatcher;
    private $tokenStorage;
    private $eventRepository;
    private $animalRepository;

    public function __construct(TokenStorageInterface $tokenStorage, EventRepository $eventRepository, AnimalRepository $animalRepository)
    {
        $this->tokenStorage = $tokenStorage;
        $this->eventRepository = $eventRepository;
        $this->animalRepository = $animalRepository;
    }
    /*
    public function __construct(EventDispatcherInterface $eventDispatcher)
    {
        //$this->eventDispatcher = $eventDispatcher;
    }
    */
    
    #[Route('/calendar', name: 'app_calendar_show')]
    public function index(): Response
    {
        $user = $this->tokenStorage->getToken()->getUser();
        $events = $this->eventRepository->findEventsForUser($user);
        //dd($events);
        $eventsToSend = [];
        
        
        foreach ($events as $event) {
            $animals = $this->animalRepository->findAnimalsByEvent($event);
            $amimalsToSend = [];
            foreach ($animals as $animal) {
                $amimalsToSend[] = $animal->getName();
            }
            $eventsToSend[] = [
                'id' => $event->getId(),
                'name' => $event->getName(),
                'user' => $event->getUser()->getPseudo(),
                'comment' => $event->getComment(),
                'start' => $event->getStart()->format('Y-m-d H:i:s'),
                'duration' => $event->getDuration(),
                'priority' => $event->getPriority()->getName(),
                'category' => $event->getCategory()->getName(),
                'status' => $event->getStatus()->getName(),
                'animals' => $amimalsToSend
            ];
        }
        //dd(json_encode($eventsToSend));
        //$event = new CalendarEvent(new \DateTime(), new \DateTime(), []);
        //$this->eventDispatcher->dispatch($event, CalendarEvents::SET_DATA);
        return $this->render('calendar/index.html.twig', [   
            'eventsToLoad' => json_encode($eventsToSend),
        ]);
    }
}
