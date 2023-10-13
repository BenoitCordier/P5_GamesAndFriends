<?php

namespace App\Controller\Admin;

use App\Entity\Event;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;

class EventCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Event::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInPlural('Evenements')
            ->setEntityLabelInSingular('Evenement')
            ->setPageTitle('index', 'Games and Friends - Administration - Evenements')
        ;
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id')
                ->hideOnForm(),
            TextField::new('name'),
            TextField::new('eventGame')
                ->setFormTypeOption('disabled', 'disabled')
                ->hideOnIndex(),
            TextEditorField::new('eventDescription')
                ->hideOnIndex(),
            DateTimeField::new('eventStartAt')
                ->hideOnIndex(),
            DateTimeField::new('eventEndAt')
                ->hideOnIndex(),
            TextField::new('eventAdmin')
                ->setFormTypeOption('disabled', 'disabled'),
            IntegerField::new('eventMaxPlayer')
                ->hideOnIndex(),
            TextEditorField::new('location')
                ->hideOnIndex(),
        ];
    }

    public function configureActions(Actions $actions): Actions
    {
        return $actions
            ->add(Crud::PAGE_INDEX, Action::DETAIL)
        ;
    }
}
