<?php

namespace App\Form;

use App\Entity\Coaster;
use App\Entity\Park;
use App\Entity\Category;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;

class CoasterType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', options:['label' => 'Nom du Coaster',])
            ->add('maxSpeed', options:['label' => 'Vitesse Max du Coaster',])
            ->add('length', options:['label' => 'Longueur du Coaster',])
            ->add('maxHeight', options:['label' => 'Hauteur Max du Coaster',])
            ->add('park', EntityType::class, ['class' => Park::class,'required' => false,])
            ->add('operating', options:['label' => 'En fonctionnement',])
            ->add('categories', EntityType::class, ['class' => Category::class, 'multiple' => true,'expanded' => true,
                'query_builder' => function (EntityRepository $er): QueryBuilder {
                                        return $er->createQueryBuilder('c')
                                            ->orderBy('c.name', 'ASC');
                                    },
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Coaster::class,
        ]);
    }
}
