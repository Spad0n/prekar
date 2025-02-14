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

        // Getting all payments from this admin
        $payments = $entityManager->getRepository(Payment::class)->findAll();

        // Getting all services from this admin
        $services = $entityManager->getRepository(Service::class)->findBy(['admin' => $admin]);
        dump($services);

        if ($request->isMethod('POST')) {
            //Getting form data
            $action = $request->request->get('action');
            $payments_selected = $request->request->all('payments_selected'); // Récupérer toutes les cases cochées


            if($action === 'update_commission')
            {
                $newCommission = $request->request->get('service_fee');
                dump ($newCommission);
                dump ($payments_selected);

                if ($newCommission !== null && $payments_selected !== null) {

                    foreach ($services as $service) {
                        if(in_array($service->getPayment()->getId(),$payments_selected)){
                            $service->setServiceFee((float) $newCommission);
                            $entityManager->flush();
                        }
                    }
                    $entityManager->flush();
                    $this->addFlash('success', 'Commissions updated !');
                    return $this->redirectToRoute('services_dashboard');
                }
            }
            else if($action === 'UAS_commission')
            {
                $newCommission = $request->request->get('service_fee');
                dump ($newCommission);
                dump ($payments_selected);

                if ($newCommission !== null && $payments_selected !== null) {

                    foreach ($services as $service) {
                        if(in_array($service->getPayment()->getId(),$payments_selected)){
                            $service->setServiceFee((float) $newCommission);
                            $entityManager->flush();
                        }

                    }
                    foreach($payments_selected as $payment_id){
                        $this->SendPayment($entityManager, $payment_id);

                    }
                    $entityManager->flush();
                    $this->addFlash('success', 'Commissions updated + Payments sent!');


                    return $this->redirectToRoute('services_dashboard');
                }
            }
            else if($action == 'send_payment')
            {
                foreach($payments_selected as $payment_id){
                    $this->SendPayment($entityManager, $payment_id);
                }
                $this->addFlash('success', 'Payments sent!');
                return $this->redirectToRoute('services_dashboard');
            }else if(str_starts_with($action, 'process_payment_'))
            {
                $paymentId = str_replace('process_payment_', '', $action);
                $this->SendPayment($entityManager, $paymentId);
                $this->addFlash('success', 'Payment sent!');
                return $this->redirectToRoute('services_dashboard');
            }
        }

        // Sort payments by status
        usort($payments, function($a, $b) {
        if ($a->getStatus() == $b->getStatus()) {
            return 0;
        }
        return ($a->getStatus() == 'Pending') ? -1 : 1;
    });

        return $this->render('admin/services_dashboard.html.twig', [
            'payments' => $payments,
            //'services' => $services,
        ]);
    }

    function SendPayment($entityManager,$payment_id): void
    {
        $payment = $entityManager->getRepository(Payment::class)->find($payment_id);
        $payment->setStatus('Paid');
        $payment->setPayDate(new \DateTime());
        $entityManager->flush();
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
        $payment->setTotal(89.00);
        $payment->setStatus('Pending');

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

    #[Route('/admin/user_dashboard', name: 'users_dashboard')]
    public function userDashboard(EntityManagerInterface $entityManager, Request $request): Response
    {
        $admin = $entityManager->getRepository(Admin::class)->find($this->getUser()->getId());

        if (!$admin) {
            throw $this->createNotFoundException('Admin not found');
        }

        $users = [];

        if ($request->isMethod('POST')) {
            $criteria = [];
            $id = $request->request->get('search_id');
            $email = $request->request->get('search_email');
            $name = $request->request->get('search_name');
            $lastname = $request->request->get('search_lastname');

            if (!empty($id)) {
                $criteria['id'] = $id;
            }
            if (!empty($email)) {
                $criteria['email'] = $email;
            }
            if (!empty($name)) {
                $criteria['name'] = $name;
            }
            if (!empty($lastname)) {
                $criteria['lastName'] = $lastname;
            }

            if (!empty($criteria)) {
                $users = $entityManager->getRepository(User::class)->findBy($criteria);
            }
        }



        return $this->render('admin/user_dashboard.html.twig', [
            'users' => $users,
        ]);
    }


}
