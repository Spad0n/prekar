<?php

namespace App\Controller\Profile;

use App\Entity\Admin;
use App\Entity\Offer;
use App\Entity\Payment;
use App\Entity\Renting;
use App\Entity\Service;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class UserProfileController extends AbstractController
{
    #[Route('/user/profile', name: 'app_user_profile')]
    public function index(): Response
    {
        if(!$this->getUser()) {
            return $this->redirectToRoute('app_login');
        }


        return $this->render('user_profile/profile.html.twig', [
            'controller_name' => 'UserProfileController',
            'profile_image' => $this->getUser()->getProfileImage(),
            'subscriptions' => $this->getUser()->getSubscriptions()
        ]);
    }

    #[Route('/user/profile/claim-payment', name: 'app_user_profile_claim_payment_maybe')]
    public function claimPaymentMaybe(EntityManagerInterface $entityManager): Response
    {
        if(!$this->getUser()) {
            return $this->redirectToRoute('app_login');
        }

        $ownerOffers = $entityManager->getRepository(Offer::class)->findBy(['userOwner' => $this->getUser()]);
        $rentingsDone = [];
        $nbRentings = 0;
        for($i = 0; $i < count($ownerOffers); $i++) {
            $renting = $entityManager->getRepository(Renting::class)->findBy(['offer' => $ownerOffers[$i], 'done' => true, 'proceeded' => false]);
            if($renting != null) {
                $rentingsDone[$nbRentings] = $renting[0];
                $nbRentings++;
            }
        }

        $amount = 0;

        foreach($rentingsDone as $renting) {
            $amount += $renting->getTotalAmount();
        }

        return $this->render('user_profile/payment_claim.html.twig', [
            'controller_name' => 'UserProfileController',
            'rentings_done' => $rentingsDone,
            'total_amount' => $amount
        ]);
    }

    #[Route('/user/profile/claim-payment/validate', name: 'app_user_profile_claim_payment', methods: ["POST"])]
    public function claimPayment(EntityManagerInterface $em): Response
    {
        if(!$this->getUser()) {
            return $this->redirectToRoute('app_login');
        }

        $ownerOffers = $em->getRepository(Offer::class)->findBy(['userOwner' => $this->getUser()]);
        $rentingsDone = [];
        $nbRentings = 0;
        for($i = 0; $i < count($ownerOffers); $i++) {
            $renting = $em  ->getRepository(Renting::class)->findBy(['offer' => $ownerOffers[$i], 'done' => true, 'proceeded' => false]);
            if($renting != null) {
                $rentingsDone[$nbRentings] = $renting[0];
                $nbRentings++;
            }
        }

        $amount = 0;
        foreach($rentingsDone as $renting) {
            $amount += $renting->getTotalAmount();
            $renting->setProceeded(true);
        }

        // Create a new Payment entity
        $payment = new Payment();
        // Set the properties of the Payment entity
        $payment->setTotal($amount);
        $payment->setStatus('Pending');
        $admin = $em->getRepository(Admin::class)->findAll();
        $admin = $admin[rand(0, count($admin) - 1)];

            $service = new Service();
            $service->setServiceFee(0.00);
            $service->setAdmin($admin);
            $em->persist($service);
            $payment->setApply($service);



        $payment->setAdmin($admin);
        $user = $this->getUser();
        $payment->setUserOwner($user);
        // Persist the Payment entity to the database
        dump($service);
        dump($payment);

        $em->persist($payment);

        $em->flush();

        $this->addFlash("success", "Payment Claimed Successfully");

        return $this->redirectToRoute('app_user_profile_claim_payment_maybe');
    }
}
