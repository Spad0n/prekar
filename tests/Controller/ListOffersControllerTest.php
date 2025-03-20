<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class ListOffersControllerTest extends WebTestCase
{
    public function testListOffers(): void {
        $client = static::createClient();
        $crawler = $client->request('GET', '/offer/list');
        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('h1', 'Offers');
    }

    public function testFilterByBrand(): void {
        $client = static::createClient();
        $crawler = $client->request('GET', '/offer/list?brand[]=Skoda');
        $this->assertResponseIsSuccessful();
        $crawler->filter('.offer-card .brand')->each(function ($node) {
            $this->assertStringContainsString('Skoda', $node->text(), 'Offer does not match filter');
        });
    }

    public function testFilterByPriceRange(): void {
        $client = static::createClient();
        $crawler = $client->request('GET', '/offer/list?min_price=500&max_price=1000');
        $this->assertResponseIsSuccessful();
        $crawler->filter('.offer-card .price')->each(function ($node) {
            $price = (int) filter_var($node->text(), FILTER_SANITIZE_NUMBER_INT);
            $this->assertGreaterThanOrEqual(500, $price, 'Price is below the minimum filter');
            $this->assertLessThanOrEqual(1000, $price, 'Price is above the maximum filter');
        });
    }

    public function testSorting(): void {
        $client = static::createClient();
        $crawler = $client->request('GET', '/offer/list?sort=price_asc');
        $this->assertResponseIsSuccessful();
        $prices = $crawler->filter('.offer-card .price')->each(function ($node) {
            return (int) filter_var($node->text(), FILTER_SANITIZE_NUMBER_INT);
        });
        $sortedPrices = $prices;
        sort($sortedPrices);
        $this->assertEquals($sortedPrices, $prices, 'Offers are not sorted by price in ascending order');
    }

}