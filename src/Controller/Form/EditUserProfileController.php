<?php

namespace App\Controller\Form;

use App\Form\EditProfileFormType;
use App\Form\RegistrationFormType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Validator\Constraints\File as FileConstraint;

final class EditUserProfileController extends AbstractController
{
    #[Route('/edit/profile', name: 'app_edit_user_profile')]
    public function editProfile(Request $request, EntityManagerInterface $entityManager, SluggerInterface $slugger): Response
    {
        if(!$this->getUser()) {
            return $this->redirectToRoute('app_login');
        }

        $currentUser = $this->getUser();
        $form = $this->createForm(EditProfileFormType::class, $currentUser);
        $form->handleRequest($request);
        
        if($form->isSubmitted() && $form->isValid()){
            $profileImageFile = $form->get('profileImage')->getData();
            
            if ($profileImageFile) {
                $originalFilename = pathinfo($profileImageFile->getClientOriginalName(), PATHINFO_FILENAME);
                $safeFilename = $slugger->slug($originalFilename);
                $newFilename = $safeFilename.'-'.uniqid().'.'.$profileImageFile->guessExtension();

                try {
                    $profileImageFile->move(
                        $this->getParameter('profile_images_directory'),
                        $newFilename
                    );
                    $currentUser->setProfileImage($newFilename);
                } catch (FileException $e) {
                    $this->addFlash('error', 'Une erreur est survenue lors de l\'upload de l\'image');
                }
            }

            $entityManager->persist($currentUser);
            $entityManager->flush();
            return $this->redirectToRoute('app_user_profile');
        }

        //send form to the new page
        return $this->render('user_profile/edit_information.html.twig',[
            'controller_name' => 'EditUserProfileController',
            'form' => $form->createView(),
        ]);
    }}
