<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Attribute\Route;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Offer;
use Symfony\Component\HttpFoundation\Response;


final class ListOffersController extends AbstractController
{
    #[Route('/offer/list', name: 'offer_list')]
    public function list(EntityManagerInterface $entityManager): Response
    {
        $offers = $entityManager->getRepository(Offer::class)->findAll();

        return $this->render('offer/list.html.twig', [
            'offers' => $offers ?? [], // Ensure an empty array if no offers exist
        ]);
    }
}
