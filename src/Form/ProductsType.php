<?php

namespace App\Form;

use App\Entity\Categories;
use App\Entity\Distributors;
use App\Entity\Products;
use Doctrine\ORM\Mapping\Entity;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ProductsType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name_product')
            ->add('description_product')
            ->add('price_product')
            ->add('availability_product')
            ->add('reference_id', ReferencesType::class, [
                'required'=>true
            ])
            ->add('category_id', EntityType::class, [
                'class' => Categories::class,
                'choice_label'=> 'nameCategory',
                'required'=>true
            ])
            ->add('product_distributors', EntityType::class,[
                'class' => Distributors::class,
                'choice_label'=>'nameDistributor',
                'label' => 'Select distributor(s)',
                'multiple'=>true
            ])
            ->add('image_product', FileType::class,[
                'label' => "product's picture",
                'required'=> false,
                'data_class'=> null,
                'empty_data' => 'no picture associated'
    ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Products::class,
        ]);
    }
}
