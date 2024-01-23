<?php
namespace App\EventSubscriber;

use CalendarBundle\CalendarEvents;
use CalendarBundle\Entity\Event;
use CalendarBundle\Event\CalendarEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use App\Repository\EventRepository;
use App\Entity\Event as EventObject;
class CalendarSubscriber implements EventSubscriberInterface
{

    private $tokenStorage;
    private $eventRepository;

    public function __construct(TokenStorageInterface $tokenStorage, EventRepository $eventRepository)
    {
        $this->tokenStorage = $tokenStorage;
        $this->eventRepository = $eventRepository;
    }

    public static function getSubscribedEvents()
    {
        return [
            CalendarEvents::SET_DATA => 'onCalendarSetData',
        ];
    }

    public function onCalendarSetData(CalendarEvent $calendar)
    {
        $start = $calendar->getStart();
        $end = $calendar->getEnd();
        $filters = $calendar->getFilters();
        $user = $this->tokenStorage->getToken()->getUser();
        $events = $this->eventRepository->findEventsForUser($user);
        //die('test');
        foreach ($events as $event) {
            $startDate = clone $event->getStart();
            $endDate = $startDate->modify('+1 day');
            $calendar->addEvent(new Event($event->getName(), $startDate, $endDate, $event->getId(), [
                'color' => '#FF0000',
            ]));
        }
        // You may want to make a custom query from your database to fill the calendar
        //dd($events);
        /*
        $calendar->addEvent(new Event(
            'Event 1',
            new \DateTime('Tuesday this week'),
            new \DateTime('Wednesdays this week')
        ));

        // If the end date is null or not defined, it creates a all day event
        $calendar->addEvent(new Event(
            'All day event',
            new \DateTime('Friday this week')
        ));
        */
    }
}
?>