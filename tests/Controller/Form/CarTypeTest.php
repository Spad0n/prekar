<?php

namespace App\Tests\Form;

use App\Entity\Car;
use App\Form\CarType;
use Symfony\Component\Form\Test\TypeTestCase;

class CarTypeTest extends TypeTestCase
{
    public function testSubmitValidData()
    {
        $formData = [
            'brand' => 'twingo velault',
            'model' => '1',
            'registration' => 'AAA-111-WW',
            'nbSeat' => 5,
            'bootCapacity' => 500,
            'fuelType' => 'petrol',
        ];

        $form = $this->factory->create(CarType::class);
        $form->submit($formData);

        $this->assertTrue($form->isSubmitted());
        $this->assertTrue($form->isValid());

        /** @var Car $car */
        $car = $form->getData();
        $this->assertInstanceOf(Car::class, $car);
        $this->assertEquals('twingo velault', $car->getBrand());
        $this->assertEquals('1', $car->getModel());
        $this->assertEquals('AAA-111-WW', $car->getRegistration());
        $this->assertEquals(5, $car->getNbSeat());
        $this->assertEquals(500, $car->getBootCapacity());
        $this->assertEquals('petrol', $car->getFuelType());
    }



    public function testSubmitOptionalFields()
    {
        $formData = [
            'brand' => 'Velault',
            'model' => 'Clio de fou',
            'registration' => 'AAA-WWW-ZZZ',
            'nbSeat' => 4,
            'fuelType' => 'petrol',
        ];

        $form = $this->factory->create(CarType::class);
        $form->submit($formData);

        $this->assertTrue($form->isValid());
        //optional :
        $car = $form->getData();
        $this->assertNull($car->getBootCapacity());
    }
}
