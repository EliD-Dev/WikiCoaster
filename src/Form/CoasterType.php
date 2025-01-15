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
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Validator\Constraints\Image;
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
                                                'placeholder' => 'Sélectionner un Park',
                                                'group_by' => function(Park $entity): ?string {
                                                    return Countries::getName($entity->getCountry(), 'fr');
                                                },])
            ->add('operating', options:['label' => 'En fonctionnement',])
            ->add('categories', EntityType::class, ['class' => Category::class, 'multiple' => true,'expanded' => true,
                'query_builder' => function (EntityRepository $er): QueryBuilder {
                                        return $er->createQueryBuilder('c')
                                            ->orderBy('c.name', 'ASC');
                                    },
            ])
            ->add('image', FileType::class, [
                'label' => 'Image du Coaster (jpg, png)',
                'mapped' => false, // Ne lie pas ce champ à la propriété de l'entité
                'required' => false, // Optionnel
                'constraints' => [
                    new Image([
                        'maxSize' => '2M',
                        'mimeTypes' => ['image/jpeg', 'image/png'],
                        'mimeTypesMessage' => 'Veuillez télécharger une image valide (jpg ou png)',
                    ])
                ],])
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
