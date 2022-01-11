<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\TextType;

class RegistrationFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('email',
                EmailType::class ,
                ['attr'=>[
                    'placeholder' => "Adresse email",]
                ]
            )

            ->add('password',
                PasswordType::class,
                ['attr' => [
                    'placeholder' => "Mot de passe",
                ]]
            )

            ->add(
                'CofirmePassword',
                PasswordType::class,
                ['attr' => [
                    'placeholder' => "Confirmer Mot de passe",
                ]]
            )
            ->add('nom' , TextType :: class , [
                'attr'=>[
                    'placeholder'=>"Nom"
                ]
            ])
            ->add('prenom', TextType :: class , [
                'attr'=>[
                    'placeholder'=>"Prenom"
                ]
            ]);

    }

    public function getBlockPrefix()
    {
        return '';
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}