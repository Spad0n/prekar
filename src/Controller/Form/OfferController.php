<?php

namespace App\Controller\Form;

use App\Entity\Offer;
use App\Entity\Car;
use App\Entity\Renting;
use App\Entity\Subscription;
use App\Form\OfferFormType;
use App\Form\CarType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityNotFoundException;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;

final class OfferController extends AbstractController
{
    private $uploadDir;

    #[Route('/offer/new', name: 'offer_new')]
    #[IsGranted('IS_AUTHENTICATED_FULLY')]
    public function new(Request $request, EntityManagerInterface $entityManager) {
        if($this->getUser()== null) {
            return $this->redirectToRoute('app_login');
        }
        $offer = new Offer();
        $offer->setUserOwner($this->getUser());
        $userCars = $entityManager->getRepository(Car::class)->findBy(['userOwner' => $this->getUser()]);
        $form = $this->createForm(OfferFormType::class, $offer, ['user_cars' => $userCars]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $existingCar = $form->get('existingCar')->getData();
            $newCar = $form->get('newCar')->getData();
            if ($existingCar && $existingCar->getBrand()) {
                foreach($existingCar->getOffers() as $existingOffer) {
                    if($existingOffer->getStartDate() <= $offer->getEndDate() && $existingOffer->getEndDate() >= $offer->getStartDate()) {
                        $this->addFlash('error', 'You have another offer for this car during this period.');
                        return $this->redirectToRoute('offer_new');
                    }
                }
                $offer->setCar($existingCar);
            } elseif ($newCar && $newCar->getBrand()) {
                $newCar->setUserOwner($this->getUser());
            
                // Handle image upload for new car
                $imageFile = $form->get('newCar')->get('image')->getData();
                if ($imageFile) {
                    $newFilename = uniqid().'.'.$imageFile->guessExtension();
                    try {
                        $imageFile->move(
                            $this->uploadDir,
                            $newFilename
                        );
                    $newCar->setImageFilename($newFilename);
                } catch (\Exception $e) {
                    $this->addFlash('error', 'Error uploading image');
                    return $this->redirectToRoute('offer_new');
                }
            }
            
            $entityManager->persist($newCar);
            $offer->setCar($newCar);
        } else {
            $this->addFlash('error', 'Please select or create a car.');
            return $this->redirectToRoute('offer_new');
        }
        
        $entityManager->persist($offer);
        $entityManager->flush();
        return $this->redirectToRoute('offer_list');
    }
    
    return $this->render('offer/new.html.twig', [
        'form' => $form,
    ]);
}

    #[Route('/user_profile/list', name: 'user_offer_list')]
    public function list(EntityManagerInterface $entityManager) {
        $user = $this->getUser();
        $offers = $entityManager->getRepository(Offer::class)->findBy(['userOwner' => $user]);
        return $this->render('user_profile/list.html.twig', ['offers' => $offers,]);
    }

    #[Route('/offer/{id<\d+>}', name: "offer_view")]
    public function view(Offer $offer) {
        $user = $this->getUser();
        $isActualUser = false;
        if ($offer->getUserOwner()->getId() === $user->getId()) {
            $isActualUser = true;
        }
        return $this->render('offer/view.html.twig', ['offer' => $offer, 'actualUser' => $isActualUser]); //need to be changed when the offer page is ready
    }

