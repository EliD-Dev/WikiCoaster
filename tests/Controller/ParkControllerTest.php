<?php
namespace App\Tests\Controller;

use App\Kernel;
use App\Repository\UserRepository;
use App\Repository\ParkRepository;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class ParkControllerTest extends WebTestCase
{
    private ?KernelBrowser $client = null;
    private ?UserRepository $userRepository = null;
    private ?ParkRepository $parkRepository = null;

    protected function setUp(): void
    {
        $this->client = static::createClient();
        $this->userRepository = static::getContainer()->get(UserRepository::class);
        $this->parkRepository = static::getContainer()->get(ParkRepository::class);
    }

    public function testIndex()
    {
        $this->client->request('GET', '/park');
        $this->assertResponseIsSuccessful();
    }

    public function testNew()
    {
        $adminUser = $this->userRepository->findOneBy(['username' => 'admin']);
        $this->client->loginUser($adminUser);

        $this->client->request('GET', '/park/new');
        $this->assertResponseIsSuccessful();

        $this->client->submitForm('Ajouter', [
            'park[name]' => 'Parc test',
            'park[country]' => 'FR',
            'park[openingYear]' => 1992,
        ]);

        $this->assertResponseRedirects('/park');

        $park = $this->parkRepository->findOneBy(['name' => 'Parc test']);
        $this->assertEquals('Parc test', $park->getName());
    }

    public function testNewWithAccessDenied()
    {
        static::ensureKernelShutdown();
        $this->client = static::createClient();

        $this->client->request('GET', '/park/new');

        $this->assertResponseRedirects('/login');
    }

    public function testEdit()
    {
        $adminUser = $this->userRepository->findOneBy(['username' => 'admin']);
        $this->client->loginUser($adminUser);

        $park = $this->parkRepository->findOneBy(['name' => 'Parc test']);

        $this->client->request('GET', '/park/' . $park->getId() . '/edit');
        $this->assertResponseIsSuccessful();

        $this->client->submitForm('Modifier', [
            'park[name]' => 'Parc test modifié',
            'park[country]' => 'DE',
            'park[openingYear]' => 2001,
        ]);

        $this->assertResponseRedirects('/park');

        $park = $this->parkRepository->findOneBy(['name' => 'Parc test modifié']);
        $this->assertEquals('Parc test modifié', $park->getName());
    }

    public function testEditWithAccessDenied()
    {
        static::ensureKernelShutdown();
        $this->client = static::createClient();

        $park = $this->parkRepository->findOneBy(['name' => 'Parc test modifié']);

        $this->client->request('GET', '/park/' . $park->getId() . '/edit');

        $this->assertResponseRedirects('/login');
    }

    public function testDeleteWithAccessDenied()
    {
        static::ensureKernelShutdown();
        $this->client = static::createClient();

        $park = $this->parkRepository->findOneBy(['name' => 'Parc test modifié']);

        $this->client->request('GET', '/park/' . $park->getId());
        $this->assertResponseIsSuccessful();

        $this->client->submitForm('Supprimer');

        $this->assertResponseRedirects('/login');
    }

    public function testDelete()
    {
        $adminUser = $this->userRepository->findOneBy(['username' => 'admin']);
        $this->client->loginUser($adminUser);

        $park = $this->parkRepository->findOneBy(['name' => 'Parc test modifié']);

        $this->client->request('GET', '/park/' . $park->getId());
        $this->assertResponseIsSuccessful();

        $this->client->submitForm('Supprimer');

        $park = $this->parkRepository->findOneBy(['name' => 'Parc test modifié']);
        $this->assertNull($park);
    }
}