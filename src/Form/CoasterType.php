<?php

namespace App\Form;

use App\Entity\Coaster;
use PhpParser\Node\Stmt\Label;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CoasterType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', options:['label' => 'Nom du Coaster',])
            ->add('maxSpeed', options:['label' => 'Vitesse Max du Coaster',])
            ->add('length', options:['label' => 'Longueur du Coaster',])
            ->add('maxHeight', options:['label' => 'Hauteur Max du Coaster',])
            ->add('operating', options:['label' => 'Est opérationnel',])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Coaster::class,
        ]);
    }
}
