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

    public function testArchivedTrue(): void
    {
        $trip = new Trip();
        $trip->setId(42);
        $trip->setState(StateEnum::CREATED);

        $tripRepository = $this->createMock(TripRepository::class);

        $tripRepository->expects($this->once())->method('find')->with(42)->willReturn($trip);
        $entityManager = $this->createStub(EntityManagerInterface::class);

        $tripService = new TripService($entityManager,$tripRepository);
        $tripService->archive(42);

        $this->assertSame(StateEnum::ARCHIVED, $trip->getState());
    }

    public function testArchiveFalseVoid(): void
    {
        $tripRepository = $this->createMock(TripRepository::class);

        $tripRepository->expects($this->once())->method('find')->with(42)->willReturn(null);
        $entityManager = $this->createStub(EntityManagerInterface::class);

        $tripService = new TripService($entityManager,$tripRepository);

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Trip not found');

        $tripService->archive(42);
    }
    public function testArchiveFalseTripArchived(): void
    {
        $trip = new Trip();
        $trip->setId(42);
        $trip->setState(StateEnum::ARCHIVED);

        $tripRepository = $this->createMock(TripRepository::class);

        $tripRepository->expects($this->once())->method('find')->with(42)->willReturn($trip);
        $entityManager = $this->createStub(EntityManagerInterface::class);

        $tripService = new TripService($entityManager,$tripRepository);
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Trip already archived');

        $tripService->archive(42);

    }


    public function testPublishedTrue(): void
    {
        $trip = new Trip();
        $trip->setId(42);
        $trip->setState(StateEnum::CREATED);

        $tripRepository = $this->createMock(TripRepository::class);

        $tripRepository->expects($this->once())->method('find')->with(42)->willReturn($trip);
        $entityManager = $this->createStub(EntityManagerInterface::class);

        $tripService = new TripService($entityManager,$tripRepository);
        $tripService->publish(42);

        $this->assertSame(null, $trip->getState());
    }

    public function testPublishedVoidTrip(): void
    {
        $tripRepository = $this->createMock(TripRepository::class);

        $tripRepository->expects($this->once())->method('find')->with(42)->willReturn(null);
        $entityManager = $this->createStub(EntityManagerInterface::class);

        $tripService = new TripService($entityManager,$tripRepository);

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Trip not found');

        $tripService->publish(42);
    }

    public function testPublishedNotCreated(): void
    {
        $trip = new Trip();
        $trip->setId(42);
        $trip->setState(null);

        $tripRepository = $this->createMock(TripRepository::class);

        $tripRepository->expects($this->once())->method('find')->with(42)->willReturn($trip);
        $entityManager = $this->createStub(EntityManagerInterface::class);

        $tripService = new TripService($entityManager,$tripRepository);
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('state is not Created');

        $tripService->publish(42);
    }


}
