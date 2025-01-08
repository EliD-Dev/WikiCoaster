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
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Intl\Countries;

class CoasterType extends AbstractType
{
    private readonly AuthorizationCheckerInterface $authorizationChecker;

    public function __construct(AuthorizationCheckerInterface $authorizationChecker)
    {
        $this->authorizationChecker = $authorizationChecker;
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', options:['label' => 'Nom du Coaster',])
            ->add('maxSpeed', options:['label' => 'Vitesse Max du Coaster',])
            ->add('length', options:['label' => 'Longueur du Coaster',])
            ->add('maxHeight', options:['label' => 'Hauteur Max du Coaster',])
            ->add('park', EntityType::class, ['class' => Park::class,'required' => false,
                                                'group_by' => function(Park $entity): ?string {
                                                    return Countries::getName($entity->getCountry());
                                                },])
            ->add('operating', options:['label' => 'En fonctionnement',])
            ->add('categories', EntityType::class, ['class' => Category::class, 'multiple' => true,'expanded' => true,
                'query_builder' => function (EntityRepository $er): QueryBuilder {
                                        return $er->createQueryBuilder('c')
                                            ->orderBy('c.name', 'ASC');
                                    },
            ])
        ;

        if ($this->authorizationChecker->isGranted('ROLE_ADMIN')) {
            $builder->add('published', options:['label' => 'Publié','required' => false,]);
        }
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Coaster::class,
        ]);
    }
}
