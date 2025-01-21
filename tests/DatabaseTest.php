<?php

namespace App\Tests;

use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use App\Entity\Owner;

class DatabaseTest extends KernelTestCase{
    private $entityManager;

    public function setUp(): void
    {
        $kernel = self::bootKernel();
        $this->entityManager = $kernel->getContainer()->get('doctrine')->getManager();
    }

    public function testDatabaseConnection(): void
    {
        $this->assertNotNull($this->entityManager); 
    }
    
    public function testOwnerInsertedSuccessfully(): void
    {
        $owner = new Owner();
        $owner->setNom('Leo Andre');
        $owner->setEmail('c@b.c');
        $owner->setPassword('123456');
        $this->entityManager->persist($owner);
        $this->entityManager->flush();

        $insertedNom = $this->entityManager->getRepository(Owner::class)->findOneBy([
            'nom' => 'Leo Andre',
        ]);

        $this->assertNotNull($insertedNom);
        $this->assertEquals('Leo Andre', $insertedNom->getNom());
    }
}