<?php

namespace App\Form;

use App\Entity\Address;
use App\Entity\Site;
use App\Entity\Trip;
use App\Entity\User;
use App\Repository\UserRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\RadioType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class TripType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name')
            ->add('startDate')
            ->add('endDate')
            ->add('limitRegistrationDate')
            ->add('description')
            ->add('maxRegistration')
            ->add('site', EntityType::class, [
                'class' => Site::class,
                'choice_label' => 'name',
            ])
            ->add('choiceMethodAddress', ChoiceType::class, [
                'mapped' => false,
                'choices' => [
                    'Choisir' => false,
                    'Créer' => true,
                ],
                'data' => false,
                'expanded' => true,
            ])
            ->add('address', EntityType::class, [
                'class' => Address::class,
                'choice_label' => 'name',
                'placeholder' => 'Choisisssez une adresse',
                'required' => false,
            ])
            ->add('newAddress', AddressType::class, [
                'mapped' => false,
                'label' => 'Créer une adresse',
                'required' => false,
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Trip::class,
        ]);
    }
}
