<?php

namespace App\Form;

use App\Entity\Categories;
use App\Entity\Distributors;
use App\Entity\Products;
use App\Entity\User;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Form\CallbackTransformer;
use App\Form\DataTransformers\UserToIdentifierTransformer;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Notifier\Texter;

class ProductsType extends AbstractType
{
    public function __construct(
        private Security $security,
        private UserToIdentifierTransformer $transformer,
    ){
        $this->security = $security;
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        // $this->security->getUser();
        
        $builder
            ->add('name_product', TextType::class)
            ->add('description_product')
            ->add('price_product')
            ->add('availability_product')
            ->add('reference_id', ReferencesType::class, [
                'label'=>"reference number", 
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
            ->add('seller', HiddenType::class,[
                'data'=>$this->security->getUser()->getUserIdentifier(),
                'invalid_message'=>'user is not valid"',
            ]);
            
            $builder->get('seller')
                ->addModelTransformer($this->transformer);

        // $builder->get('seller')
        //     ->addModelTransformer(new CallbackTransformer(
        //         function($user): string {
        //             return $user->getUserIdentifier();
        //         },
        //         function($user): User {
        //             return $user;
        //         }
        //     ));    
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Products::class
        ]);
    }
}
