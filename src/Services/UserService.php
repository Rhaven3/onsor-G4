<?php

namespace App\Services;

use App\Entity\User;
use App\Enum\StateEnum;
use App\Repository\TripRepository;
use App\Repository\UserRepository;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

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

class UserImporter
{
    private EntityManagerInterface $em;
    private UserPasswordHasherInterface $passwordHasher;

    public function __construct(EntityManagerInterface $em, UserPasswordHasherInterface $passwordHasher)
    {
        $this->em = $em;
        $this->passwordHasher = $passwordHasher;
    }

    public function importFromExcel(string $filePath): array
    {
        $report = [
            'success' => 0,
            'errors' => [],
        ];


        //Charge le fichier Excel et capte les erreurs

        try {
            $spreadsheet = IOFactory::load($filePath);
            $worksheet = $spreadsheet->getActiveSheet();
            $rows = $worksheet->toArray();
            array_shift($rows); // On saute l'en-tête

            // Traite chaque ligne de l'excel

            foreach ($rows as $i => $row) {
                try {
                    $this->processRow($row);
                    $report['success']++;
                } catch (\Exception $e) {
                    $report['errors'][] = [
                        'line' => $i + 2, // +2 car on a sauté l'en-tête et on commence à 0
                        'message' => $e->getMessage(),
                    ];
                }
            }

            // Fini l'import

            $this->em->flush();
        } catch (\Exception $e) {
            $report['errors'][] = [
                'line' => 0,
                'message' => 'Erreur de lecture du fichier : ' . $e->getMessage(),
            ];
        }

        return $report;
    }

    private function processRow(array $row): void
    {
        // Adapte les indices selon ton fichier Excel
        $email = $row[0] ?? null;
        $password = $row[1] ?? null;
        $firstName = $row[2] ?? null;
        $lastName = $row[3] ?? null;
        $roles = json_decode($row[4] ?? '[]', true) ?: [];

        if (!$email || !$password) {
            throw new \Exception('Email ou mot de passe manquant.');
        }

        // Vérifie si l'email existe déjà
        $existingUser = $this->em->getRepository(User::class)->findOneBy(['email' => $email]);
        if ($existingUser) {
            throw new \Exception('Un utilisateur avec cet email existe déjà.');
        }

        $user = new User();
        $user->setEmail($email);
        $user->setFirstName($firstName);
        $user->setLastName($lastName);
        $user->setRoles($roles);
        $user->setPassword($this->passwordHasher->hashPassword($user, $password));

        $this->em->persist($user);
    }
}



