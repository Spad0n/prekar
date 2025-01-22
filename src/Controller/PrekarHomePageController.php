<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class PrekarHomePageController extends AbstractController
{
    #[Route('', name: 'app_prekar_home_page')]
    public function index(): Response
    {
        return $this->render('prekar_home_page/index.html.twig', []);
    }
}
