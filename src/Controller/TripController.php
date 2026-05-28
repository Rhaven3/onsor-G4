<?php

namespace App\Controller;

use App\Entity\Trip;
use App\Enum\StateEnum;
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
    public function __construct(private TripService $tripService, private EntityManagerInterface $entityManager)
    {
    }

    #[Route('/list/{page?1}', name: 'list', requirements: ['page' => '\d+'], methods: ['GET', 'POST'])]
    public function list(Request $request, int $page): Response
    {
        return $this->extracted($request, $page, 'list');
    }

    #[Route('/listCreated/{page?1}', name: 'listCreated', requirements: ['page' => '\d+'], methods: ['GET', 'POST'])]
    public function listCreated(Request $request, int $page): Response
    {
        if (!$this->getUser()) {
            return $this->redirectToRoute('trip_list');
        }
        return $this->extracted($request, $page, 'listCreated');
    }

    private function extracted(Request $request, int $page, string $context): Response
    {
        $filterForm = $this->createForm(FilterTripType::class);
        $filterForm->handleRequest($request);

        $id = $this->getUser() ? $this->getUser()->getId() : null;
        $filters = [];

        if ($filterForm->isSubmitted() && $filterForm->isValid()) {
            $filters = $filterForm->getData();
            if ($request->isMethod('POST')) {
                $page = 1;
            }
        }

        if ($context === 'listCreated') {
            $trips = $this->tripService->findAllCreated($id, $page);
        } else {
            $trips = $this->tripService->findByFilter($filters, $id, $page);
        }
        $totalTrips = count($trips);
        $maxPage = ceil($totalTrips / 15);

        if ($page < 1) {
            return $this->redirectToRoute('trip_list');
        } elseif ($page > $maxPage) {
            return $this->redirectToRoute('trip_list', ['page' => $maxPage]);
        }

        return $this->render('trip/list.html.twig', [
            'trips' => $trips,
            'filterForm' => $filterForm->createView(),
            'currentPage' => $page,
            'currentRoute' => 'trip_' . $context,
            'formAction' => $this->generateUrl('trip_list', ['page' => 1]),
            'maxPage' => $maxPage,
        ]);
    }

    #[Route('/create', name: 'create', methods: ['POST', 'GET'])]
    #[Route('/{id}/update', name: 'update', requirements: ['id' => '\d+'])]
    #[IsGranted("ROLE_USER")]
    public function create(Request $request, int $id = null): Response
    {
        $trip = new Trip();
        if ($id) {
            $trip = $this->tripService->find($id);
            if (!$trip) {
                throw $this->createNotFoundException('Cette sortie n\'existe pas');
            }
            $this->denyAccessUnlessGranted('TRIP_EDIT', $trip, 'Vous ne pouvez pas modifier cette sortie');
        }

        $form = $this->createForm(TripType::class, $trip);
        $form->handleRequest($request);
        /**
         * @var $AddressHisCreated boolean
         */
        $AddressHisCreated = $form->get('choiceMethodAddress')->getData();

        if ($form->isSubmitted()) {
            if ($form->isValid()) {
                $isPublished = $request->request->get('published');


                $address = $form->get('address')->getData();
                if ($AddressHisCreated) {
                    $newAddress = $form->get('newAddress')->getData();
                    if ($newAddress->getName() !== null && $newAddress->getStreet() !== null && $newAddress->getState() !== null && $newAddress->getCity() !== null
                        && $newAddress->getPostcode() !== null && $newAddress->getLatitude() !== null && $newAddress->getLongitude() !== null) {
                        $this->entityManager->persist($newAddress);
                        $address = $newAddress;
                    } else {
                        $this->addFlash('error', 'Les données de la nouvelle adresse sont invalides.');
                        return $this->render('trip/create.html.twig', [
                            'form' => $form->createView(),
                            "published" => $trip->getState() === null,
                        ]);
                    }

                }
                $trip->setAddress($address);
                $trip->setState(StateEnum::CREATED);
                if ($isPublished === 'true') {
                    $trip->setState(null);
                }
                $trip->setOrganisator($this->getUser());
                $this->entityManager->persist($trip);
                $this->entityManager->flush();
                if ($trip->getId() !== 0) {
                    $this->addFlash('success', 'Sortie créée avec succès !');
                } else {
                    $this->addFlash('success', 'Sortie mise à jour avec succès !');
                }
                return $this->redirectToRoute('trip_detail', ['id' => $trip->getId()]);
            } else {
                $this->addFlash('error', 'Données du formulaire invalides ou incomplètes.');
            }
    }
        return $this->render('trip/create.html.twig', [
            'form' => $form,
            "published" => $trip->getState() === null,
        ]);
    }

    #[Route('/{id}', name: 'detail')]
    public function detail(int $id): Response
    {
        $trip = $this->tripService->findByIdJoin($id);

        if (empty($trip)) throw $this->createNotFoundException('Sortie non trouvée');

        return $this->render('trip/detail.html.twig', [
            'trip' => $trip[0],
            'controller_name' => 'TripController',
        ]);
    }

    #[Route('/{id}/cancel', name: 'cancel')]
    #[IsGranted("ROLE_USER")]
    public function cancel(int $id, Request $request): Response
    {

        $valeurSaisie = $request->query->get('saisie');

        $trip = $this->tripService->find($id);
        if ($this->getUser() == $trip->getOrganisator() || $this->getUser()->getRoles()) {
            $this->tripService->cancel($id, $valeurSaisie);
        }
        return $this->redirectToRoute('trip_detail', ['id' => $id]);

    }

    #[Route('/{id}/publish', name: 'publish')]
    #[IsGranted("TRIP_PUBLISH")]
    public function publish(int $id): Response
    {
        $trip = $this->tripService->find($id);
        if ($this->getUser() == $trip->getOrganisator()) {
            $this->tripService->publish($id);
        }
        return $this->redirectToRoute('trip_detail', ['id' => $id]);
    }


    #[IsGranted("ROLE_ADMIN")]
    #[Route('/{id}/archive', name: 'archive')]
    public function archive(int $id): Response
    {
        $this->tripService->archive($id);
        return $this->redirectToRoute('trip_list');
    }


}
