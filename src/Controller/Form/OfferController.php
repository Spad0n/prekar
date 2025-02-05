<?php

namespace App\Controller\Form;

use App\Entity\Offer;
use App\Entity\Car;
use App\Form\OfferFormType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\Security;

final class OfferController extends AbstractController
{
    #[Route('/offer/new', name: 'offer_new')]
    #[IsGranted('IS_AUTHENTICATED_FULLY')]
    public function new(Request $request, EntityManagerInterface $entityManager) {
        if($this->getUser()== null) {
            return $this->redirectToRoute('app_login');
        }
        $offer = new Offer();
        $offer->setUserOwner($this->getUser()); // auto assign logged-in user
        $form = $this->createForm(OfferFormType::class, $offer);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $car = $form->get('car')->getData();
            $offer->setCar($car);
            $car->setUserOwner($this->getUser());
            $entityManager->persist($offer);
            $entityManager->flush();
            return $this->redirectToRoute('offer_list');
        }
        return $this->render('offer/new.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/offer/list', name: 'offer_list')]
    public function list(EntityManagerInterface $entityManager) { // show existing offers, TODO delete after tests
        $offers = $entityManager->getRepository(Offer::class)->findAll();
        return $this->render('offer/list.html.twig', ['offers' => $offers,]);
    }

    #[Route('/offer/{id}', name: "offer_view")]
    public function view(Offer $offer) {
        return $this->render('offer/view.html.twig', ['offer' => $offer,]);
    }
}
