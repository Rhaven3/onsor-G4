<?php

namespace App\DataFixtures;

use App\Entity\Site;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;

class SiteFixture extends Fixture implements OrderedFixtureInterface
{
    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create("fr_FR");

        $site = new Site();
        $site->setName('eni');

        $manager->persist($site);
        $manager->flush();
    }

    public function getOrder(): int {
        return 1;
    }
}
