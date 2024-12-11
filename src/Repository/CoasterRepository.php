<?php

namespace App\Repository;

use App\Entity\Coaster;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Coaster>
 */
class CoasterRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Coaster::class);
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
    public function findFiltered(string $parkId = '', string $categoryId = '', string $search = ''): array
    {
        $qb = $this->createQueryBuilder('c')
            ->leftJoin('c.Park', 'p')
            ->leftJoin('c.Categories', 'cat') // Jointure avec les catégories
            ->addSelect('cat'); // Sélectionner les catégories

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

        if ($search !== '') {
            $qb->andWhere('c.name LIKE :search')
                ->setParameter('search', '%' . $search . '%');
        }

        return $qb->getQuery()->getResult();
    }
}
