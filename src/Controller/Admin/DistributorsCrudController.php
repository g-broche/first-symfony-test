<?php

namespace App\Controller\Admin;

use App\Entity\Distributors;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;

class DistributorsCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Distributors::class;
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
