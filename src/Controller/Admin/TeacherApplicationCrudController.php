<?php

namespace App\Controller\Admin;

use App\Entity\TeacherApplication;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\EmailField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class TeacherApplicationCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return TeacherApplication::class;
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id')->hideOnForm(),
            TextField::new('fullName'),
            EmailField::new('email'),
            TextField::new('primaryTrack'),
            TextField::new('experienceLevel'),
            ChoiceField::new('status')->setChoices([
                'NEW' => 'NEW',
                'APPROVED' => 'APPROVED',
                'REJECTED' => 'REJECTED',
            ]),
            TextEditorField::new('bio')->hideOnIndex(),
            TextField::new('links')->hideOnIndex(),
            DateTimeField::new('createdAt')->hideOnForm(),
        ];
    }
}
