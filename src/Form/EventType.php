<?php

namespace App\Form;

use App\Entity\Game;
use App\Entity\Event;
use Doctrine\ORM\QueryBuilder;
use App\Repository\GameRepository;
use Symfony\Component\Form\AbstractType;
use Karser\Recaptcha3Bundle\Form\Recaptcha3Type;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Karser\Recaptcha3Bundle\Validator\Constraints\Recaptcha3;

class EventType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class, [
                'attr' => [
                    'class' => 'form-control',
                    'minlength' => '3',
                    'maxlenght' => '50'
                ],
                'label' => "Nom de l'évènement",
                'label_attr' => [
                    'class' => 'form-label mt-4'
                ],
                'constraints' => [
                    new Assert\Length(['min' => 3, 'max' => 50]),
                    new Assert\NotBlank()
                ]
            ])
            ->add('eventDescription', TextareaType::class, [
                'attr' => [
                    'class' => 'form-control'
                ],
                'label' => "Description",
                'label_attr' => [
                    'class' => 'form-label mt-4'
                ],
                'constraints' => [
                    new Assert\NotBlank()
                ]
            ])
            ->add('eventStartAt', DateTimeType::class, [
                'widget' => 'single_text',
                'attr' => [
                    'class' => 'form-control'
                ],
                'label' => "Date de début",
                'label_attr' => [
                    'class' => 'form-label mt-4'
                ],
                'constraints' => [
                    new Assert\NotBlank(),
                    new Assert\GreaterThan('now')
                ]

            ])
            ->add('eventEndAt', DateTimeType::class, [
                'widget' => 'single_text',
                'attr' => [
                    'class' => 'form-control'
                ],
                'label' => "Date de fin",
                'label_attr' => [
                    'class' => 'form-label mt-4'
                ],
                'constraints' => [
                    new Assert\NotBlank(),
                    new Assert\GreaterThan([
                        'propertyPath' => 'parent.all[eventStartAt].data'
                    ])
                ]
            ])
            ->add('location', TextType::class, [
                'attr' => [
                    'class' => 'form-control',
                    'minlength' => '2',
                    'maxlenght' => '255',
                    'id' => 'signin_adress',
                ],
                'label' => "Adresse",
                'label_attr' => [
                    'class' => 'form-label mt-4'
                ],
                'constraints' => [
                    new Assert\Length(['min' => 2, 'max' => 255]),
                    new Assert\NotBlank()
                ]
            ])
            ->add('eventMaxPlayer', IntegerType::class, [
                'attr' => [
                    'class' => 'form-control',
                    'min' => '1',
                    'max' => '1000'
                ],
                'label' => "Nombre de participants maximum",
                'label_attr' => [
                    'class' => 'form-label mt-4'
                ],
                'constraints' => [
                    new Assert\NotBlank(),
                    new Assert\Positive(),
                    new Assert\LessThanOrEqual(1000)
                ]
            ])
            ->add('eventGame', EntityType::class, [
                'class' => Game::class,
                'query_builder' => function (GameRepository $r): QueryBuilder {
                    return $r->createQueryBuilder('i')
                        ->orderBy('i.gameName', 'ASC');
                },
                'choice_label' => 'gameName',
                'multiple' => false,
                'expanded' => false,
                'attr' => [
                    'class' => 'form-select'
                ],
                'label' => "Jeux",
                'label_attr' => [
                    'class' => 'form-label mt-4'
                ]
            ])
            ->add('submit', SubmitType::class, [
                'attr' => [
                    'class' => 'btn btn-outline-primary mt-4 mb-4'
                ],
                'label' => 'Valider'
            ])
            ->add('captcha', Recaptcha3Type::class, [
                'constraints' => new Recaptcha3(),
                'action_name' => 'contact',
                'locale' => 'fr',
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
