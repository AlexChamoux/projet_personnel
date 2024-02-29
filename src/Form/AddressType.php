<?php

namespace App\Form;

use App\Entity\Address;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;

class AddressType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
        ->add('number', TextType::class, [
            'label' => 'Numéro'
        ])
        ->add('street', TextType::class, [
            'label' => 'Rue'
        ])
        ->add('zipcode', TextType::class, [
            'label' => 'Code Postal'
        ])
        ->add('city', TextType::class, [
            'label' => 'Ville'
        ])
        ->add('country', TextType::class, [
            'label' => 'Pays'
        ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Address::class,
        ]);
    }
}
