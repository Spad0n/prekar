<?php

namespace App\Controller\Form;

use App\Entity\User;
use App\Form\EditProfileFormType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Form\FormError;

final class EditUserProfileController extends AbstractController
{
    #[Route('/edit/profile', name: 'app_edit_user_profile')]
    public function editProfile(Request $request, EntityManagerInterface $entityManager, SluggerInterface $slugger): Response
    {
        if (!$this->getUser()) {
            return $this->redirectToRoute('app_login');
        }
    
        $currentUser = $this->getUser();
        $form = $this->createForm(EditProfileFormType::class, $currentUser);
        $form->handleRequest($request);
    
        if ($form->isSubmitted()) {
            /** @var UploadedFile|null $profileImageFile */
            $profileImageFile = $form->get('profileImage')->getData();
    
            if ($profileImageFile) {
                $allowedMimeTypes = ['image/jpeg', 'image/png', 'image/gif'];
                if (!in_array($profileImageFile->getMimeType(), $allowedMimeTypes)) {
                    $form->addError(new FormError('Invalid file type. Please upload a JPG, PNG, or GIF.'));

                    return $this->render('user_profile/edit_information.html.twig', [
                        'form' => $form,
                    ]);
                }
    
                $originalFilename = pathinfo($profileImageFile->getClientOriginalName(), PATHINFO_FILENAME);
                $safeFilename = $slugger->slug($originalFilename);
                $newFilename = $safeFilename . '-' . uniqid() . '.' . $profileImageFile->guessExtension();
    
                try {
                    $profileImageFile->move(
                        $this->getParameter('profile_images_directory'),
                        $newFilename
                    );
                    $currentUser->setProfileImage($newFilename);
                } catch (FileException $e) {
                    $form->addError(new FormError('An error occurred while uploading the image.'));
                    
                    return $this->render('user_profile/edit_information.html.twig', [
                        'form' => $form,
                    ]);
                }
            }

            if ($form->isValid()) {
                $entityManager->persist($currentUser);
                $currentUser->setRoles(array_unique(array_merge($currentUser->getRoles(), $form->get('userType')->getData())));

                $entityManager->flush();
                return $this->redirectToRoute('app_user_profile');
            }
        }
    
        return $this->render('user_profile/edit_information.html.twig', [
            'form' => $form,
        ]);
    }    
}