    #[Route('/offer/{id<\d+>}/edit', name: 'offer_edit')]
    #[IsGranted('IS_AUTHENTICATED_FULLY')]
    public function edit(Request $request, EntityManagerInterface $entityManager, int $id): Response 
    {
        $offer = $entityManager->getRepository(Offer::class)->find($id);
        
        if (!$offer) {
            throw new EntityNotFoundException('Offer not found');
        }

        if($offer->getUserOwner() == $this->getUser()) {
            $car = $offer->getCar();
            $form = $this->createForm(OfferFormType::class, $offer, [
                'car' => $car,
                'is_edit' => true,
            ]);
            
            $form->handleRequest($request);
            
            if ($form->isSubmitted() && $form->isValid()) {
                $imageFile = $form->get('car')->get('image')->getData();
                
                if ($imageFile) {
                    $newFilename = uniqid().'.'.$imageFile->guessExtension();
                    
                    try {
                        $imageFile->move(
                            $this->uploadDir,
                            $newFilename
                        );
                        
                        // Delete old image if exists
                        if ($car->getImageFilename()) {
                            $oldFilePath = $this->uploadDir.'/'.$car->getImageFilename();
                            if (file_exists($oldFilePath)) {
                                unlink($oldFilePath);
                            }
                        }
                        
                        $car->setImageFilename($newFilename);
                    } catch (\Exception $e) {
                        $this->addFlash('error', 'Error uploading image');
                        return $this->redirectToRoute('offer_edit', ['id' => $id]);
                    }
                }


                /* Check for available conflicts */
                $available = $form->get('available')->getData();

                $entityManager->flush();
                $this->addFlash('success', 'Offer and car details updated successfully.');
                return $this->redirectToRoute('offer_list');
            }

            return $this->render('offer/edit.html.twig', [
                'form' => $form,
                'offer' => $offer
            ]);
        }

        $this->addFlash('error', 'You can modify only offers you own.');
        return $this->redirectToRoute('offer_list');
    }

    #[Route('/offer/{id<\d+>}/delete', name: 'offer_delete', methods: ['POST'])]
    #[IsGranted('IS_AUTHENTICATED_FULLY')]
    public function delete(Request $request, EntityManagerInterface $entityManager, Offer $offer): Response {
        if ($this->isCsrfTokenValid('delete'.$offer->getId(), $request->request->get('_token'))) {
            $entityManager->remove($offer);
            $entityManager->flush();
            $this->addFlash('success', 'Offer deleted successfully.');
        }
        return $this->redirectToRoute('offer_list');
    }

    public function __construct(string $uploadDir)
    {
        $this->uploadDir = $uploadDir;
    }

    #[Route('/offer/{id}/rent', name: 'offer_rent_date')]
    #[IsGranted('IS_AUTHENTICATED_FULLY')]
    public function rentDate(Request $request, EntityManagerInterface $entityManager, int $id): Response {
        $offer = $entityManager->getRepository(Offer::class)->find($id);

        // Check for active subscription
        $user = $this->getUser();
        $subscription = $entityManager->getRepository(Subscription::class)->findOneBy(['user' => $user]);

        $currentDate = new \DateTime();
        if (!$subscription || $subscription->getEndDate() < $currentDate || $subscription->getStartDate() > $currentDate) {
            $this->addFlash('error', 'You need an active subscription to rent a car.');
            return $this->redirectToRoute('subscription_new', [
                'returnUrl' => $this->generateUrl('offer_rent_date', ['id' => $id]),
            ]);
        }

        return $this->render('offer/rent/dates.html.twig', [
            'offer' => $offer,
        ]);
    }

    #[Route('/offer/{id}/rent/delivery', name: 'offer_rent_delivery', methods: ['POST'])]
    #[IsGranted('IS_AUTHENTICATED_FULLY')]
    public function rentDelivery(Request $request, EntityManagerInterface $entityManager, int $id): Response {
        $offer = $entityManager->getRepository(Offer::class)->find($id);
        $startDate = new \DateTime($request->request->get('startDate'));
        $endDate = new \DateTime($request->request->get('endDate'));
        $delivery = $offer->getDelivery();

        if($startDate == null || $endDate == null) {
            $this->addFlash('error', 'Please select start and end date.');
            return $this->redirectToRoute('offer_rent_date', ['id' => $id]);
        }

        if($startDate > $endDate) {
            $this->addFlash('error', 'End date must be after the start date.');
            return $this->redirectToRoute('offer_rent_date', ['id' => $id]);
        }

        if ($startDate < $offer->getStartDate() || $endDate > $offer->getEndDate()) {
            $this->addFlash('error', 'Selected dates are not available for this offer.');
            return $this->redirectToRoute('offer_rent_date', ['id' => $id]);
        }


        return $this->render('offer/rent/delivery.html.twig',[
            'offer' => $offer,
            'startDate' => $startDate->format('Y-m-d'),
            'endDate' => $endDate->format('Y-m-d'),
            'delivery' => $delivery,
        ]);
    }

