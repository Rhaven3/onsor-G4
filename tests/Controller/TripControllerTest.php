<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

final class TripControllerTest extends WebTestCase
{
    public function testIndex(): void
    {
        $client = static::createClient();
        $client->request('GET', '/trip');

        self::assertResponseIsSuccessful();
    }

    public function testCreate(): void
    {
        $client = static::createClient();
        $client->request('GET', '/trip/create');

        self::assertResponseIsSuccessful();
    }

    public function testDetail(): void
    {
        $client = static::createClient();
        $client->request('GET', '/trip/1');

        self::assertResponseIsSuccessful();
    }
}
