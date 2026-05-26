<?php

namespace App\Controller;

use App\Entity\Trip;
use App\Form\FilterTripType;
use App\Form\TripType;
use App\Repository\TripRepository;
use App\Services\TripService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route(path: '/trip', name: 'trip_')]
final class TripController extends AbstractController
{
    public function __construct(
        private TripService $tripService,
        private EntityManagerInterface $entityManager,
    )
    {}

    #[Route('', name: 'list')]
    public function list(Request $request): Response
    {
        $trips = $this->tripService->findByFilter();
        $filterForm = $this->createForm(FilterTripType::class);
        $filterForm->handleRequest($request);

        if ($filterForm->isSubmitted() && $filterForm->isValid()) {
            $data = $filterForm->getData();
            if ($this->getUser()){
                $id = $this->getUser()->getId();
            }else{
                $id = null;
            }
            $trips = $this->tripService->findByFilter($data,$id);
        }

        return $this->render('trip/list.html.twig', [
            'trips' => $trips,
            'filterForm' => $filterForm->createView(),
        ]);
    }
    #[Route('/create', name: 'create', methods: ['POST', 'GET'])]
    public function create(Request $request): Response
    {
        $trip = new Trip();
        $form = $this->createForm(TripType::class, $trip);
        $form->handleRequest($request);
        /**
         * @var $AddressHisCreated boolean
         */
        $AddressHisCreated = $form->get('choiceMethodAddress')->getData();
        if ($form->isSubmitted() && $form->isValid()) {
            $address = $form->get('address')->getData();
            if ($AddressHisCreated) {
                $newAddress = $form->get('newAddress')->getData();
                $this->entityManager->persist($newAddress);
                $address = $newAddress;
            }
            $trip->setAddress($address);
            $trip->setOrganisator($this->getUser());
            $this->entityManager->persist($trip);
            $this->entityManager->flush();
            $this->addFlash('success', 'Trip created!');
            return $this->redirectToRoute('trip_detail', ['id' => $trip->getId()]);
        }
        return $this->render('trip/create.html.twig', [
            'form' => $form,
        ]);
    }

    #[Route('/{id}/update', name: 'update')]
    public function update(): Response
    {
        return $this->render('trip/list.html.twig', [
            'controller_name' => 'TripController',
        ]);
    }

    #[Route('/{id}', name: 'detail')]
    public function detail(int $id): Response
    {
        $trip = $this->tripService->findByIdJoin($id);

        return $this->render('trip/detail.html.twig', [
            'trip' => $trip[0],
            'controller_name' => 'TripController',
        ]);
    }

    #[Route('/{id}/cancel', name: 'cancel')]
    public function cancel(int $id): Response
    {
        $trip = $this->tripService->find($id);
        if ($this->getUser() == $trip->getOrganisator() || $this->getUser()->getRoles()){
            $this->tripService->cancel($id);
        }

        return $this->redirectToRoute('trip_detail', ['id' => $id]);
    }

    #[IsGranted("ROLE_ADMIN")]
    #[Route('/{id}/archive', name: 'archive')]
    public function archive(): Response
    {
        return $this->render('trip/list.html.twig', [
            'controller_name' => 'TripController',
        ]);
    }
}
