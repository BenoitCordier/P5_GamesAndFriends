<?php

namespace App\Form;

use App\Entity\Game;
use Doctrine\ORM\QueryBuilder;
use App\Repository\GameRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

class UserGameType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('games', EntityType::class, [
                'class' => Game::class,
                'query_builder' => function (GameRepository $r): QueryBuilder {
                    return $r->createQueryBuilder('i')
                        ->orderBy('i.gameName', 'ASC');
                },
                'choice_label' => 'gameName',
                'multiple' => true,
                'expanded' => true,
                'attr' => [
                    'class' => 'form-control'
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
                'label' => 'Ajouter'
            ])
        ;
    }
}
