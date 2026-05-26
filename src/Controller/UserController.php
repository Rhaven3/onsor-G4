<?php

namespace App\Controller;

use App\Form\FilterTripType;
use App\Form\RegistrationFormType;
use App\Form\UserUpdateFormType;
use App\Repository\UserRepository;
use App\Services\TripService;
use App\Services\UserService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

final class UserController extends AbstractController
{
    #[Route('/user/{id}', name: 'app_user_show_id')]
    public function show(int $id, UserRepository $userRepository, UserService $userService): Response
    {
        $user = $userService->getUser($id, $userRepository);

        if (!$user) {
            throw $this->createNotFoundException("It's embarrassing, but your friend doesn't exist.");
        }

        return $this->render('user/show.html.twig', [
            'user' => $user,
        ]);
    }

    #[Route('/user', name: 'app_user_show')]
    public function showAll(UserRepository $userRepository, UserService $userService): Response
    {
        $users = $userService->getUserAll($userRepository);

        if (!$users) {
            throw $this->createNotFoundException("It's embarrassing, but your friend doesn't exist.");
        }

        return $this->render('user/showAll.html.twig', [
            'users' => $users,
        ]);
    }

    #[Route('/user/{id}/update', name: 'app_user_update')]
    public function update(
        int $id,
        UserRepository $userRepository,
        EntityManagerInterface $entityManager,
        Request $request,
        UserService $userService,
        UserPasswordHasherInterface $passwordHasher
    ): Response {
        // Récupère l'utilisateur connecté
        $currentUser = $this->getUser();

        // Vérifie que l'ID dans l'URL correspond à l'utilisateur connecté
        if ($currentUser->getId() !== $id) {
            throw $this->createAccessDeniedException('Vous ne pouvez modifier que votre propre profil.');
        }

        // Récupère l'utilisateur à modifier (pour être sûr qu'il existe)
        $user = $userService->getUser($id, $userRepository);
        if (!$user) {
            throw $this->createNotFoundException("It's embarrassing, but your friend doesn't exist.");
        }

        $form = $this->createForm(UserUpdateFormType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Gestion du mot de passe
            $currentPassword = $form->get('currentPassword')->getData();
            $newPassword = $form->get('newPassword')->getData();

            if ($currentPassword && $newPassword) {
                if (!$passwordHasher->isPasswordValid($user, $currentPassword)) {
                    $this->addFlash('error', 'Le mot de passe actuel est incorrect.');
                    return $this->render('user/update.html.twig', [
                        'registrationForm' => $form->createView(),
                        'user' => $user,
                    ]);
                }
                $user->setPassword($passwordHasher->hashPassword($user, $newPassword));
            }

            // Gestion de la photo de profil
            $photo = $form->get('photo')->getData();
            if ($photo) {
                // Liste des extensions et types MIME autorisés
                $allowedExtensions = ['jpg', 'jpeg', 'png', 'webp', 'gif'];
                $allowedMimeTypes = ['image/jpeg', 'image/png', 'image/webp', 'image/gif'];

                // Vérification de l'extension
                $fileExtension = strtolower($photo->getClientOriginalExtension());
                if (!in_array($fileExtension, $allowedExtensions, true)) {
                    $this->addFlash('error', 'Seules les images JPG, PNG, WebP et GIF sont autorisées.');
                    return $this->render('user/update.html.twig', [
                        'registrationForm' => $form->createView(),
                        'user' => $user,
                    ]);
                }

                // Vérification du type MIME
                $fileMimeType = $photo->getClientMimeType();
                if (!in_array($fileMimeType, $allowedMimeTypes, true)) {
                    $this->addFlash('error', 'Le type de fichier n\'est pas une image valide.');
                    return $this->render('user/update.html.twig', [
                        'registrationForm' => $form->createView(),
                        'user' => $user,
                    ]);
                }

                // Si tout est OK, on supprime l'ancienne photo si elle existe
                if ($user->getPhoto()) {
                    $oldFile = $this->getParameter('photos_directory') . '/' . $user->getPhoto();
                    if (file_exists($oldFile)) {
                        unlink($oldFile);
                    }
                }

                // Génération d'un nom de fichier unique
                $newFilename = uniqid() . '.' . $fileExtension;
                $photo->move($this->getParameter('photos_directory'), $newFilename);
                $user->setPhoto($newFilename);
            }

            $entityManager->persist($user);
            $entityManager->flush();

            return $this->redirectToRoute('app_home');
        }

        return $this->render('user/update.html.twig', [
            'registrationForm' => $form->createView(),
            'user' => $user,
        ]);
    }

    #[Route('/user/tripRegister/{id}', name: 'trip_register')]
    public function registerTrip(UserService $userService, int $id): \Symfony\Component\HttpFoundation\RedirectResponse
    {
        $userService->registration($id, $this->getUser());
        return $this->redirectToRoute('trip_detail', ['id' => $id]);
    }

    #[Route('/user/tripWithdraw/{id}', name: 'trip_withdraw')]
    public function withdrawTrip(UserService $userService, int $id, TripService $tripService, Request $request): ?Response
    {
        $userService->withdraw($id, $this->getUser());
        $trips = $tripService->findByFilter();
        $filterForm = $this->createForm(FilterTripType::class);
        $filterForm->handleRequest($request);

        return $this->render('trip/list.html.twig', [
            'trips' => $trips,
            'filterForm' => $filterForm->createView(),
        ]);
    }

    #[Route('/user/tripWithdrawDetail/{id}', name: 'trip_withdrawDetail')]
    public function withdrawTripDetail(UserService $userService, int $id, TripService $tripService, Request $request): ?Response
    {
        $userService->withdraw($id, $this->getUser());
        $trips = $tripService->findByFilter();
        $filterForm = $this->createForm(FilterTripType::class);
        $filterForm->handleRequest($request);

        return $this->redirectToRoute('trip_detail', ['id' => $id]);
    }
}
