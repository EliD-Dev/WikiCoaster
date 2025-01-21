<?php

namespace App\Repository;

use App\Entity\Park;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Symfony\Bundle\SecurityBundle\Security;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Park>
 */
class ParkRepository extends ServiceEntityRepository
{
    private readonly Security $security;

    public function __construct(ManagerRegistry $registry, Security $security)
    {
        parent::__construct($registry, Park::class);
        $this->security = $security;
    }

    public function findFiltered(string $search = '', string $pays = '', int $count = 20, int $page = 1): Paginator
    {
        $begin = ($page - 1) * $count;

        $qb = $this->createQueryBuilder('p')
            ->setMaxResults($count)
            ->setFirstResult($begin);

        if ($search !== '') {
            $qb->andWhere('p.name LIKE :search')
                ->setParameter('search', "%$search%");
        }

        if ($pays !== '') {
            $qb->andWhere('p.country = :pays')
                ->setParameter('pays', $pays);
        }

        return new Paginator($qb->getQuery());
    }

    public function findAllCountries(): array
    {
        return $this->createQueryBuilder('p')
            ->select('DISTINCT p.country')
            ->orderBy('p.country', 'ASC')
            ->getQuery()
            ->getResult();
    }


//    /**
//     * @return Park[] Returns an array of Park objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('p')
//            ->andWhere('p.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('p.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Park
//    {
//        return $this->createQueryBuilder('p')
//            ->andWhere('p.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
