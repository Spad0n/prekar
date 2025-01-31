<?php

namespace App\Controller;

use App\Entity\Admin;
use App\Entity\Payment;
use App\Entity\Service;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AdminController extends AbstractController
{
    #[Route('/admin/services_dashboard', name: 'services_dashboard')]
    public function dashboard(EntityManagerInterface $entityManager, Request $request): Response
    {
        $admin = $entityManager->getRepository(Admin::class)->find($this->getUser()->getId());

        if (!$admin) {
            throw $this->createNotFoundException('Admin not found');
        }

        // Récupère tous les paiements que cet admin contrôle
        $payments = $entityManager->getRepository(Payment::class)->findAll();

        // Récupère le service associé à l’admin
        $service = $entityManager->getRepository(Service::class)->findOneBy(['admin' => $admin]);
        dump($service);

        if ($request->isMethod('POST')) {
            $newCommission = $request->request->get('service_fee');
            $payments_selected[] = $request->request->get('payments_selected');
            if ($newCommission !== null && $payments_selected !== null) {

                $service->setServiceFee((float) $newCommission);
                $entityManager->flush();
                $this->addFlash('success', 'Commission mise à jour avec succès !');
                return $this->redirectToRoute('admin_dashboard');
            }
        }

        return $this->render('admin/services_dashboard.html.twig', [
            'payments' => $payments,
            'service_fee' => $service ? $service->getServiceFee() : 0, // Mettre une valeur par défaut
        ]);
    }

    #[Route('/admin/', name: 'admin_panel')]
    public function showPanels(): Response
    {
        return $this->render('admin/panels.html.twig');
    }


    #[Route('/admin/add-payment', name: 'admin_add_payment')]
    public function addPayment(EntityManagerInterface $entityManager, Request $request): Response
    {
        // Create a new Payment entity
        $payment = new Payment();
        $admin = $entityManager->getRepository(Admin::class)->find($this->getUser()->getId());
        dump($admin);

        // Set the properties of the Payment entity
        $payment->setTotal(344.00);
        $payment->setStatus('Pending');
        $payment->setPayDate(new \DateTime());

        // Retrieve the associated Service entity
        $service = $entityManager->getRepository(Service::class)->find(1);
        if ($service) {
            $payment->setApply($service);
        }else{
            $service = new Service();
            $service->setServiceFee(0.00);
            $service->setAdmin($admin);
            $entityManager->persist($service);
            $payment->setApply($service);
        }


        $payment->setAdmin($admin);
        // Persist the Payment entity to the database
        dump($service);
        dump($payment);

        $entityManager->persist($payment);

        $entityManager->flush();
        // Add a success flash message
        $this->addFlash('success', 'Payment added successfully!');

        // Redirect to the admin dashboard or another page*/
        return $this->redirectToRoute('services_dashboard');
    }
}
