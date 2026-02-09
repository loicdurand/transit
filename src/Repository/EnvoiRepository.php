<?php

namespace App\Repository;

use App\Entity\Envoi;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Envoi>
 */
class EnvoiRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Envoi::class);
    }

    /**
     * @return Envoi[] Returns an array of Envoi objects
     */
    public function findAllUnfinalized(): array
    {
        return $this->createQueryBuilder('e')
            ->andWhere('e.statut != :val')
            ->setParameter('val', 'finalisé')
            ->orderBy('e.id', 'ASC')
            //    ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }

    //    public function findOneBySomeField($value): ?Envoi
    //    {
    //        return $this->createQueryBuilder('e')
    //            ->andWhere('e.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
