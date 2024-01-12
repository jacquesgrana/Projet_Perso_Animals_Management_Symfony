<?php

namespace App\Repository;

use App\Entity\EventPriority;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<EventPriority>
 *
 * @method EventPriority|null find($id, $lockMode = null, $lockVersion = null)
 * @method EventPriority|null findOneBy(array $criteria, array $orderBy = null)
 * @method EventPriority[]    findAll()
 * @method EventPriority[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class EventPriorityRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, EventPriority::class);
    }

//    /**
//     * @return EventPriority[] Returns an array of EventPriority objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('e')
//            ->andWhere('e.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('e.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?EventPriority
//    {
//        return $this->createQueryBuilder('e')
//            ->andWhere('e.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
