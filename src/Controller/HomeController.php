<?php

namespace App\Controller;

use App\Repository\TripRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class HomeController extends AbstractController
{
    #[Route('/', name: 'app')]
    #[Route('/home', name: 'app_home')]
    public function index(TripRepository $tripRepository): Response
    {
        $tripExists = $tripRepository->count();
        if ($tripExists !== 0) {
            $tripsSelection = $tripRepository->findNNextTrip(min($tripExists, 4));
        } else {
            $tripsSelection = null;
        }
        return $this->render('home/index.html.twig', [
            'trips' => $tripsSelection,
        ]);
    }
}
