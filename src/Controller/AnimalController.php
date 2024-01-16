<?php

namespace App\Controller;

use App\Entity\Animal;
use App\Entity\Event;
use App\Form\AnimalType;
use App\Repository\AnimalRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\Common\Collections\ArrayCollection;


#[Route('/animal')]
class AnimalController extends AbstractController
{
    #[Route('/', name: 'app_animal_index', methods: ['GET'])]
    public function index(AnimalRepository $animalRepository): Response
    {
        return $this->render('animal/index.html.twig', [
            'animals' => $animalRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_animal_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $animal = new Animal();
        // Récupérer l'utilisateur en cours
        $user = $this->getUser();

        // Fixer l'utilisateur en cours comme maître de l'animal
        $animal->setMaster($user);
        $form = $this->createForm(AnimalType::class, $animal);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $formData = $form->getData();
            $events = $formData->getEvents();
            if($events) {
                foreach ($events as $event) {
                    $event->addAnimal($animal);
                    $animal->addEvent($event);
                    $entityManager->persist($event);
                }
            }


            $entityManager->persist($animal);
            $entityManager->flush();

            return $this->redirectToRoute('app_animal_list', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('animal/new.html.twig', [
            'animal' => $animal,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_animal_show', methods: ['GET'])]
    public function show(Animal $animal): Response
    {
        return $this->render('animal/show.html.twig', [
            'animal' => $animal,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_animal_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Animal $animal, EntityManagerInterface $entityManager): Response
    {

        $form = $this->createForm(AnimalType::class, $animal);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $formData = $form->getData();
            $newEvents = $formData->getEvents();

            $oldEvents = $entityManager->getRepository(Event::class)->findEventsByAnimal($animal);

            $oldEventsCollection = new ArrayCollection($oldEvents);
            $commonEvents = $newEvents->filter(function ($newEvent) use ($oldEventsCollection) {
            return $oldEventsCollection->contains($newEvent);
        });


        $toAddEvents = $newEvents->filter(function ($newEvent) use ($commonEvents) {
            return !$commonEvents->contains($newEvent);
        });

        $toRemoveEvents = $oldEventsCollection->filter(function ($oldEvent) use ($commonEvents) {
            return !$commonEvents->contains($oldEvent);
        });
            
        foreach ($toAddEvents as $event) {
            $event->addAnimal($animal);
            $animal->addEvent($event);
            $entityManager->persist($event);
        }
        foreach ($toRemoveEvents as $event) {
            $event->removeAnimal($animal);
            $animal->removeEvent($event);
            if($event->getAnimals()->isEmpty()) {
                $entityManager->remove($event);
            }
            else {
                $entityManager->persist($event);
            }
        }

            $entityManager->persist($animal);
            $entityManager->flush();

            return $this->redirectToRoute('app_animal_list', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('animal/edit.html.twig', [
            'animal' => $animal,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_animal_delete', methods: ['POST'])]
    public function delete(Request $request, Animal $animal, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$animal->getId(), $request->request->get('_token'))) {
            if($animal->getEvents()) {
                foreach ($animal->getEvents() as $event) {
                    $event->removeAnimal($animal);
                    //$animal->removeEvent($event);
                    if($event->getAnimals()->isEmpty()) {
                        $entityManager->remove($event);
                    }
                    else {
                        $entityManager->persist($event);
                    }
                }
            }
            
            $entityManager->remove($animal);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_animal_list', [], Response::HTTP_SEE_OTHER);
    }
}
