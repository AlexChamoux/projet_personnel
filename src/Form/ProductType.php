<?php

namespace App\Form;

use App\Entity\Product;
use App\Entity\Category;
use App\Form\ImagesType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\String\Slugger\SluggerInterface;


class ProductType extends AbstractType
{
    private $slugger;

    public function __construct(SluggerInterface $slugger)
    {
        $this->slugger = $slugger;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        

        $existingImages = $options['existingImages'];
        // $imageDirectory = $options['imageDirectory'];
        
        $builder
            ->add('nameProduct', TextType::class, [
                'label' => 'Nom du Produit',
                'attr' => ['class' => 'form-control'],                
            ])
            ->add('description', TextareaType::class, [
                'label' => 'Description',
                'attr' => ['class' => 'form-control', 'rows' => 8],
            ])
            ->add('moreInformation', TextareaType::class, [
                'label' => 'Plus d\'informations',
                'attr' => ['class' => 'form-control', 'rows' => 8],
            ])
            ->add('createdAt', DateTimeType::class, [
                'label' => 'Date de création',
            ])
            ->add('priceProduct', MoneyType::class, [
                'label' => 'Prix',
                'currency' => 'EUR',
            ])
            ->add('isBest', CheckboxType::class, [
                'label' => 'Les Meilleurs',
                'required' => false,
            ])
            ->add('isNew', CheckboxType::class, [
                'label' => 'Nouveautés',
                'required' => false,
            ])
            ->add('category', EntityType::class, [
                'class' => Category::class,
                'label' => 'Catégorie',
                'choice_label' => 'nameCategory',
                'placeholder' => 'Sélectionnez une catégorie',
            ])
            ->add('images', CollectionType::class, [
                'label' => false,
                'entry_type' => ImagesType::class,
                'allow_add' => true,
                'allow_delete' => true,
                'by_reference' => false,
                'mapped' => false,
                'prototype' => true,
                'data' => $existingImages,
            ]);

    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Product::class,
            'existingImages' => null,
            'imageDirectory' => '',
        ]);
    }

}