<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

class UserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('email', null, [
                'attr' => ['class' => 'form-control'],
                'label' => 'Email',
            ])
            ->add('password', null, [
                'attr' => ['class' => 'form-control'],
                'label' => 'Mot de passe',
            ])
            ->add('firstname', null, [
                'attr' => ['class' => 'form-control'],
                'label' => 'Prénom',
            ])
            ->add('lastname', null, [
                'attr' => ['class' => 'form-control'],
                'label' => 'Nom',
            ])
            ->add('pseudo', null, [
                'attr' => ['class' => 'form-control'],
                'label' => 'Pseudo',
            ])
            ->add('birth', null, [
                'attr' => ['class' => 'form-control'],
                'label' => 'Naissance',
            ])
            ->add('active', null, [
                'attr' => ['class' => 'form-control'],
                'label' => 'Actif',
                'required' => true
            ])
            ->add('roles', ChoiceType::class, [
                'attr' => ['class' => 'form-control'],
                'label' => 'Rôles',
                'choices' => [
                    'ROLE_USER' => 'ROLE_USER',
                    'ROLE_ADMIN' => 'ROLE_ADMIN',
                    // Ajoutez  ici d'autres rôles si nécessaire
                ],
                'expanded' => true,
                'multiple' => true,
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
