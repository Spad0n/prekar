<?php

namespace App\Controller;

use App\Entity\Admin;
use App\Entity\Payment;
use App\Entity\Service;
use App\Entity\User;
use App\Entity\ValidateUser;
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
        $payments = [];
        $statesFilters = [];


        if ($request->isMethod('POST')) {
            //Getting form data
            $action = $request->request->get('action');
            $payments_selected = $request->request->all('payments_selected'); // Getting all checked boxes
            $statesFilters = $request->request->all('filters');






            if($statesFilters !== null)
            {
                $payments = $entityManager->getRepository(Payment::class)->findBy(['admin' => $admin, 'status' => $statesFilters]);
            }
            else
            {
                $payments = $entityManager->getRepository(Payment::class)->findBy(['admin' => $admin]);
            }



            if($action === 'update_commission')
            {
                $newCommission = $request->request->get('service_fee');


                if ($newCommission !== null && $payments_selected !== null)
                {

                    foreach ($payments_selected as $payment) {
                        $payment = $entityManager->getRepository(Payment::class)->find($payment);
                        $payment->getApply()->setServiceFee((float) $newCommission);
                            $entityManager->flush();
                        }
                }
                    $entityManager->flush();
                    $this->addFlash('success', 'Commissions updated !');

            }
            else if($action === 'UAS_commission')
            {
                $newCommission = $request->request->get('service_fee');
                dump ($newCommission);
                dump ($payments_selected);

                if ($newCommission !== null && $payments_selected !== null) {

                    foreach ($payments_selected as $payment) {
                        $payment = $entityManager->getRepository(Payment::class)->find($payment);
                        $payment->getApply()->setServiceFee((float) $newCommission);
                        $entityManager->flush();
                        $this->SendPayment($entityManager, $payment_id);
                     }


                    }

                    $entityManager->flush();
                    $this->addFlash('success', 'Commissions updated + Payments sent!');



            }
            else if($action == 'send_payment')
            {
                foreach($payments_selected as $payment_id){
                    $this->SendPayment($entityManager, $payment_id);
                }
                $this->addFlash('success', 'Payments sent!');
            }else if(str_starts_with($action, 'process_payment_'))
            {
                $paymentId = str_replace('process_payment_', '', $action);
                $this->SendPayment($entityManager, $paymentId);
                $this->addFlash('success', 'Payment sent!');
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
            'state' => $statesFilters,
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


    #[Route('/admin/driver_dashboard', name: 'drivers_dashboard')]
    public function driver_dashboard(EntityManagerInterface $entityManager, Request $request): Response
    {
        $drivers = [];
        $state = [];

        if($request->isMethod('POST'))
        {
            $state = $request->request->all('filters');
            $drivers = $entityManager->getRepository(ValidateUser::class)->findBy(['state' => $state]);
        }else
        {
            $drivers = $entityManager->getRepository(ValidateUser::class)->findBy(['state' => 'Pending']);
        }

        if ($request->isMethod('POST')) {
            $action = $request->request->get('action');

            if(str_starts_with($action, 'accept_'))
            {
                $id = str_replace('accept_', '', $action);
                $this->acceptDriverLicence($entityManager, $id);
                $this->addFlash('success', 'Driver licence accepted!');
                return $this->redirectToRoute('drivers_dashboard');
            }
            else if(str_starts_with($action,'deny_'))
            {
                $id = str_replace('deny_', '', $action);
                $this->denyDriverLicence($entityManager, $id);
                $this->addFlash('success', 'Driver licence denied!');
                return $this->redirectToRoute('drivers_dashboard');
            }
        }

        return $this->render('admin/driverlicencevalidation_dashboard.html.twig', [
            'drivers' => $drivers,
            'state' => $state,
        ]);
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
        $user = $entityManager->getRepository(User::class)->find(1);
        $payment->setUserOwner($user);
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


        $users = [];

        if ($request->isMethod('POST')) {
            $email = $request->request->get('search_email');
            $name = $request->request->get('search_name');
            $lastname = $request->request->get('search_lastname');

            $queryBuilder = $entityManager->getRepository(User::class)->createQueryBuilder('u')
                        ->leftJoin('u.validateUser', 'v')
                        ->addSelect('v');

                if (!empty($email)) {
                    $queryBuilder->andWhere('u.email LIKE :email')
                                 ->setParameter('email', '%' . $email . '%');
                }
                if (!empty($name)) {
                    $queryBuilder->andWhere('u.name LIKE :name')
                                 ->setParameter('name', '%' . $name . '%');
                }
                if (!empty($lastname)) {
                    $queryBuilder->andWhere('u.lastname LIKE :lastname')
                                 ->setParameter('lastname', '%' . $lastname . '%');
                }

                $users = $queryBuilder->getQuery()->getResult();
        }



        return $this->render('admin/user_dashboard.html.twig', [
            'users' => $users,
        ]);
    }

    public function acceptDriverLicence($entityManager, $id): void
    {
        $user = $entityManager->getRepository(ValidateUser::class)->find($id);
        $user->setState('Accepted');
        $admin = $entityManager->getRepository(Admin::class)->find($this->getUser()->getId());
        $user->setAdmin($admin);
        $user->setValidationDate(new \DateTime());
        $entityManager->flush();
    }

    public function denyDriverLicence($entityManager, $id): void
    {
        $user = $entityManager->getRepository(ValidateUser::class)->find($id);
        $user->setState('Denied');
        $admin = $entityManager->getRepository(Admin::class)->find($this->getUser()->getId());
        $user->setAdmin($admin);
        //$user->setValidationDate(new \DateTime());
        $entityManager->flush();
    }

    #[Route('/admin/user/profil/{id}', name: 'admin_user_profilpage', methods: ['POST', 'GET'])]
    public function visitUser(int $id, EntityManagerInterface $entityManager): Response
    {
        $user = $entityManager->getRepository(User::class)->find($id);
        if(!$user)
        {
            throw $this->createNotFoundException('User not found');
        }

        return $this->render('admin/adminprofil.html.twig', [
            'user' => $user,
        ]);
    }







}
