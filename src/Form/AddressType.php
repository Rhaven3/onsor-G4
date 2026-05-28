<?php

namespace App\Form;

use App\Entity\Address;
use BcMath\Number;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AddressType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', null, [
                'label' => 'Nom',
                "required" => true,
            ])
            ->add('street', null, [
                'label' => 'Rue',
                "required" => true,
            ])
            ->add('state', null, [
                'label' => 'Département',
                "required" => true,
            ])
            ->add('city', null, [
                'label' => 'Ville',
                "required" => true,
            ])
            ->add('postcode', null, [
                'label' => 'Code postal',
                "required" => true,
            ])
            ->add('latitude', NumberType::class , [
                'required' => true,
            ])
            ->add('longitude', NumberType::class,[
                'required' => true,
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Address::class,
        ]);
    }
}
