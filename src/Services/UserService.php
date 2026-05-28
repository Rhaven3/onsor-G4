<?php

namespace App\Services;

use App\Entity\User;
use App\Enum\StateEnum;
use App\Repository\SiteRepository;
use App\Repository\TripRepository;
use App\Repository\UserRepository;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserService
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private UserRepository $userRepository,
        private TripRepository $tripRepository,
        private SiteRepository $siteRepository,
        private UserPasswordHasherInterface $passwordHasher,
        )
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

    public function importUsersFromExcel(string $filePath): array
    {
        $report = ['success' => 0, 'errors' => []];

        try {
            $spreadsheet = IOFactory::load($filePath);
            $rows = $spreadsheet->getActiveSheet()->toArray();
            array_shift($rows); // saute l'en-tête

            foreach ($rows as $i => $row) {
                try {
                    $this->processRow($row);
                    $report['success']++;
                } catch (\Exception $e) {
                    $report['errors'][] = ['line' => $i + 2, 'message' => $e->getMessage()];
                }
            }
            $this->entityManager->flush();
        } catch (\Exception $e) {
            $report['errors'][] = ['line' => 0, 'message' => 'Erreur fichier : ' . $e->getMessage()];
        }

        return $report;
    }

    private function processRow(array $row): void
    {
        [$email, $username, $firstName, $lastName, $mobile, $password, $siteName] = array_pad($row, 7, null);

        if (!$email || !$password || !$username || !$firstName || !$lastName || !$mobile) {
            throw new \Exception('Champ obligatoire manquant.');
        }

        if ($this->entityManager->getRepository(User::class)->findOneBy(['email' => $email])) {
            throw new \Exception("Email $email déjà utilisé.");
        }

        $site = $siteName
            ? $this->siteRepository->findOneBy(['name' => $siteName])
            : null;

        if (!$site) {
            throw new \Exception("Site '$siteName' introuvable.");
        }

        $user = new User();
        $user->setEmail($email);
        $user->setUsername($username);
        $user->setFirstName($firstName);
        $user->setLastName($lastName);
        $user->setMobile($mobile);
        $user->setSite($site);
        $user->setRoles(['ROLE_USER']);
        $user->setActivate(true);
        $user->setPassword($this->passwordHasher->hashPassword($user, $password));

        $this->entityManager->persist($user);
    }


}

