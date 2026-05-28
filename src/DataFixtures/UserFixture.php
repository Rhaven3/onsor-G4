<?php

namespace App\DataFixtures;

use App\Entity\Site;
use App\Entity\User;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;


class UserFixture extends Fixture implements OrderedFixtureInterface
{
    public function load(ObjectManager $manager): void
    {
        $userTest = new User();
        $userTest->setUsername('test');
        $userTest->setFirstName('Test');
        $userTest->setLastName('Test');
        $userTest->setEmail('test@testtest.fr');
        $userTest->setMobile('0666777999');
        $userTest->setPassword(password_hash('test', PASSWORD_DEFAULT));
        $userTest->setSite($manager->getRepository(Site::class)->findOneBy(['name' => 'eni']));
        $userTest->setRoles(['ROLE_USER']);
        $userTest->setActivate(true);
        $manager->persist($userTest);

        $adminTest = new User();
        $adminTest->setUsername('admin');
        $adminTest->setPassword(password_hash('admin', PASSWORD_DEFAULT));
        $adminTest->setFirstName('Admin');
        $adminTest->setLastName('Test');
        $adminTest->setEmail('test@testadmin.fr');
        $adminTest->setMobile('0666777999');
        $adminTest->setSite($manager->getRepository(Site::class)->findOneBy(['name' => 'eni']));
        $adminTest->setRoles(['ROLE_ADMIN']);
        $adminTest->setActivate(true);
        $manager->persist($adminTest);

        $deletedUser = new User();
        $deletedUser->setUsername('deleted');
        $deletedUser->setPassword(password_hash('deleted', PASSWORD_DEFAULT));
        $deletedUser->setFirstName('deleted');
        $deletedUser->setLastName('User');
        $deletedUser->setEmail('deleted@testadmin.fr');
        $deletedUser->setMobile('0666777998');
        $deletedUser->setSite($manager->getRepository(Site::class)->findOneBy(['name' => 'eni']));
        $deletedUser->setRoles(['ROLE_ADMIN']);
        $deletedUser->setActivate(false);
        $manager->persist($deletedUser);


        $manager->flush();
    }

    public function getOrder(): int {
        return 2;
    }
}
