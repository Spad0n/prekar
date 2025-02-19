<?php

namespace App\Controller\Form;

use App\Entity\Offer;
use App\Entity\Car;
use App\Form\OfferFormType;
use App\Form\CarType;
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
        $userCars = $entityManager->getRepository(Car::class)->findBy(['userOwner' => $this->getUser()]);
        $form = $this->createForm(OfferFormType::class, $offer, ['user_cars' => $userCars]);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $existingCar = $form->get('existingCar')->getData();
            $newCar = $form->get('newCar')->getData();
            if ($existingCar) {
                foreach($existingCar->getOffers() as $existingOffer) {
                    if($existingOffer->getStartDate() <= $offer->getEndDate() && $existingOffer->getEndDate() >= $offer->getStartDate()) {
                        $this->addFlash('error', 'You have another offer for this car during this period.');
                        return $this->redirectToRoute('offer_new');
                    }
                }

                $offer->setCar($existingCar);
            } elseif ($newCar) {
                $newCar->setUserOwner($this->getUser());
                $entityManager->persist($newCar);
                $offer->setCar($newCar);
            } elseif ($existingCar && $newCar) {
                $this->addFlash('error', 'Please select OR create a car.');
                return $this->redirectToRoute('offer_new');

            } else {
                $this->addFlash('error', 'Please select or create a car.');
                return $this->redirectToRoute('offer_new');
            }
            $entityManager->persist($offer);
            $entityManager->flush();
            return $this->redirectToRoute('offer_list');
        }
        return $this->render('offer/new.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/offer/list', name: 'offer_list')]
    public function list(EntityManagerInterface $entityManager) {
        $offers = $entityManager->getRepository(Offer::class)->findAll();
        return $this->render('offer/list.html.twig', ['offers' => $offers,]);
    }

    #[Route('/offer/{id}', name: "offer_view")]
    public function view(Offer $offer) {
        return $this->render('user_profile/profile.html.twig', ['offer' => $offer,]); //need to be changed when the offer page is ready
    }

    #[Route('/offer/{id}/edit', name: 'offer_edit')]
    #[IsGranted('IS_AUTHENTICATED_FULLY')]
    public function edit(Request $request, EntityManagerInterface $entityManager, Offer $offer): Response {
        if($offer->getUserOwner() == $this->getUser()) {
            $car = $offer->getCar();
            $form = $this->createForm(OfferFormType::class, $offer, [
                'car' => $car,
                'is_edit' => true,
            ]);
            $form->handleRequest($request);
            if ($form->isSubmitted() && $form->isValid()) {
                $newCar = $form->get('car')->getData();
                if ($newCar !== $car) {
                    $entityManager->persist($newCar);
                }
                $entityManager->flush();
                $this->addFlash('success', 'Offer and car details updated successfully.');
                return $this->redirectToRoute('offer_list');
            }
            return $this->render('offer/edit.html.twig', [
                'form' => $form->createView(),
                'offer' => $offer
            ]);
        } else {
            $this->addFlash('error', 'You can modify only offers you own.');
            return $this->redirectToRoute('offer_list');
        }
    }

    #[Route('/offer/{id}/delete', name: 'offer_delete', methods: ['POST'])]
    #[IsGranted('IS_AUTHENTICATED_FULLY')]
    public function delete(Request $request, EntityManagerInterface $entityManager, Offer $offer): Response {
        if ($this->isCsrfTokenValid('delete'.$offer->getId(), $request->request->get('_token'))) {
            $entityManager->remove($offer);
            $entityManager->flush();
            $this->addFlash('success', 'Offer deleted successfully.');
        }
        return $this->redirectToRoute('offer_list');
    }
}
