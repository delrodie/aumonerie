<?php

namespace App\Repository;

use App\Entity\Cardinal;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Cardinal|null find($id, $lockMode = null, $lockVersion = null)
 * @method Cardinal|null findOneBy(array $criteria, array $orderBy = null)
 * @method Cardinal[]    findAll()
 * @method Cardinal[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CardinalRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Cardinal::class);
    }

    // /**
    //  * @return Cardinal[] Returns an array of Cardinal objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('c.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Cardinal
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
