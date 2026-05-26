<?php

namespace App\DataFixtures;

use App\Entity\Address;
use App\Entity\Site;
use App\Entity\Trip;
use App\Entity\User;
use App\Enum\StateEnum;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;

class TripFixture extends Fixture implements OrderedFixtureInterface
{
    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create("fr_FR");
        $user = $manager->getRepository(User::class)->findOneBy(['username' => 'test']);


        $address = new Address();
        $address->setName($faker->name())
        ->setStreet($faker->streetAddress())
        ->setState($faker->jobTitle())
        ->setCity($faker->city())
        ->setPostcode($faker->postcode())
        ->setLatitude($faker->latitude())
            ->setState(StateEnum::CREATED)
        ->setLongitude($faker->longitude());

        $manager->persist($address);
        $manager->flush();


        for ($i = 0; $i < 10; $i++) {
            $trip = new Trip();

            $fakeStartDate = $faker->dateTimeBetween('now', '+1 year');
            $fakeEndDate = $faker->dateTimeInInterval($fakeStartDate, '+1 month');

            $trip->setName($faker->name() . $faker->randomLetter() . $faker->colorName())
            ->setStartDate($fakeStartDate)
            ->setEndDate($fakeEndDate)
            ->setLimitRegistrationDate($faker->dateTimeBetween($fakeStartDate, $fakeEndDate))
            ->setDescription($faker->text())
            ->setOrganisator($user)
            ->setMaxRegistration(10)
            ->setAddress($address)
            ->setSite($manager->getRepository(Site::class)->findOneBy(['name' => 'eni']));

            $manager->persist($trip);
        }
        $manager->flush();
    }

    public function getOrder(): int {
        return 3;
    }
}
