<?php

namespace App\Repository;

use App\Entity\OpeningTime;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<OpeningTime>
 */
class OpeningTimeRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, OpeningTime::class);
    }

    //    /**
    //     * @return OpeningTime[] Returns an array of OpeningTime objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('o')
    //            ->andWhere('o.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('o.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?OpeningTime
    //    {
    //        return $this->createQueryBuilder('o')
    //            ->andWhere('o.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
