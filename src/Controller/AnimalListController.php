<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use App\Repository\AnimalRepository;
use App\Entity\Animal;
use App\Library\CustomLibrary;

class AnimalListController extends AbstractController
{

    private $tokenStorage;
    private $animalRepository;

    public function __construct(TokenStorageInterface $tokenStorage, AnimalRepository $animalRepository)
    {
        $this->tokenStorage = $tokenStorage;
        $this->animalRepository = $animalRepository;
    }

    #[Route('/animals/list', name: 'app_animal_list')]
    public function index(): Response
    {
        $user = $this->tokenStorage->getToken()->getUser(); 
        $animals = $this->animalRepository->findBy(['master' => $user]);
        /*
        foreach ($animals as $animal) { 
            $animal->getCategory()->setName(CustomLibrary::getEmoticonFromCategory($animal->getCategory()->getName()) . " " . $animal->getCategory()->getName());
        }*/
        usort($animals, function ($a, $b) {
            return $a->getId() <=> $b->getId();
        });
        return $this->render('animal_list/index.html.twig', [
            'animals' => $animals,
        ]);
    }
}
