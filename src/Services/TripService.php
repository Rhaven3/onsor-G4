<?php

namespace App\Services;

use App\Entity\Trip;
use App\Enum\StateEnum;
use App\Repository\TripRepository;
use Doctrine\ORM\EntityManagerInterface;
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



    public function cancel(int $id): void
    {
        $trip = $this->tripRepository->find($id);

        if (!$trip) {
            throw new Exception('Trip not found');
        }

        $trip->setState(StateEnum::CANCELLED);
        $this->entityManager->persist($trip);
        $this->entityManager->flush();
    }

    public function archive(TripRepository $tripRepository, EntityManagerInterface $entityManager
        , int $id): void
    {
        $trip = $tripRepository->find($id);

        if (!$trip) {
            throw new Exception('Trip not found');
        }
        $trip->setState(StateEnum::ARCHIVED);
        $entityManager->persist($trip);
        $entityManager->flush();
    }

    public function findByFilter(array $filters = [] , ?int $id = null): array
    {
        return $this->tripRepository->findTripsWithFilters($filters,$id);
    }

    public function findByIdJoin($id): ?array
    {
        return $this->tripRepository->findTripWithJoin($id);
    }

}
