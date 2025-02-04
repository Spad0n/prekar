<?php

namespace App\Tests\Form;

use App\Entity\Offer;
use App\Form\OfferFormType;
use Symfony\Component\Form\Test\TypeTestCase;

class OfferFormTypeTest extends TypeTestCase
{
public function testSubmitValidData()
{
$formData = [
'startDate' => '2024-02-01',
'endDate' => '2024-02-10',
'localisationGarage' => 'King jouet en face du auchan',
'price' => 10000000000.00,
'delivery' => 'yes',
'available' => 'available',
];
// create a form
$form = $this->factory->create(OfferFormType::class);
$form->submit($formData);

$this->assertTrue($form->isSubmitted()); //check if the test is submitted
$this->assertTrue($form->isValid()); //check if the test is valid

    // check if everything is ok  in the formù
$offer = $form->getData();
$this->assertInstanceOf(Offer::class, $offer);
$this->assertEquals(new \DateTime('2024-02-01'), $offer->getStartDate());
$this->assertEquals(new \DateTime('2024-02-10'), $offer->getEndDate());
$this->assertEquals('King jouet en face du auchan', $offer->getLocalisationGarage());
$this->assertEquals(10000000000.00, $offer->getPrice());
$this->assertEquals('yes', $offer->getDelivery());
$this->assertEquals('available', $offer->getAvailable());
}

public function testSubmitInvalidData()
{
$formData = [
'startDate' => '',
'price' => -50,
'delivery' => 'huuuu ? idk',
];

$form = $this->factory->create(OfferFormType::class);

//we're going to have exception invalidargumentexception
$this->expectException(\Symfony\Component\PropertyAccess\Exception\InvalidArgumentException::class);
$form->submit($formData);

}

public function testSubmitMissingOptionalFields()
{
$formData = [
'startDate' => '2024-02-01',
'price' => 150.00,
];

$form = $this->factory->create(OfferFormType::class);
$form->submit($formData);

$this->assertTrue($form->isValid());

// optional fields :
$offer = $form->getData();
$this->assertNull($offer->getEndDate());
$this->assertNull($offer->getLocalisationGarage());
$this->assertNull($offer->getDelivery());
$this->assertNull($offer->getAvailable());
}
}
