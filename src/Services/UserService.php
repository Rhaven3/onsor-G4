<?php

namespace App\Services;

use App\Entity\User;
use App\Enum\StateEnum;
use App\Repository\TripRepository;
use App\Repository\UserRepository;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;

class UserService
{
    public function __construct(private EntityManagerInterface $entityManager,private UserRepository $userRepository,private TripRepository $tripRepository)
    {
    }


    public function registration (int $idTrip, User $user):void {
        $today = new DateTime('now');
        $trip = $this->tripRepository->find($idTrip);

        if ($trip->getState() == null && $trip->getLimitRegistrationDate() > $today
            && $trip->getMaxRegistration() > $trip->getParticipants()->count()) {
            $trip->addParticipant($user);
            $this->entityManager->persist($trip);
            $this->entityManager->flush();
        }
    }

    public function withdraw(int $idTrip, User $user):void {
        $today = new DateTime('now');
        $trip = $this->tripRepository->find($idTrip);
        if ($trip->getState() == null && $trip->getLimitRegistrationDate() > $today
        && $trip->getParticipants()->contains($user)){
            $trip->removeParticipant($user);
            $this->entityManager->persist($trip);
            $this->entityManager->flush();
        }
    }

    public function getUser(int $id, UserRepository $userRepository): ?User {
        $user = $userRepository->find($id);
        return $user;

    }

    public function getUserAll(UserRepository $userRepository): array {
        $users = $userRepository->findAll();
        return $users;

    }

}
