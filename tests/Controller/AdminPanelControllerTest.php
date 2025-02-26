<?php

namespace App\Tests\Controller;

use App\Entity\Admin;
use App\Entity\User;
use App\Entity\ValidateUser;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AdminPanelControllerTest extends WebTestCase
{
    private $client;
    private $entityManager;
    private $userId;

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

        //check if there's not already a user :
        $existingUser2 = $this->entityManager->getRepository(User::class)->findOneBy(['email' => 'user@user.com']);
        if ($existingUser2) {
            $this->entityManager->remove($existingUser2);
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

        $user2 = new User();

        $user2 ->setName('user');
        $user2->addRole('ROLE_USER');
        $user2->addRole('ROLE_BORROWER');
        $user2->setEmail('user@user.com');
        $passwordHasher = $container->get(UserPasswordHasherInterface::class);
        $hashedPassword = $passwordHasher->hashPassword($user2, 'user');
        $user2->setPassword($hashedPassword);



        $this->entityManager->persist($user2);
        $this->entityManager->flush();

        $existingUser3 = $this->entityManager->getRepository(User::class)->findOneBy(['email' => 'user@user.com']);
        $this->userId = $existingUser3->getId();


        $userValidate = new ValidateUser();
        $userValidate->setUser($existingUser3);
        $userValidate->setState('Pending');
        $this->entityManager->persist($userValidate);
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

    /*
     * Check if we can find one user
     */
    public function testFindUser(): void {
        $crawler = $this->client->request('GET', '/login');
        $submitButton = $crawler->selectButton('Sign in');
        $form = $submitButton->form([
            'email' => 'admin@admin.com',
            'password' => 'admin',
        ]);
        $this->client->submit($form);
        $this->client->followRedirect();

        $crawlerUser = $this->client->request('GET', '/admin/user_dashboard');
        $submitUser = $crawlerUser->selectButton('Search');
        $formUser = $submitUser->form([
            'search_email' => 'user@user.com']);
        $this->client->submit($formUser);
        $this->assertResponseIsSuccessful();

        $clientnb = $this->client->getCrawler()->filter('tbody tr')->count();
        $this->assertEquals(1, $clientnb);
    }

    public function testCantFindUser(): void {
        $crawler = $this->client->request('GET', '/login');
        $submitButton = $crawler->selectButton('Sign in');
        $form = $submitButton->form([
            'email' => 'admin@admin.com',
            'password' => 'admin',
        ]);
        $this->client->submit($form);
        $this->client->followRedirect();

        $crawlerUser = $this->client->request('GET', '/admin/user_dashboard');
        $submitUser = $crawlerUser->selectButton('Search');
        $formUser = $submitUser->form([
            'search_email' => 'user@user.fr',
            'search_name' => 'üs3r',
        ]);
        $this->client->submit($formUser);
        $this->assertResponseIsSuccessful();

        $clientnb = $this->client->getCrawler()->filter('tbody tr')->count();
        $this->assertEquals(0, $clientnb);
    }


    //Test for the user's driver licence
    public function testAcceptDriverLicenceFindUsertwo(): void {
        $crawler = $this->client->request('GET', '/login');
        $submitButton = $crawler->selectButton('Sign in');
        $form = $submitButton->form([
            'email' => 'admin@admin.com',
            'password' => 'admin',
        ]);
        $this->client->submit($form);
        $this->client->followRedirect();

        $this->client->request('POST', '/admin/driver_dashboard', ['filters' => ['Pending']]);

        $this->assertResponseIsSuccessful();
        $nbPending = $this->client->getCrawler()->filter('tbody')->count();
        dump($this->client->getCrawler()->filter('tbody')->html());

        $this->assertEquals(1, $nbPending);
    }

}
