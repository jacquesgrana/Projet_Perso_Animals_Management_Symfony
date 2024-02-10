<?php

namespace App\Repository;

use App\Entity\ConfirmToken;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<ConfirmToken>
 *
 * @method ConfirmToken|null find($id, $lockMode = null, $lockVersion = null)
 * @method ConfirmToken|null findOneBy(array $criteria, array $orderBy = null)
 * @method ConfirmToken[]    findAll()
 * @method ConfirmToken[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ConfirmTokenRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ConfirmToken::class);
    }

//    /**
//     * @return ConfirmToken[] Returns an array of ConfirmToken objects
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

//    public function findOneBySomeField($value): ?ConfirmToken
//    {
//        return $this->createQueryBuilder('c')
//            ->andWhere('c.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
