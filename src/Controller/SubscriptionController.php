<?php

namespace App\Controller;

use App\Entity\Subscription;
use App\Form\SubscriptionType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class SubscriptionController extends AbstractController
{
    #[Route('/subscription/new', name: 'subscription_new')]
    #[IsGranted('IS_AUTHENTICATED_FULLY')]
    public function new(Request $request): Response
    {
        $subscription = new Subscription();
        $subscription->setUser($this->getUser());

        $form = $this->createForm(SubscriptionType::class, $subscription);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $startDate = $subscription->getStartDate();
            $endDate = $subscription->getEndDate();
            $days = $startDate->diff($endDate)->days;
            $cost = ($days + 1) * 5;

            $subscription->setCost($cost);

            return $this->redirectToRoute('subscription_payment', [
                'startDate' => $subscription->getStartDate()->format('Y-m-d'),
                'endDate' => $subscription->getEndDate()->format('Y-m-d'),
                'cost' => $cost,
            ]);
        }

        return $this->render('subscription/new.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/subscription/payment', name: 'subscription_payment')]
    #[IsGranted('IS_AUTHENTICATED_FULLY')]
    public function payment(Request $request): Response
    {
        $startDate = $request->query->get('startDate');
        $endDate = $request->query->get('endDate');
        $cost = $request->query->get('cost');

        return $this->render('subscription/payment.html.twig', [
            'startDate' => $startDate,
            'endDate' => $endDate,
            'cost' => $cost,
        ]);
    }

    #[Route('/subscription/confirm-payment', name: 'subscription_confirm_payment', methods: ['POST'])]
    #[IsGranted('IS_AUTHENTICATED_FULLY')]
    public function confirmPayment(Request $request, EntityManagerInterface $entityManager): Response
    {
        $startDate = $request->request->get('startDate');
        $endDate = $request->request->get('endDate');
        $cost = $request->request->get('cost');

        // Handle payment logic here...
        $paymentSuccessful = true; // Replace with actual payment logic

        if ($paymentSuccessful) {
            $subscription = new Subscription();
            $subscription->setUser($this->getUser());
            $subscription->setStartDate(new \DateTime($startDate));
            $subscription->setEndDate(new \DateTime($endDate));
            $subscription->setCost($cost);

            $entityManager->persist($subscription);
            $entityManager->flush();

            $this->addFlash('success', 'Subscription created successfully.');

            return $this->redirectToRoute('app_user_profile');
        } else {
            $this->addFlash('error', 'Payment failed. Please try again.');

            return $this->redirectToRoute('subscription_payment', [
                'startDate' => $startDate,
                'endDate' => $endDate,
                'cost' => $cost,
            ]);
        }
    }

    #[Route('/subscription/cancel/{id}', name: 'subscription_cancel', methods: ['POST'])]
    #[IsGranted('IS_AUTHENTICATED_FULLY')]
    public function cancel(Request $request, EntityManagerInterface $entityManager, int $id): Response
    {
        $subscription = $entityManager->getRepository(Subscription::class)->find($id);

        if (!$subscription || $subscription->getUser() !== $this->getUser()) {
            throw $this->createNotFoundException('Subscription not found or you do not have permission to cancel it.');
        }

        $entityManager->remove($subscription);
        $entityManager->flush();

        $this->addFlash('success', 'Subscription cancelled successfully.');

        return $this->redirectToRoute('app_user_profile');
    }
}
