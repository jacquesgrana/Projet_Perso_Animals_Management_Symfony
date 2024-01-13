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

class AnimalType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name')
            ->add('comment')
            ->add('birth')
            ->add('gender', EntityType::class, [
                'class' => Gender::class,
'choice_label' => 'name',
            ])
            ->add('category', EntityType::class, [
                'class' => AnimalCategory::class,
'choice_label' => 'name',
            ])
            ->add('master', EntityType::class, [
                'class' => User::class,
'choice_label' => 'pseudo',
            ])
            ->add('events', EntityType::class, [
                'class' => Event::class,
'choice_label' => 'name',
'multiple' => true,
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
