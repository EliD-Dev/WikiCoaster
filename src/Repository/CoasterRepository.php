<?php

namespace App\Repository;

use App\Entity\Coaster;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\SecurityBundle\Security;

/**
 * @extends ServiceEntityRepository<Coaster>
 */
class CoasterRepository extends ServiceEntityRepository
{
    private readonly Security $security;

    public function __construct(ManagerRegistry $registry, Security $security)
    {
        parent::__construct($registry, Coaster::class);
        $this->security = $security;
    }

//    /**
//     * @return Coaster[] Returns an array of Coaster objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('c')
//            ->andWhere('c.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('c.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Coaster
//    {
//        return $this->createQueryBuilder('c')
//            ->andWhere('c.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
    public function findFiltered(string $parkId = '', string $categoryId = '', string $search = '', string $published = '' ,int $count = 20, int $page = 1): Paginator
    {
        $begin = ($page - 1) * $count;
        
        $qb = $this->createQueryBuilder('c')
                    ->addSelect('p', 'cat')
                    ->leftJoin('c.Park', 'p')
                    ->leftJoin('c.Categories', 'cat') // Jointure avec les catégories
                    ->setMaxResults($count) // LIMIT
                    ->setFirstResult($begin); // OFFSET

        // Filtrer par parc si un ID de parc est fourni
        if ($parkId !== '') {
            $qb->andWhere('p.id = :parkId')
                ->setParameter('parkId', (int)$parkId);
        }

        // Filtrer par catégorie si un ID de catégorie est fourni
        if ($categoryId !== '') {
            $qb->andWhere('cat.id = :categoryId')
                ->setParameter('categoryId', (int)$categoryId);
        }

        if ($published !== null && $published !== '') {
            $qb->andWhere('c.published = :published')
                         ->setParameter('published', (bool) $published);
        }

        if ($search !== '') {
            $qb->andWhere('c.name LIKE :search')
                ->setParameter('search', '%' . $search . '%');
        }

        if (!$this->security->isGranted('ROLE_ADMIN')) {
            $qb->andWhere('c.published = true OR c.author = :author')
                ->setParameter('author', $this->security->getUser());
        }

        return new Paginator($qb->getQuery());
    }
}
