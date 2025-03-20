<?php

namespace App\Controller\Profile;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
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
}
