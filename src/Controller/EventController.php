<?php

namespace App\Controller;

use App\Entity\Animal;

use App\Entity\Event;
use App\Form\EventType;
use App\Repository\EventRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\Common\Collections\ArrayCollection;

#[Route('/event')]
class EventController extends AbstractController
{
    #[Route('/', name: 'app_event_index', methods: ['GET'])]
    public function index(EventRepository $eventRepository): Response
    {
        return $this->render('event/index.html.twig', [
            'events' => $eventRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_event_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $event = new Event();
        $user = $this->getUser();
        $event->setUser($user);
        $form = $this->createForm(EventType::class, $event);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            
            $formData = $form->getData();
            $animals = $formData->getAnimals();

            if($animals) {
                foreach ($animals as $animal) {
                    $animal->addEvent($event);
                    $event->addAnimal($animal);
                    $entityManager->persist($animal);
                }
            }

            $entityManager->persist($event);
            $entityManager->flush();

            return $this->redirectToRoute('app_event_list', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('event/new.html.twig', [
            'event' => $event,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_event_show', methods: ['GET'])]
    public function show(Event $event): Response
    {
        return $this->render('event/show.html.twig', [
            'event' => $event,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_event_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Event $event, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(EventType::class, $event);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $formData = $form->getData();
            $newAnimals = $formData->getAnimals();
            $oldAnimals = $entityManager->getRepository(Animal::class)->findAnimalsByEvent($event);
            $oldAnimalsCollection = new ArrayCollection($oldAnimals);

            $commonAnimals = $newAnimals->filter(function ($newAnimal) use ($oldAnimalsCollection) {
                return $oldAnimalsCollection->contains($newAnimal);
            });

            $toAddAnimals = $newAnimals->filter(function ($newAnimal) use ($commonAnimals) {
                return !$commonAnimals->contains($newAnimal);
            });

            $toRemoveAnimals = $oldAnimalsCollection->filter(function ($oldAnimal) use ($commonAnimals) {
                return !$commonAnimals->contains($oldAnimal);
            });

            if (!$toAddAnimals->isEmpty()) {
                foreach ($toAddAnimals as $animal) {
                    $animal->addEvent($event);
                    $event->addAnimal($animal);
                    $entityManager->persist($animal);
                }
            }

            if (!$toRemoveAnimals->isEmpty()) {
                foreach ($toRemoveAnimals as $animal) {
                    $animal->removeEvent($event);
                    $event->removeAnimal($animal);
                    $entityManager->persist($animal);
                }
            }
            

            $entityManager->persist($event);
            $entityManager->flush();

            return $this->redirectToRoute('app_event_list', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('event/edit.html.twig', [
            'event' => $event,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_event_delete', methods: ['POST'])]
    public function delete(Request $request, Event $event, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$event->getId(), $request->request->get('_token'))) {
            if($event->getAnimals()) {
                foreach ($event->getAnimals() as $animal) {
                    $animal->removeEvent($event);
                    $entityManager->persist($animal);
                }
            }
            
            $entityManager->remove($event);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_event_list', [], Response::HTTP_SEE_OTHER);
    }
}
