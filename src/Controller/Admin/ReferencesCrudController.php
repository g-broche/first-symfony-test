<?php

namespace App\Controller\Admin;

use App\Entity\References;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;

class ReferencesCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return References::class;
    }

    /*
    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id'),
            TextField::new('title'),
            TextEditorField::new('description'),
        ];
    }
    */
}
