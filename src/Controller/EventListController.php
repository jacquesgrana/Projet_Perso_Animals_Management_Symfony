<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use App\Repository\EventRepository;
use App\Entity\Event;
class EventListController extends AbstractController
{
    private $tokenStorage;
    private $eventRepository;

    public function __construct(TokenStorageInterface $tokenStorage, EventRepository $eventRepository)
    {
        $this->tokenStorage = $tokenStorage;
        $this->eventRepository = $eventRepository;
    }

    #[Route('/events/list', name: 'app_event_list')]
    public function index(): Response
    {
        $user = $this->tokenStorage->getToken()->getUser();
        $events = $this->eventRepository->findEventsForUser($user);
        //dump($events);
        //die('test');
        return $this->render('event_list/index.html.twig', [
            'events' => $events,
        ]);
    }
}
