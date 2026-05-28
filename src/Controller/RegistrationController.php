<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\RegistrationFormType;
use App\Form\UserImportFormType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;

class RegistrationController extends AbstractController
{
    #[Route('/register', name: 'app_register')]
    public function register(Request $request, UserPasswordHasherInterface $userPasswordHasher,
                             EntityManagerInterface $entityManager,
                             RegistrationFormType $registrationFormType): Response
    {
        $user = new User();
        $form = $this->createForm(RegistrationFormType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var string $plainPassword */
            $plainPassword = $form->get('plainPassword')->getData();

            // encode the plain password
            $user->setPassword($userPasswordHasher->hashPassword($user, $plainPassword));

            // add role by default
            $user->setRoles(['ROLE_USER']);

            // activate true by default
            $user->setActivate(true);

            $entityManager->persist($user);
            $entityManager->flush();

            // do anything else you need here, like send an email
            $this->addFlash('success', 'Utilisateur créé avec succès');
            return $this->redirectToRoute('app_home');
        } elseif ($form->isSubmitted() && !$form->isValid()) {
            $this->addFlash('error', 'Une erreur est survenue');
        }

        return $this->render('registration/register.html.twig', [
            'registrationForm' => $form,
        ]);
    }

    #[Route('/register/import', name: 'app_register_import')]
    public function importUsersExcel(
        Request $request,
        UserImportService $userImportService
    ): Response {
        $form = $this->createForm(UserImportFormType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $file = $form->get('excel_file')->getData();
            $result = $userImportService->UserImportFormType($file);

            if ($result['success']) {
                $this->addFlash('success', sprintf('%d utilisateurs importés avec succès !', $result['count']));
            } else {
                $this->addFlash('error', $result['message']);
            }

            return $this->redirectToRoute('app_register_import');
        }

        return $this->render('registration/import.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}


