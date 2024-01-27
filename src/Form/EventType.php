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
use Symfony\Component\Validator\Constraints\Range;
use App\Library\WeekPatternLibrary;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use App\Form\DataTransformer\WeekPatternToStringTransformer;
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
            ->add('patternsNumber', null, [
                'attr' => ['class' => 'form-control'],
                'label' => 'Nb fois (\'0\' -> ∞ infini)',
                'constraints' => [
                    new Range([
                        'min' => 0,
                        'max' => 99,
                        'minMessage' => 'La valeur minimale autorisée est {{ limit }}.',
                        'maxMessage' => 'La valeur maximale autorisée est {{ limit }}.',
                    ]),
                ],
            ])
            /*
            ->add('weekPattern', null, [
                'attr' => ['class' => 'form-control'],
                'label' => 'Pattern de la semaine',
                // Ajoutez d'autres options si nécessaire
            ])*/
            
            ->add('weekPattern', ChoiceType::class, [
                'attr' => ['class' => 'form-control'],
                'label' => 'Pattern de la semaine',
                'choices' => [
                    'Lundi' => 'Lundi',
                    'Mardi' => 'Mardi',
                    'Mercredi' => 'Mercredi',
                    'Jeudi' => 'Jeudi',
                    'Vendredi' => 'Vendredi',
                    'Samedi' => 'Samedi',
                    'Dimanche' => 'Dimanche',
                ],
                'multiple' => true,
                'expanded' => true,
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
                'expanded' => true,
            ])
        ;
        $builder->get('weekPattern')->addModelTransformer(new WeekPatternToStringTransformer());
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Event::class,
        ]);
    }
}
