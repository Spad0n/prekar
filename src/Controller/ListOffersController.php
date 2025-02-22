<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Attribute\Route;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Offer;
use App\Entity\Car;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;


final class ListOffersController extends AbstractController
{
    #[Route('/offer/list', name: 'offer_list')]
    public function list(Request $request, EntityManagerInterface $entityManager, Security $security): Response
    {
        $offerRepo = $entityManager->getRepository(Offer::class);
        $carRepo = $entityManager->getRepository(Car::class);
    
        // Check if the user is an admin
        $isAdmin = $security->isGranted('ROLE_ADMIN');
    
        // Fetch available filter options
        $locations = $offerRepo->getDistinctLocations();
        $brands = $carRepo->getDistinctCarBrands();
        $fuelTypes = $carRepo->getDistinctCarFuelTypes();
        $seats = $carRepo->getDistinctCarSeats();
        $bootCapacities = $carRepo->getDistinctCarBootCapacities();
        $maxPrice = $offerRepo->getMaxPrice();
    
        $filters = [
            'locations' => $request->query->all('locations') ?: [],
            'brand' => $request->query->all('brand') ?: [],
            'fuelType' => $request->query->all('fuelType') ?: [],
            'nbSeat' => $request->query->all('nbSeat') ?: [],
            'bootCapacity' => $request->query->all('bootCapacity') ?: [],
            'delivery' => $request->query->all('delivery') ?: [],
            'minPrice' => $request->query->getInt('min_price', 0),
            'maxPrice' => $request->query->getInt('max_price', $maxPrice),
        ];
    
        // Fetch offers with applied filters
        $offers = $offerRepo->findFilteredOffers($filters, $isAdmin);
    
        return $this->render('offer/list.html.twig', [
            'offers' => $offers,
            'maxPrice' => $maxPrice,
            'locations' => $locations,
            'brands' => $brands,
            'fuelTypes' => $fuelTypes,
            'seats' => $seats,
            'bootCapacities' => $bootCapacities,
            'filters' => $filters,
            'isAdmin' => $isAdmin,
        ]);
    }    
}
