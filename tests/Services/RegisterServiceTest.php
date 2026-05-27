<?php

namespace App\Tests\Services;

use App\Entity\User;
use App\Repository\UserRepository;
use PHPUnit\Framework\TestCase;



class RegisterServiceTest extends TestCase
{
    public function testRegister(string $email, string $username, string $first_name, string $last_name, string $mobile): void
    {
        $userRepository = $this->createMock(UserRepository::class);
        $userRepository->method('findOneBy')->willReturn(null);

        $user = new User();
        $this->assertEquals('test@example.com', $user->getEmail());



    }
}
