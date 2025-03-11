<?php

namespace App\Controller;

use App\Entity\Dispute;
use App\Entity\Report;
use App\Form\DisputeType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class DisputeController extends AbstractController
{
    #[Route('/user/dispute', name: 'app_dispute')]
    public function createDispute(Request $request,EntityManagerInterface $entityManager): Response
    {
        if (!$this->getUser()) {
            return $this->redirectToRoute('app_login');
        }

        $dispute = new Dispute();
        $report = new Report();
        $form = $this->createForm(DisputeType::class,$dispute);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $report->setDispute($dispute);
            // need to get the owner and borrower from the offer
            $entityManager->persist($dispute);
            $entityManager->flush();

            return $this->redirectToRoute('app_dispute');
        }


        return $this->render('dispute/create_dispute.html.twig', [
            'controller_name' => 'DisputeController',
            'form'=>$form,
        ]);
    }
}
