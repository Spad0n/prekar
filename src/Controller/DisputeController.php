<?php

namespace App\Controller;

use App\Entity\Dispute;
use App\Entity\Renting;
use App\Entity\Report;
use App\Form\DisputeType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class DisputeController extends AbstractController
{
    #[Route('/user/dispute/{renting_id}', name: 'app_dispute')]
    public function createDispute(Request $request,EntityManagerInterface $entityManager,int $renting_id): Response
    {
        if (!$this->getUser() ) {
            return $this->redirectToRoute('app_login');
        }

        $renting  = $entityManager->getRepository(Renting::class)->find($renting_id);

        if(!$renting){
            return $this->redirectToRoute('app_prekar_home_page');
        }

        //if the dispute already exists :
        $existingDispute = $entityManager->getRepository(Dispute::class)->findOneBy(['renting'=>$renting]);
        if($existingDispute){
            return $this->redirectToRoute('app_dispute_page',['report_id'=>$existingDispute->getReports()[0]->getId()]);
        }



        $owner = $renting->getOffer()->getUserOwner();
        $borrower = $renting->getUserBorrower();

        if($this->getUser() != $owner && $this->getUser() != $borrower){
            return $this->redirectToRoute('app_prekar_home_page');
        }

        $dispute = new Dispute();
        $report = new Report();


        $form = $this->createForm(DisputeType::class,$dispute);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $report->setDispute($dispute);
            $report->setUserBorrower($borrower);
            $report->setUserOwner($owner);
            $dispute->addReport($report);
            $dispute->setRenting($renting);
            $dispute->setStatus("Waiting for a jurist...");
            $entityManager->persist($report);
            $entityManager->persist($dispute);
            $entityManager->flush();

            return $this->redirectToRoute('app_dispute_page',['report_id'=>$report->getId()]);
        }


        return $this->render('dispute/create_dispute.html.twig', [
            'controller_name' => 'DisputeController',
            'form'=>$form,
            'owner'=>$owner,
            'borrower'=>$borrower,
            'renting'=>$renting
        ]);
    }

    #[Route('/user/dispute/page/{report_id}', name: 'app_dispute_page')]
    public function disputePage(EntityManagerInterface $entityManager,int $report_id): Response
    {
        if (!$this->getUser() ) {
            return $this->redirectToRoute('app_login');
        }

        $report  = $entityManager->getRepository(Report::class)->find($report_id);

        if(!$report){
            return $this->redirectToRoute('app_prekar_home_page');
        }

        return $this->render('dispute/dispute_page.html.twig', [
            'controller_name' => 'DisputeController',
            'report'=>$report,
            'renting'=>$report->getDispute()->getRenting()
        ]);
    }
}
