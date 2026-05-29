<?php

namespace App\Tests\Controller;

use App\Entity\Site;
use App\Entity\User;
use App\Repository\SiteRepository;
use App\Services\TripService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

final class TripControllerTest extends WebTestCase
{
    public function testList(): void
    {
        // Test avec une pagination existante
        $client = static::createClient();
        $client->request('GET', '/trip/list/1');
        self::assertResponseIsSuccessful();

        // Test avec une pagination inexistante
        $client->request('GET', '/trip/list/-10');
        self::assertResponseStatusCodeSame(404);
    }

    public function testListCreated(): void
    {
        $client = static::createClient();

        // Test avec une pagination existante
        $client->request('GET', '/trip/listCreated/1');
        self::assertResponseIsSuccessful();

        // Test avec une pagination inexistante
        $client->request('GET', '/trip/listCreated/-10');
        self::assertResponseStatusCodeSame(404);
    }

    public function testCreateGET(): void
    {
        $client = static::createClient();
        $client->request('GET', '/trip/create');

        self::assertResponseIsSuccessful();
    }

    public function testUpdate(): void
    {
        $client = static::createClient();
        $client->request('GET', '/trip/1/update');

        self::assertResponseIsSuccessful();
    }

    public function testDetail(): void
    {
        $client = static::createClient();
        $client->request('GET', '/trip/1');

        self::assertResponseIsSuccessful();
    }

//    public function testDetail(): void
//    {
//        $client = static::createClient();
//        $client->request('GET', '/trip/1');
//
//        self::assertResponseIsSuccessful();
//    }
}
