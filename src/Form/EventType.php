<?php

namespace App\Form;

use App\Entity\Animal;
use App\Entity\Event;
use App\Entity\EventCategory;
use App\Entity\EventPriority;
use App\Entity\EventStatus;
use App\Entity\User;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use App\Repository\AnimalRepository;

class EventType extends AbstractType
{
    private $tokenStorage;
    private $animalRepository;

    public function __construct(TokenStorageInterface $tokenStorage, AnimalRepository $animalRepository)
    {
        $this->tokenStorage = $tokenStorage;
        $this->animalRepository = $animalRepository;
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $user = $this->tokenStorage->getToken()->getUser();
        $animals = $this->animalRepository->findBy(['master' => $user]);

        $builder
            ->add('name', null, [
                'attr' => ['class' => 'form-control'],
                'label' => 'Nom',
            ])
            ->add('comment', null, [
                'attr' => ['class' => 'form-control'],
                'label' => 'Commentaire',
            ])
            ->add('user', EntityType::class, [
                'class' => User::class,
                'choice_label' => 'pseudo',
                'attr' => ['class' => 'form-control'],
                'disabled' => true,
                'label' => 'Utilisateur',
            ])
            ->add('start', null, [
                'attr' => ['class' => 'form-control'],
                'label' => 'Début',
            ])
            ->add('duration', null, [
                'attr' => ['class' => 'form-control'],
                'label' => 'Durée',
            ])
            ->add('category', EntityType::class, [
                'class' => EventCategory::class,
                'choice_label' => 'name',
                'attr' => ['class' => 'form-control'],               
                'label' => 'Catégorie',
            ])
            ->add('status', EntityType::class, [
                'class' => EventStatus::class,
                'choice_label' => 'name',
                'attr' => ['class' => 'form-control'],
                'label' => 'Status',
            ])
            ->add('priority', EntityType::class, [
                'class' => EventPriority::class,
                'choice_label' => 'name',
                'attr' => ['class' => 'form-control'],
                'label' => 'Priorité',
            ])
            ->add('animals', EntityType::class, [
                'class' => Animal::class,
                'choice_label' => 'name',
                'multiple' => true,
                'attr' => ['class' => 'form-control'],
                'choices' => $animals,
                'required' => false,
                'label' => 'Animaux',
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Event::class,
        ]);
    }
}