    #[Route('/offer/{id}/rent/payment', name: 'offer_rent_payment', methods: ['POST'])]
    #[IsGranted('IS_AUTHENTICATED_FULLY')]
    public function rentPayment(Request $request, EntityManagerInterface $entityManager, int $id): Response {
        $offer = $entityManager->getRepository(Offer::class)->find($id);
        $startDate = new \DateTime($request->request->get('startDate'));
        $endDate = new \DateTime($request->request->get('endDate'));
        $deliveryLocation = $request->request->get('deliveryLocation');

        if($offer->getDelivery() == "yes" && $deliveryLocation == null) {
            $this->addFlash('error', 'Please select a delivery location.');
            return $this->redirectToRoute('offer_rent_delivery', ['id' => $id]);
        }

        if($startDate == null || $endDate == null) {
            $this->addFlash('error', 'Please select start and end date.');
            return $this->redirectToRoute('offer_rent_date', ['id' => $id]);
        }

        if($startDate > $endDate) {
            $this->addFlash('error', 'End date must be after the start date.');
            return $this->redirectToRoute('offer_rent_date', ['id' => $id]);
        }

        if ($startDate < $offer->getStartDate() || $endDate > $offer->getEndDate()) {
            $this->addFlash('error', 'Selected dates are not available for this offer.');
            return $this->redirectToRoute('offer_rent_date', ['id' => $id]);
        }

        $nbDays = $startDate->diff($endDate)->days;


        return $this->render('offer/rent/payment.html.twig',[
            'offer' => $offer,
            'startDate' => $startDate->format('Y-m-d'),
            'endDate' => $endDate->format('Y-m-d'),
            'delivery' => $deliveryLocation,
            'nbDays' => $nbDays,
        ]);
    }

    #[Route('/offer/{id}/rent/confirm', name: 'offer_rent_confirm', methods: ['POST'])]
    #[IsGranted('IS_AUTHENTICATED_FULLY')]
    public function confirmRent(Request $request, EntityManagerInterface $entityManager, int $id): Response {
        $offer = $entityManager->getRepository(Offer::class)->find($id);
        $startDate = new \DateTime($request->request->get('startDate'));
        $endDate = new \DateTime($request->request->get('endDate'));
        $deliveryLocation = $request->request->get('delivery');

        $creditCard = $request->request->get('creditCard');
        $cvv = $request->request->get('cvv');
        $expirationDate = $request->request->get('expirationDate');
        $name = $request->request->get('name');

        // Check for active subscription
        $user = $this->getUser();
        $subscription = $entityManager->getRepository(Subscription::class)->findOneBy(['user' => $user]);

        $currentDate = new \DateTime();
        if (!$subscription || $subscription->getEndDate() < $currentDate || $subscription->getStartDate() > $currentDate) {
            $this->addFlash('error', 'You need an active subscription to rent a car.');
            return $this->redirectToRoute('subscription_new');
        }

        if($offer->getAvailable() == "not_available") {
            $this->addFlash('error', 'This offer is not available anymore.');
            return $this->redirectToRoute('offer_list');
        }

        if($offer->getDelivery() == "yes" && $deliveryLocation == null) {
            $this->addFlash('error', 'Please select a delivery location.');
            return $this->redirectToRoute('offer_rent_delivery', ['id' => $id]);
        }

        if($startDate == null || $endDate == null) {
            $this->addFlash('error', 'Please select start and end date.');
            return $this->redirectToRoute('offer_rent_date', ['id' => $id]);
        }

        if($startDate > $endDate) {
            $this->addFlash('error', 'End date must be after the start date.');
            return $this->redirectToRoute('offer_rent_date', ['id' => $id]);
        }

        if ($startDate < $offer->getStartDate() || $endDate > $offer->getEndDate()) {
            $this->addFlash('error', 'Selected dates are not available for this offer.');
            return $this->redirectToRoute('offer_rent_date', ['id' => $id]);
        }

        $renting = new Renting();
        $renting->setUserBorrower($this->getUser());
        $renting->setOffer($offer);
        $renting->setTotalAmount($offer->getPrice() * $startDate->diff($endDate)->days);
        $renting->setStartDate($startDate);
        $renting->setEndDate($endDate);
        $renting->setNbKm(122);
        $renting->setCommentary("No comment");
        $renting->setDeliveryLocation($deliveryLocation);
        $offer->setAvailable("not_available");


        $entityManager->persist($renting);
        $entityManager->flush();

        return $this->redirectToRoute("app_user_profile");
    }

}
