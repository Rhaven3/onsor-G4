<?php

namespace App\Tests\Services;

use App\Entity\User;
use App\Repository\UserRepository;
use App\Services\UserService;
use PHPUnit\Framework\TestCase;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;


class RegisterServiceTest extends TestCase
{
    public function testRegister(string $email, string $username, string $first_name, string $last_name, string $mobile): void
    {


        $registrationData = [
            'email'    => 'test@mail.com',
            'username' => 'testuser',
            'first'
            'password' => '123456',

            // autres champs selon ton entité...
        ];




    }
}
