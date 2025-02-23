<?php

namespace App\Tests\Controller;

use App\Entity\Admin;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AdminPanelControllerTest extends WebTestCase
{
    private $client;
    private $entityManager;

    public function setUp(): void
    {
        $this->client = static::createClient();
        $container = $this->client -> getContainer();
        $this ->entityManager = $container ->get(EntityManagerInterface::class);

        //check if there's not already an admin :

        $existingUser = $this->entityManager->getRepository(Admin::class)->findOneBy(['email' => 'admin@admin.com']);
        if ($existingUser) {
            $this->entityManager->remove($existingUser);
            $this->entityManager->flush();
        }


        $user = new Admin();

        $user ->setName('admin');
        $user->addRole('ROLE_ADMIN');

        $user ->setEmail('admin@admin.com');
        $passwordHasher = $container->get(UserPasswordHasherInterface::class);
        $hashedPassword = $passwordHasher->hashPassword($user, 'admin');
        $user->setPassword($hashedPassword);

        $this->entityManager->persist($user);
        $this->entityManager->flush();
    }

    public function testRedirectNonAdminUser(): void
    {
        $crawler = $this->client->request('GET', '/admin');
        $this->assertResponseRedirects('/login');
    }

    public function testAdminPanel(): void
    {
        $crawler = $this->client->request('GET', '/admin');
        $this->assertResponseRedirects('/login');
    }


    public function testAdminServices(): void
    {
        $crawler = $this->client->request('GET', '/login');
        $submitButton = $crawler->selectButton('Sign in');
        $form = $submitButton->form([
            'email' => 'admin@admin.com',
            'password' => 'admin',
        ]);
        $this->client->submit($form);
        $this->client->followRedirect();

        $this->client->request('GET', '/admin/services_dashboard');
        $this->assertResponseIsSuccessful();
    }

    public function testAdminUser(): void
    {
        $crawler = $this->client->request('GET', '/login');
        $submitButton = $crawler->selectButton('Sign in');
        $form = $submitButton->form([
            'email' => 'admin@admin.com',
            'password' => 'admin',
        ]);
        $this->client->submit($form);
        $this->client->followRedirect();

        $this->client->request('GET', '/admin/user_dashboard');
        $this->assertResponseIsSuccessful();
    }


    /*
     * Problem in this test
     */
    public function testAdminDriverLicence(): void
    {
        $crawler = $this->client->request('GET', '/login');
        $submitButton = $crawler->selectButton('Sign in');
        $form = $submitButton->form([
            'email' => 'admin@admin.com',
            'password' => 'admin',
        ]);
        $this->client->submit($form);
        $this->client->followRedirect();

        $this->client->request('GET', '/admin/driver_dashboard');
        $this->assertResponseIsSuccessful();
    }




}
