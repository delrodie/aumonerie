<?php

namespace App\Repository;

use App\Entity\ProgrammeActivite;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method ProgrammeActivite|null find($id, $lockMode = null, $lockVersion = null)
 * @method ProgrammeActivite|null findOneBy(array $criteria, array $orderBy = null)
 * @method ProgrammeActivite[]    findAll()
 * @method ProgrammeActivite[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ProgrammeActiviteRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ProgrammeActivite::class);
    }

    // /**
    //  * @return ProgrammeActivite[] Returns an array of ProgrammeActivite objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('p.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?ProgrammeActivite
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
