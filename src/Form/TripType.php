<?php

namespace App\Form;

use App\Entity\Address;
use App\Entity\Site;
use App\Entity\Trip;
use App\Entity\User;
use App\Repository\UserRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ButtonType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\RadioType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class TripType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', null, [
                'label' => 'Nom',
            ])
            ->add('startDate', null, [
                'label' => 'Date de début',
            ])
            ->add('endDate', null, [
                'label' => 'Date de fin',
            ])
            ->add('limitRegistrationDate', null, [
                'label' => 'Date de fin d\'inscription',
            ])
            ->add('description')
            ->add('maxRegistration', null, [
                'label' => 'Nombre d\'inscription possible',
            ])
            ->add('site', EntityType::class, [
                'class' => Site::class,
                'choice_label' => 'name',
            ])
            ->add('choiceMethodAddress', ChoiceType::class, [
                'label' => ' ',
                'mapped' => false,
                'choices' => [
                    ' Choisir ' => false,
                    ' Créer' => true,
                ],
                'data' => false,
                'expanded' => true,
            ])
            ->add('address', EntityType::class, [
                'label' => 'Adresse',
                'class' => Address::class,
                'choice_label' => 'name',
                'placeholder' => 'Choisissez une adresse',
                'required' => false,
                'choice_attr' => function ($address) {
                    return [
                        'data-lat' => $address->getLatitude(),
                        'data-lng' => $address->getLongitude(),
                    ];
                }
            ])
            ->add('newAddress', AddressType::class, [
                'mapped' => false,
                'label' => 'Créer une adresse',
                'required' => false,
            ])
            ->add('published', HiddenType::class, [
                'data' => true,
                'mapped' => false,
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
