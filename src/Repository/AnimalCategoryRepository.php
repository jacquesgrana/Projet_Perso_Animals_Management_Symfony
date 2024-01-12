<?php

namespace App\Repository;

use App\Entity\AnimalType;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<AnimalType>
 *
 * @method AnimalType|null find($id, $lockMode = null, $lockVersion = null)
 * @method AnimalType|null findOneBy(array $criteria, array $orderBy = null)
 * @method AnimalType[]    findAll()
 * @method AnimalType[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class AnimalTypeRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, AnimalType::class);
    }

//    /**
//     * @return AnimalType[] Returns an array of AnimalType objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('a')
//            ->andWhere('a.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('a.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?AnimalType
//    {
//        return $this->createQueryBuilder('a')
//            ->andWhere('a.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
