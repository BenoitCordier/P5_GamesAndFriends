<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;

class UserPasswordType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
        ->add('plainPassword', PasswordType::class, [
                'attr' => [
                    'class' => 'password-field form-control'],
                'label' => 'Mot de passe',
                'label_attr' => [
                    'class' => 'form-label mt-4'
                ],
                'constraints' => [
                    new Assert\NotBlank()
                ]
        ])
        ->add('newPassword', RepeatedType::class, [
            'type' => PasswordType::class,
            'required' => true,
            'first_options'  => ['label' => 'Nouveau mot de passe'],
            'second_options' => ['label' => 'Confirmation du nouveau mot de passe'],
            'invalid_message' => 'Les mots de passes ne sont pas identiques.',
            'options' => [
                'attr' => [
                    'class' => 'password-field form-control'],
                'label_attr' => [
                    'class' => 'form-label mt-4'
            ]]
        ])
        ->add('submit', SubmitType::class, [
            'attr' => [
                'class' => 'btn btn-outline-primary mt-4'
            ],
            'label' => 'Valider'
        ])
        ;
    }
}
