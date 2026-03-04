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
    public function findAllNotArchived(): array
    {
        return $this->createQueryBuilder('e')
            // ->innerJoin('e.statut', 's')
            // ->andWhere('s.libelle != :val')
            // ->setParameter('val', 'Finalisé')
            ->andWhere('e.archive IS NULL')
            ->orWhere('e.archive != :val')
            ->setParameter('val', true)
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
