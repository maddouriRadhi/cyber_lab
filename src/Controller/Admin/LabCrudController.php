<?php

namespace App\Controller\Admin;

use App\Entity\Lab;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextareaField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class LabCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Lab::class;
    }

    public function configureFields(string $pageName): iterable
    {
        yield IdField::new('id')->onlyOnIndex();

        yield TextField::new('title');
        yield TextareaField::new('description')->hideOnIndex();

        yield AssociationField::new('teacher')->setRequired(false);

        yield ChoiceField::new('difficulty')->setChoices([
            'Easy' => 'Easy',
            'Medium' => 'Medium',
            'Hard' => 'Hard',
        ]);

        yield IntegerField::new('points');
        yield TextField::new('flag')->hideOnIndex();
        yield BooleanField::new('published');

        yield DateTimeField::new('scheduledAt')->setRequired(false);
        yield TextField::new('meetingLink')->setRequired(false)->hideOnIndex();
        yield IntegerField::new('maxSeats')->setRequired(false)->hideOnIndex();

        yield TextField::new('slug')->onlyOnIndex();
        yield DateTimeField::new('createdAt')->onlyOnIndex();
    }
}
