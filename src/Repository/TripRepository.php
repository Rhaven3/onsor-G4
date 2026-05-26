<?php

namespace App\Repository;

use App\Entity\Trip;
use App\Enum\StateEnum;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Trip>
 */
class TripRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Trip::class);
    }

    public function findAllByUser(int $id)
    {
        $dateArchive = new \DateTime('-30 days');
        $qb = $this->createQueryBuilder('t');
        $qb
            ->join('t.participants', 'p')
            ->where("p.id = :id")
            ->andWhere("t.state != :state AND t.endDate > :date")
            ->setParameter('id', $id)
            ->setParameter('state', StateEnum::ARCHIVED)
            ->setParameter('date', $dateArchive)
            ->orderBy('t.endDate', 'DESC');

        $query = $qb->getQuery();
        return $query->getResult();
    }

    public function findAllBySite(int $id)
    {
        $dateArchive = new \DateTime('-30 days');
        $qb = $this->createQueryBuilder('t');
        $qb
            ->join('t.site', 's')
            ->addSelect('s')
            ->where("s.id = :id")
            ->andWhere("t.state != :state AND t.endDate > :date")
            ->setParameter('id', $id)
            ->setParameter('state', StateEnum::ARCHIVED)
            ->setParameter('date', $dateArchive)
            ->orderBy('t.endDate', 'DESC');

        $query = $qb->getQuery();
        return $query->getResult();
    }



    public function findTripWithJoin(int $id)
    {
        $qb = $this->createQueryBuilder('t');
        $qb
            ->join('t.site', 's')
            ->addSelect('s')
            ->where("t.id = :id")
            ->setParameter('id', $id)
            ->leftJoin('t.participants', 'p')
            ->addSelect('p')
            ->leftJoin('t.organisator', 'o')
            ->addSelect('o')
            ->leftJoin('t.address','a')
            ->addSelect('a');

        $query = $qb->getQuery();
        return $query->getResult();
    }



    public function findTripsWithFilters(array $filters = [], $id = null): array
    {
        $dateArchive = new \DateTime('-30 days');
        $qb = $this->createQueryBuilder('t');

        $qb->Join('t.organisator', 'o')
            ->addSelect('o')
            ->andWhere('(t.state != :state and t.state != :s2) OR t.state IS NULL')
            ->andWhere('t.endDate > :archiveDate')
            ->setParameter('state', (string) StateEnum::ARCHIVED->value)
            ->setParameter('s2', (string) StateEnum::CREATED->value)
            ->setParameter('archiveDate', $dateArchive);

        if (!empty($filters['name'])) {
            $qb->andWhere('t.name LIKE :name')
                ->setParameter('name', '%' . $filters['name'] . '%');
        }

        if (!empty($filters['site'])) {
            $qb->join('t.site', 's')
                ->addSelect('s')
                ->andWhere('s.id = :siteId')
                ->setParameter('siteId', $filters['site']);
        }

        if (!empty($filters['dateStart'])) {
            $qb->andWhere('t.startDate >= :dateStart')
                ->setParameter('dateStart', $filters['dateStart']);
        }

        if (!empty($filters['dateEnd'])) {
            $qb->andWhere('t.endDate <= :dateEnd')
                ->setParameter('dateEnd', $filters['dateEnd']);
        }

        if (!empty($filters['organizer'])) {
            $qb->andWhere('o.id = :organizerId')
                ->setParameter('organizerId', $id);
        }


        if (!empty($filters['notRegister']) && !empty($filters['register'])) {

        }elseif (!empty($filters['notRegister']) || !empty($filters['register'])) {
            $qb->Join('t.participants', 'p');

            if (!empty($filters['register'])) {
                $qb->andWhere(':userId MEMBER OF t.participants')
                    ->setParameter('userId', $id);
            }

            if (!empty($filters['notRegister'])) {
                $qb->andWhere(':userId NOT MEMBER OF t.participants')
                    ->setParameter('userId', $id);
            }
        }

        if (!empty($filters['ended'])) {
            $qb->andWhere('t.endDate < :now')
                ->setParameter('now', new \DateTime());
        }

        $qb->orderBy('t.endDate', 'DESC');

        return $qb->getQuery()->getResult();
    }

    /**
     * Méthode en charge rechercher les N prochaines Sorties
     * @param int $nbTrips Nombre de sorties souhaités
     * @return mixed
     */
    function findNNextTrip(int $nbTrips) {
        $qb = $this
            ->createQueryBuilder('t')
            ->join('t.organisator', 'o')
            ->addSelect('o')
            ->where('t.startDate >= :now')
            ->setParameter('now', new \DateTime('now'))
            ->andWhere('t.state = :state')
            ->orWhere('t.state IS NULL')
            ->setParameter('state', StateEnum::CREATED)
            ->addOrderBy('t.startDate', 'ASC')
            ->setMaxResults($nbTrips);

        return $qb
            ->getQuery()
            ->getResult();
    }

//    /**
//     * @return Trip[] Returns an array of Trip objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('t')
//            ->andWhere('t.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('t.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Trip
//    {
//        return $this->createQueryBuilder('t')
//            ->andWhere('t.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
