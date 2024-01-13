<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use App\Repository\AnimalRepository;
use App\Entity\Animal;

class AnimalListController extends AbstractController
{

    private $tokenStorage;
    private $animalRepository;

    public function __construct(TokenStorageInterface $tokenStorage, AnimalRepository $animalRepository)
    {
        $this->tokenStorage = $tokenStorage;
        $this->animalRepository = $animalRepository;
    }

    #[Route('/animal/list', name: 'app_animal_list')]
    public function index(): Response
    {
        $user = $this->tokenStorage->getToken()->getUser(); 
        $animals = $this->animalRepository->findBy(['master' => $user]);
        /*
        dump($animals);

        foreach ($animals as $animal) {
            dump($animal->getCategory()->getName());
        }
        die('test');
        */
        return $this->render('animal_list/index.html.twig', [
            'animals' => $animals,
        ]);
    }
}
