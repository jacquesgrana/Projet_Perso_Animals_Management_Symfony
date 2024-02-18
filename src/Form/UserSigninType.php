<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\ButtonType;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

class UserSigninType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('email', null, [
                'attr' => ['class' => 'form-control'],
                'label' => 'Email',
                'constraints' => [
                    new \Symfony\Component\Validator\Constraints\Email()
                ],
                'required' => true
                ])
            ->add('password', PasswordType::class, [
                'attr' => [
                    'class' => 'login-input',
                ],
                'label' => 'Mot de passe',
                'required' => true
            ])
            ->add('firstname', null, [
                'attr' => ['class' => 'form-control'],
                'label' => 'Prénom',
                'required' => true
            ])
            ->add('lastname', null, [
                'attr' => ['class' => 'form-control'],
                'label' => 'Nom',
                'required' => true
            ])
            ->add('pseudo', null, [
                'attr' => ['class' => 'form-control'],
                'label' => 'Pseudo',
                'required' => true
            ])
            ->add('birth', null, [
                'attr' => ['class' => 'form-control'],
                'label' => 'Naissance',
                'required' => true
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
