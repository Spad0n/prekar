<?php
namespace App\tests;

use App\Controller\RegistrationController;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Form\FormInterface;

class RegistrationTest extends TestCase
{
    public function testRegistration(): void
    {
        // Mock de l'EntityManager
        $entityManager = $this->createMock(EntityManagerInterface::class);
        $entityManager->expects($this->once())->method('persist');
        $entityManager->expects($this->once())->method('flush');

        // Mock du PasswordHasher
        $passwordHasher = $this->createMock(UserPasswordHasherInterface::class);
        $passwordHasher
            ->expects($this->once())
            ->method('hashPassword')
            ->willReturn('hashed-password');

        // Mock du formulaire
        $form = $this->createMock(FormInterface::class);
        $form->method('isSubmitted')->willReturn(true);
        $form->method('isValid')->willReturn(true);
        $form->method('getData')->willReturn([
            'email' => 'test@example.com',
            'password' => 'password',
            'lastName' => 'Doe',
            'name' => 'John',
            'userType' => 'ROLE_BORROWER',
        ]);

        $form->method('get')
            ->willReturnCallback(function ($field) {
                $mock = $this->createMock(FormInterface::class);
                $mock->method('getData')->willReturn(
                    $field === 'userType' ? 'ROLE_BORROWER' : null
                );
                return $mock;
            });

        // Mock de la requête
        $request = $this->createMock(Request::class);

        // Instanciez le contrôleur
        $controller = new RegistrationController();

        // Appelez la méthode du contrôleur
        $response = $controller->index($request, $passwordHasher, $entityManager);

        // Vérifiez la réponse
        $this->assertInstanceOf(Response::class, $response);
    }
}

?>
