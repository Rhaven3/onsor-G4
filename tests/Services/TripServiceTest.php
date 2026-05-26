<?php

namespace App\Tests\Services;

use App\Entity\Trip;
use App\Enum\StateEnum;
use App\Services\TripService;
use PHPUnit\Framework\TestCase;
use App\Repository\TripRepository;
use Doctrine\ORM\EntityManagerInterface;

class TripServiceTest extends TestCase
{
    public function testCancel(): void
    {
        // Mock du Trip
        $trip = $this->createMock(Trip::class);

        // Vérifie que setState() est appelé
        $trip->expects($this->once())
            ->method('setState')
            ->with(StateEnum::CANCELLED);

        // Mock du repository
        $tripRepository = $this->createMock(TripRepository::class);

        // find(1) retourne le trip mocké
        $tripRepository->expects($this->once())
            ->method('find')
            ->with(1)
            ->willReturn($trip);

        // Mock entity manager
        $entityManager = $this->createMock(EntityManagerInterface::class);

        $entityManager->expects($this->once())
            ->method('persist')
            ->with($trip);

        $entityManager->expects($this->once())
            ->method('flush');

        // Service
        $service = new TripService(
            $entityManager,
            $tripRepository
        );

        // Test
        $service->cancel(1);
    }
}
