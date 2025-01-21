<?php

namespace App\Tests\Controller;

use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;


class CoasterControllerTest extends WebTestCase
{
    public function testIndex()
    {
        $client = static::createClient();
        $client->request('GET', '/coaster');

        $this->assertResponseIsSuccessful();
    }

    public function testNew()
    {
        $client = static::createClient();

        $userRepository = static::getContainer()->get(UserRepository::class);
        $adminUser = $userRepository->findOneBy(['username' => 'admin']);
        $client->loginUser($adminUser);

        $client->request('GET', '/coaster/add');

        $this->assertResponseIsSuccessful();
    }
}