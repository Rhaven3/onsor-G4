<?php

namespace App\Controller;

use App\Entity\Address;
use App\Form\AddressType;
use App\Services\AddressService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/address', name: 'address_')]
final class AddressController extends AbstractController
{
    public function __construct(private AddressService $addressService, private EntityManagerInterface $entityManager)
    {
    }

    #[Route('/', name: 'list')]
    #[IsGranted("ROLE_ADMIN")]
    public function index(): Response
    {
        $addressS =  $this->addressService->findAll();


        return $this->render('address/index.html.twig', [
            'addressS' => $addressS,
        ]);
    }

    #[Route('/{id}/update', name: 'update', requirements: ['id' => '\d+'])]
    #[IsGranted("ROLE_ADMIN")]
    public function update(int $id, Request $request): Response
    {
        $address = $this->addressService->findById($id);
        $title = 'Modifier une adresse';
        $bouton = 'Modifier';

        $form = $this->createForm(AddressType::class, $address);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->entityManager->persist($address);
            $this->entityManager->flush();
            return $this->redirectToRoute('address_list');
        }

        return $this->render('address/update.html.twig', [
            'form' => $form->createView(),
            'address' => $address,
            'title' => $title,
            'bouton' => $bouton,
        ]);
    }

    #[Route('/create', name: 'create')]
    #[IsGranted("ROLE_ADMIN")]
    public function create( Request $request): Response
    {
        $address = new Address();
        $title = 'Créer une adresse';
        $bouton = 'Créer';

        $form = $this->createForm(AddressType::class, $address);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->entityManager->persist($address);
            $this->entityManager->flush();
            return $this->redirectToRoute('address_list');
        }

        return $this->render('address/update.html.twig', [
            'form' => $form->createView(),
            'address' => $address,
            'title' => $title,
            'bouton' => $bouton,
        ]);
    }

    #[Route('/{id}/delete', name: 'delete', requirements: ['id' => '\d+'])]
    #[IsGranted("ROLE_ADMIN")]
    public function delete(int $id, Request $request): Response
    {
        $address = $this->addressService->findById($id);

        if (null === $address) {
            throw new NotFoundHttpException("L'adresse n'existe pas.");
        }

        $this->entityManager->remove($address);
        $this->entityManager->flush();

        return $this->redirectToRoute('address_list');
    }
}
