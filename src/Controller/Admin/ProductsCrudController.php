<?php

namespace App\Controller\Admin;

use App\Entity\Distributors;
use App\Entity\Products;
use App\Entity\References;
use App\Form\ReferencesType;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ImageField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField;
use EasyCorp\Bundle\EasyAdminBundle\Field\NumberField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Form\Type\FileUploadType;

class ProductsCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Products::class;
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            IntegerField::new('id', 'ID')->onlyOnIndex(),
            TextField::new('name_product', "Product's name"),
            TextField::new('description_product', "Product's description"),
            NumberField::new('price_product'),           
            ImageField::new('image_product')
                ->setBasePath('/img')
                ->setUploadDir('public/img/')
                ->setFormType(FileUploadType::class)
                ->setRequired(true),
            BooleanField::new('availability_product', "Is product available"),
            AssociationField::new("category_id", "product's category"),
            AssociationField::new("product_distributors", "distributor list"),
            IntegerField::new("reference_id", "reference number")->setFormType(ReferencesType::class),
            AssociationField::new("seller", "seller")
        ];
    }
}
