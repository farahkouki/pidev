<?php

namespace App\Form;

use App\Entity\Voyage;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;


class VoyageType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('villeDepart')
            ->add('destination')
            ->add('heureDepart')
            ->add('heureArrivee')
            ->add('typeVoyage', ChoiceType::class, [
                'choices' => [
                    'Traditionnel' => 'Traditionnel',
                    'Volontaire' => 'Volontaire',
                ],
            ])
            ->add('type', ChoiceType::class, [
                'choices' => [
                    'En Solo' => 'En Solo',
                    'En Groupe' => 'En Groupe',
                ],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Voyage::class,
        ]);
    }
}
