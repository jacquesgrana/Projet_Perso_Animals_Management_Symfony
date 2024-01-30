<?php

namespace App\Form;

use App\Entity\Animal;
use App\Entity\AnimalCategory;
use App\Entity\Event;
use App\Entity\Gender;
use App\Entity\User;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use App\Repository\EventRepository;

class AnimalType extends AbstractType
{
    private $tokenStorage;
    private $eventRepository;

    public function __construct(TokenStorageInterface $tokenStorage, EventRepository $eventRepository)
    {
        $this->tokenStorage = $tokenStorage;
        $this->eventRepository = $eventRepository;
    }


    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $user = $this->tokenStorage->getToken()->getUser();
        $events = $this->eventRepository->findEventsForUser($user);
        $builder
            ->add('name', null, [
                'attr' => ['class' => 'form-control'],
                'label' => 'Nom',
            ])
            ->add('comment', null, [
                'attr' => ['class' => 'form-control'],
                'label' => 'Commentaire',
            ])
            ->add('birth', null, [
                'attr' => ['class' => 'form-control'],
                'label' => 'Naissance',
            ])
            ->add('gender', EntityType::class, [
                'class' => Gender::class,
                'choice_label' => 'name',
                'attr' => ['class' => 'form-control'],
                'label' => 'Genre',
                'expanded' => true,
            ])
            ->add('category', EntityType::class, [
                'class' => AnimalCategory::class,
                'choice_label' => function (AnimalCategory $category) {
                    return $category->getEmoticon() . ' ' . $category->getName();
                },
                'attr' => ['class' => 'form-control'],
                'label' => 'Catégorie',
                'expanded' => true,
            ])
            ->add('master', EntityType::class, [
                'class' => User::class,
                'choice_label' => 'pseudo',
                'attr' => ['class' => 'form-control'],
                'disabled' => true,
                'label' => 'Maître',
            ])
            ->add('events', EntityType::class, [
                'class' => Event::class,
                'choice_label' => 'name',
                'multiple' => true,
                'expanded' => true,
                'attr' => ['class' => 'form-control'],
                'choices' => $events,
                'required' => false,
                'label' => 'Evenement(s)',
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Animal::class,
        ]);
    }
}
