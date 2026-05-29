<?php

namespace App\Services;

use App\Entity\Trip;
use App\Enum\StateEnum;
use App\Repository\TripRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Exception;
use Symfony\Component\HttpFoundation\Request;
use function Webmozart\Assert\Tests\StaticAnalysis\throws;

class TripService
{
    public function __construct(private EntityManagerInterface $entityManager,private TripRepository $tripRepository)
    {
    }

    public function findAll(): array
    {
        return  $this->tripRepository->findAll();
    }

    public function findAllByUser(int $idUser){
        return $this->tripRepository->findAllByUser($idUser);
    }

    public function find(int $id){
        return $this->tripRepository->find($id);
    }

    public function persistFlush(Trip $trip): void
    {
        $this->entityManager->persist($trip);
        $this->entityManager->flush();
    }

    public function findAllCreated(int $id , int $page = 1): Paginator{
        return $this->tripRepository->findAllTripCreated($id , $page);
    }


    public function cancel(int $id,$comment = null): void
    {
        $trip = $this->tripRepository->find($id);

        if (!$trip) {
            throw new Exception('Trip not found');
        }
        if ($trip->getState() === StateEnum::CREATED or  $trip->getState() == null) {
            $trip->setState(StateEnum::CANCELLED);
            $trip->setCancelComment($comment);
            $this->entityManager->persist($trip);
            $this->entityManager->flush();
        }
    }

    /**
     * @throws Exception
     */
    public function archive(int $id): void
    {
        $trip = $this->tripRepository->find($id);

        if (!$trip) {
            throw new Exception('Trip not found');
        }
        if ($trip->getState() !== StateEnum::ARCHIVED or  $trip->getState() == null) {
            $trip->setState(StateEnum::ARCHIVED);
            $this->entityManager->persist($trip);
            $this->entityManager->flush();
        }
        else{
            throw new Exception('Trip already archived');
        }
    }

    public function publish(int $id): void
    {
        $trip = $this->tripRepository->find($id);

        if (!$trip) {
            throw new Exception('Trip not found');
        }
        if ( $trip->getState() == StateEnum::CREATED ) {
            $trip->setState(null);
            $this->entityManager->persist($trip);
            $this->entityManager->flush();
        }
        else{
            throw new Exception('state is not Created');
        }

    }



    public function findByFilter(array $filters = [] , ?int $id = null,$page = 1): Paginator
    {
        return $this->tripRepository->findTripsWithFilters($filters,$id,$page);
    }

    public function findByIdJoin($id): ?array
    {
        return $this->tripRepository->findTripWithJoin($id);
    }

}
