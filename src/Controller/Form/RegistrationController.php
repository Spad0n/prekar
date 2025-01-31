<?php
namespace App\Controller\Form;

use App\Entity\Borrower;
use App\Entity\Owner;
use App\Entity\User;
use App\Form\RegistrationFormType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\Form\Extension\Core\Type\FileType;

final class RegistrationController extends AbstractController
{


    #[Route('/registration', name: 'app_registration')]
public function index(
    Request $request,
    UserPasswordHasherInterface $passwordHasher,
    EntityManagerInterface $entityManager,
    SluggerInterface $slugger
): Response
{
    $user = new User();
    $form = $this->createForm(RegistrationFormType::class, $user);
    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {
        $userType = $form->get('userType')->getData();

        if(empty($userType)){
            $this->addFlash('error', 'Please choose an user type');
            return $this->redirectToRoute('app_registration');
        }

            $userType = $form->get('userType')->getData();
            if (is_array($userType)) {
                foreach ($userType as $role) {
                    $user->addRole($role);
                }
            }


            $user->setEmail($formData['email']);
            $user->setName($formData['name']);
            $user->setLastName($formData['lastName']);

            $hashedPassword = $passwordHasher->hashPassword(
                $user,
                $formData['password']
            );
            $user->setPassword($hashedPassword);

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
                $user->setProfileImage($newFilename);
            } catch (FileException $e) {
                $this->addFlash('error', 'Une erreur est survenue lors de l\'upload de l\'image');
            }
        }

        $entityManager->persist($user);
        $entityManager->flush();

        return $this->redirectToRoute('app_login');
    }

    return $this->render('registration/registration.html.twig', [
        'form' => $form->createView(),
    ]);
}
}
