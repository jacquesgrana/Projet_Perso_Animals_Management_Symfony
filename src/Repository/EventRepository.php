<?php

namespace App\Repository;

use App\Entity\Event;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use App\Entity\User;
use App\Entity\Animal;

/**
 * @extends ServiceEntityRepository<Event>
 *
 * @method Event|null find($id, $lockMode = null, $lockVersion = null)
 * @method Event|null findOneBy(array $criteria, array $orderBy = null)
 * @method Event[]    findAll()
 * @method Event[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class EventRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Event::class);
    }

    public function findEventsForUser(User $user)
    {
   $qb = $this->createQueryBuilder('e');

   $qb->innerJoin('e.animals', 'a')
      ->innerJoin('a.master', 'u')
      ->where('u.id = :userId')
      ->setParameter('userId', $user->getId());

   return $qb->getQuery()->getResult();
}

public function findEventsByAnimal(Animal $animal): array
{
    return $this->createQueryBuilder('e')
        ->join('e.animals', 'a')
        ->where('a.id = :animalId')
        ->setParameter('animalId', $animal->getId())
        ->getQuery()
        ->getResult();
}

public function findEventsByDayAndUser($day, $user): array { 
    // créer $date à partir de $day qui est au format '2021-01-01'
    $date = new \DateTime($day);
    $end = clone $date;
    $end->modify('+1 day');
    $date->setTime(0, 0, 0); // Set time to midnight
    //dd($date, $end);
    return $this->createQueryBuilder('e')
        ->where('e.start >= :dateStart')
        ->andWhere('e.start < :dateEnd')
        ->andWhere('e.user = :user')
        ->setParameter('dateStart', $date)
        ->setParameter('dateEnd', $end)
        ->setParameter('user', $user)
        ->getQuery()
        ->getResult();
}



//    /**
//     * @return Event[] Returns an array of Event objects
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

//    public function findOneBySomeField($value): ?Event
//    {
//        return $this->createQueryBuilder('e')
//            ->andWhere('e.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
