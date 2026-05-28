<?php

namespace App\Controller;

use App\Entity\Site;
use App\Entity\User;
use App\Form\RegistrationFormType;
use App\Form\UserImportFormType;
use App\Services\UserService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;

class RegistrationController extends AbstractController
{
    #[Route('/register/solo', name: 'app_register_solo')]
    public function registerSolo(Request $request, UserPasswordHasherInterface $userPasswordHasher,
                             EntityManagerInterface $entityManager,
                             RegistrationFormType $registrationFormType): Response
    {
        $user = new User();
        $form = $this->createForm(RegistrationFormType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var string $plainPassword */
            $plainPassword = $form->get('plainPassword')->getData();

            $newSite = $form->get('newSite')->getData();
            $site = $form->get('site')->getData();

            if (!$newSite && !$site) {
                $this->addFlash('error', 'Veuillez choisir un site ou en créer un nouveau.');
                return $this->redirectToRoute('app_register_solo');
            }

            if ($newSite) {
                $site = new Site();
                $site->setName($newSite);
                $entityManager->persist($site);
                $user->setSite($site);
            } else {
                $user->setSite($site);
            }

            $user->setPassword($userPasswordHasher->hashPassword($user, $plainPassword));


            $user->setRoles(['ROLE_USER']);


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
        UserService $userService
    ): Response {
        $form = $this->createForm(UserImportFormType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $file = $form->get('file')->getData();
            $result = $userService->importUsersFromExcel($file->getPathname());

            if (empty($result['errors'])) {
                $this->addFlash('success', sprintf('%d utilisateurs importés avec succès !', $result['success']));
            } else {
                foreach ($result['errors'] as $error) {
                    $this->addFlash('error', sprintf('Ligne %d : %s', $error['line'], $error['message']));
                }
            }

            return $this->redirectToRoute('app_register_import');
        }

        return $this->render('registration/import.html.twig', [
            'registrationForm' => $form,
        ]);
    }


    #[Route('/register', name: 'app_register')]
    public function register(Request $request, UserPasswordHasherInterface $userPasswordHasher,
                             EntityManagerInterface $entityManager,
                             RegistrationFormType $registrationFormType): Response
    {

        return $this->render('registration/registerHome.html.twig', [
        ]);
    }

}


