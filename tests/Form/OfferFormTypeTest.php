<?php

namespace App\Tests\Form;

use App\Entity\User;
use App\Entity\Offer;
use App\Entity\Car;
use App\Form\OfferFormType;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Form\PreloadedExtension;
use Symfony\Component\Form\Test\TypeTestCase;
use Doctrine\ORM\EntityManagerInterface;

class OfferFormTypeTest extends KernelTestCase
{
    private EntityManagerInterface $entityManager;

    protected function setUp(): void
    {
        self::bootKernel();
        $this->entityManager = static::getContainer()->get(EntityManagerInterface::class);
    }

    public function testSubmitValidData(): void
    {
        $user = $this->entityManager->getRepository(User::class)->findOneBy([]);
        $car = $this->entityManager->getRepository(Car::class)->findOneBy(['userOwner' => $user]);

        $formData = [
            'startDate' => '2025-02-20',
            'endDate' => '2025-02-25',
            'localisationGarage' => 'Laxou',
            'price' => 150,
            'delivery' => 'yes',
            'available' => 'available',
            'existingCar' => $car ? $car->getId() : null,
        ];

        $offer = new Offer();
        $formFactory = static::getContainer()->get('form.factory');
        $form = $formFactory->create(OfferFormType::class, $offer, ['user_cars' => [$car],     'csrf_protection' => false,]);

        $form->submit($formData);

        $this->assertTrue($form->isSynchronized());
        $this->assertTrue($form->isValid());
        $this->assertEquals('Laxou', $offer->getLocalisationGarage());
        $this->assertEquals(150, $offer->getPrice());
        $this->assertEquals('available', $offer->getAvailable());
        $this->assertEquals('yes', $offer->getDelivery());
        $this->assertEquals('2025-02-20', $offer->getStartDate()->format('Y-m-d'));
        $this->assertEquals('2025-02-25', $offer->getEndDate()->format('Y-m-d'));
        $this->assertEquals($car, $offer->getCar());
    }


    public function testSubmitInvalidData(): void
    {
        $user = $this->entityManager->getRepository(User::class)->findOneBy([]);
        $car = $this->entityManager->getRepository(Car::class)->findOneBy(['userOwner' => $user]);

        $offer = new Offer();

        $formFactory = static::getContainer()->get('form.factory');
        $form = $formFactory->create(OfferFormType::class, $offer, ['user_cars' => [$car],     'csrf_protection' => false,]);

        $formData = [
            'startDate' => '2024-02-25',
            'endDate' => '2024-02-20',
            'localisationGarage' => '',
            'price' => 50,
            'delivery' => 'maybe',
            'available' => 'unknown',
        ];
        $form->submit($formData);
        $this->assertFalse($form->isValid());
    }

    public function testSubmitInvalidDate(): void
    {
        $user = $this->entityManager->getRepository(User::class)->findOneBy([]);
        $car = $this->entityManager->getRepository(Car::class)->findOneBy(['userOwner' => $user]);

        $offer = new Offer();

        $formFactory = static::getContainer()->get('form.factory');
        $form = $formFactory->create(OfferFormType::class, $offer, ['user_cars' => [$car],     'csrf_protection' => false,]);

        $formData = [
            'startDate' => '2024-02-25',
            'endDate' => '2024-02-20',
            'localisationGarage' => 'Laxou',
            'price' => 150,
            'delivery' => 'yes',
            'available' => 'available',
        ];
        $form->submit($formData);
        $this->assertFalse($form->isValid());
    }


    protected function tearDown(): void
    {
        parent::tearDown();
        $this->entityManager->close();
    }
}
