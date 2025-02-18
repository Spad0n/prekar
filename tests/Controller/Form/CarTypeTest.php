<?php

namespace App\Tests\Form;

use App\Entity\Car;
use App\Form\CarType;
use Symfony\Component\Form\Extension\Validator\ValidatorExtension;
use Symfony\Component\Form\Test\TypeTestCase;
use Symfony\Component\Validator\Validation;

class CarTypeTest extends TypeTestCase
{
    protected function getExtensions()
    {
        return [new ValidatorExtension(Validation::createValidator())];
    }

    public function testSubmitValidData()
    {
        $formData = [
            'brand' => 'twingo velault',
            'model' => '1',
            'registration' => '123 AB 75',
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
        $this->assertEquals('123 AB 75', $car->getRegistration());
        $this->assertEquals(5, $car->getNbSeat());
        $this->assertEquals(500, $car->getBootCapacity());
        $this->assertEquals('petrol', $car->getFuelType());
    }



    public function testSubmitOptionalFields()
    {
        $formData = [
            'brand' => 'Velault',
            'model' => 'Clio de fou',
            'registration' => '123 AB 75',
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

    public function testSubmitFail()
    {
        $formData = [
            'brand' => 'twingo velault',
            'model' => '1',
            'registration' => '12253 zaAB 75',
            'nbSeat' => 5,
            'bootCapacity' => 500,
            'fuelType' => 'petrol',
        ];

        $form = $this->factory->create(CarType::class);
        $form->submit($formData);

        $this->assertTrue($form->isSubmitted());
        $this->assertFalse($form->isValid());

    }

}
