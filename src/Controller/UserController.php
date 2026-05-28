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
use Symfony\Component\Security\Http\Attribute\IsGranted;

final class UserController extends AbstractController
{
    #[Route('/user/{id}', name: 'app_user_show_id')]
    public function show(int $id, UserRepository $userRepository, UserService $userService): Response
    {

        $user = $userService->getUser($id, $userRepository);

        if (!$user) {
            throw $this->createNotFoundException("C'est embarassant.. Mais votre ami n'existe pas.");
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
        // Vérifie que l'utilisateur connecté est bien celui dont l'ID est dans l'URL
        $currentUser = $this->getUser();
        if ($currentUser->getId() !== $id) {
            throw $this->createAccessDeniedException('Vous ne pouvez modifier que votre propre profil.');
        }

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

            $photo = $form->get('photo')->getData();

            if ($photo) {
                if ($user->getPhoto()) {
                    $oldFile = $this->getParameter('photos_directory') . '/' . $user->getPhoto();
                    if (file_exists($oldFile)) {
                        unlink($oldFile);
                    }
                }

                $newFilename = uniqid() . '.' . $photo->guessExtension();
                $photo->move($this->getParameter('photos_directory'), $newFilename);
                $user->setPhoto($newFilename);
            }

            $entityManager->persist($user);
            $entityManager->flush();

            $this->addFlash('success', 'Profil modifié avec succès.');

            return $this->redirectToRoute('app_home');
        } elseif ($form->isSubmitted() && !$form->isValid()) {
            $this->addFlash('error', 'Des informations sont manquantes ou erronées.');
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

    #[Route('/user/tripWithdraw/{id}', name: 'trip_withdraw', requirements: ['id' => '\d+'])]
    public function withdrawTrip(UserService $userService, int $id): Response
    {
        $userService->withdraw($id, $this->getUser());

        $this->addFlash('success', 'Vous vous êtes désisté de la sortie avec succès.');

        return $this->redirectToRoute('trip_list', ['page' => 1]);
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

    #[Route('/user/innactif/{id}', name: 'user_innactif')]
    #[IsGranted("ROLE_ADMIN")]
    public function userInnactif(UserService $userService, int $id , UserRepository $userRepository): \Symfony\Component\HttpFoundation\RedirectResponse
    {
        $userService->setInnactif($id,$userRepository );
        return $this->redirectToRoute('trip_list', ['page' => 1]);
    }

    #[Route('/user/delete/{id}', name: 'user_delete')]
    #[IsGranted("ROLE_ADMIN")]
    public function userDelete(UserService $userService, int $id , UserRepository $userRepository): \Symfony\Component\HttpFoundation\RedirectResponse
    {
        $userService->deleteUser($id,$userRepository );
        return $this->redirectToRoute('trip_list', ['page' => 1]);
    }


}
