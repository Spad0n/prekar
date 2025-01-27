<?php
namespace App\Controller;

use App\Entity\Borrower;
use App\Entity\Owner;
use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use App\Form\RegistrationFormType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

final class RegistrationController extends AbstractController
{


    #[Route('/registration', name: 'app_registration')]
    public function index(
        Request $request,
        UserPasswordHasherInterface $passwordHasher,
        EntityManagerInterface $entityManager
    ): Response 
    {
        $user = new User();

        $form = $this->createForm(RegistrationFormType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $formData = $form->getData();
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
            $user->setPrenom($formData['name']);
            $user->setNom($formData['lastName']);
            
            $hashedPassword = $passwordHasher->hashPassword(
                $user,
                $formData['password']
            );
            $user->setPassword($hashedPassword);


            $entityManager->persist($user);
            $entityManager->flush();

            return $this->redirectToRoute('app_login');
        }

        return $this->render('registration/registration.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
