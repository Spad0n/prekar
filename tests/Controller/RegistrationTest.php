<?php

namespace App\tests\Controller;

use App\Controller\RegistrationController;
use App\Entity\Borrower;
use App\Entity\Owner;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use PHPUnit\Framework\TestCase;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class RegistrationTest extends WebTestCase
{

    public function testValidRegistrationBorrower(): void
    {

        // This calls KernelTestCase::bootKernel(), and creates a
        // "client" that is acting as the browser

        $client = static::createClient();

        // Request a specific page
        $crawler = $client->request('GET', '/registration');

        $submitButton = $crawler->selectButton('submit');
        $form = $submitButton->form();

        $email = 'test' . uniqid() . '@test.fr';
        $form['registration_form[email]'] = $email;
        $form['registration_form[password]'] = 'test123';
        $form['registration_form[lastName]'] = 'test';
        $form['registration_form[name]'] = 'test';
        $form['registration_form[userType]'] = 'ROLE_BORROWER';


        // submit the Form object
        $client->submit($form);

        $this->assertResponseRedirects('/login');

                $client->followRedirect();

                $entityManager = self::getContainer()->get('doctrine')->getManager();
                $userRepository = $entityManager->getRepository(User::class);

                $user = $userRepository->findOneBy(['email' => $email]);

                $this->assertNotNull($user, 'User not exists in the database.');
                $this->assertEquals($email, $user->getEmail(), "User\'s email does not match.");

                // Verifying user's data
                $this->assertEquals($email, $user->getEmail());
                $this->assertEquals('test', $user->getPrenom());
                $this->assertEquals('test', $user->getNom());
                $this->assertTrue(in_array('ROLE_BORROWER', $user->getRoles()));


    }

public function testValidRegistrationOwner(): void
    {

        // This calls KernelTestCase::bootKernel(), and creates a
        // "client" that is acting as the browser

        $client = static::createClient();

        // Request a specific page
        $crawler = $client->request('GET', '/registration');

        $submitButton = $crawler->selectButton('submit');
        $form = $submitButton->form();

        $email = 'test' . uniqid() . '@test.fr';
        $form['registration_form[email]'] = $email;
        $form['registration_form[password]'] = 'test123';
        $form['registration_form[lastName]'] = 'test';
        $form['registration_form[name]'] = 'test';
        $form['registration_form[userType]'] = 'ROLE_OWNER';


        // submit the Form object
        $client->submit($form);

        $this->assertResponseRedirects('/login');

                $client->followRedirect();

                $entityManager = self::getContainer()->get('doctrine')->getManager();
                $userRepository = $entityManager->getRepository(User::class);

                $user = $userRepository->findOneBy(['email' => $email]);

                $this->assertNotNull($user, 'User does not exists in the database.');
                $this->assertEquals($email, $user->getEmail(), "User\'s email does not match.");

                // Verifying user's data
                $this->assertEquals($email, $user->getEmail());
                $this->assertEquals('test', $user->getPrenom());
                $this->assertEquals('test', $user->getNom());
                $this->assertTrue(in_array('ROLE_OWNER', $user->getRoles()));


    }



}
