<?php

namespace App\Form;

use App\Entity\Site;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class FilterTripType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('site', EntityType::class, [
                'class' => Site::class,
                'choice_label' => 'name',
                'label'=> 'Site :','required'=>false
            ])
            ->add('name',TextType::class,['label'=> 'Nom :','required'=>false])
            ->add('dateStart', DateTimeType::class, ['label'=> 'Date début :','required'=>false])
            ->add('dateEnd', DateTimeType::class,['label'=> 'Date fin :','required'=>false])
            ->add("organizer",CheckboxType::class,['label'=> "Sorties dont je suis l'organisateur/trice",'required'=>false])
            ->add("register",CheckboxType::class,['label'=> "Sorties auxquelles je suis inscrit/e",'required'=>false])
            ->add("notRegister",CheckboxType::class,['label'=> "Sorties auxquelles je ne suis pas inscrit/e",'required'=>false])
            ->add("ended",CheckboxType::class,['label'=> "Sorties passées",'required'=>false])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            // Configure your form options here
        ]);
    }
}
