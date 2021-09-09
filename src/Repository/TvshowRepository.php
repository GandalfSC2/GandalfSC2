<?php

namespace App\Repository;

use App\Entity\Tvshow;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Tvshow|null find($id, $lockMode = null, $lockVersion = null)
 * @method Tvshow|null findOneBy(array $criteria, array $orderBy = null)
 * @method Tvshow[]    findAll()
 * @method Tvshow[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TvshowRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Tvshow::class);
    }

    // /**
    //  * @return Tvshow[] Returns an array of Tvshow objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('t')
            ->andWhere('t.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('t.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Tvshow
    {
        return $this->createQueryBuilder('t')
            ->andWhere('t.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
