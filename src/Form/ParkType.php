<?php

namespace App\Form;

use App\Entity\Park;
use Symfony\Component\Intl\Countries;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\CountryType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

class ParkType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $anneeActuel = (int)date('Y');
        $annees = range($anneeActuel, 1900);


        $builder
            ->add('name', options:['label' => 'Nom du Park',])
            ->add('country', CountryType::class, [
                'label' => 'Pays',
                'placeholder' => 'Choisissez le pays du park',
                'preferred_choices' => ['FR', 'DE', 'BE', 'ES'],
                'choice_translation_locale' => 'fr',
            ])
            ->add('openingYear', ChoiceType::class, [
                'label' => 'Année d\'ouverture',
                'choices' => array_combine($annees, $annees),
                'data' => $options['data']->getOpeningYear() ?? $anneeActuel,
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Park::class,
        ]);
    }
}
