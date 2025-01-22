<?php

namespace App\Tests\Controller;

use App\Kernel;
use App\Repository\UserRepository;
use App\Repository\CategoryRepository;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class CategoryControllerTest extends WebTestCase
{
    private ?KernelBrowser $client = null;
    private ?UserRepository $userRepository = null;
    private ?CategoryRepository $categoryRepository = null;

    protected function setUp(): void
    {
        $this->client = static::createClient();
        $this->userRepository = static::getContainer()->get(UserRepository::class);
        $this->categoryRepository = static::getContainer()->get(CategoryRepository::class);
    }

    public function testIndex()
    {
        $this->client->request('GET', '/category');
        $this->assertResponseIsSuccessful();
    }

    public function testNew()
    {
        $adminUser = $this->userRepository->findOneBy(['username' => 'admin']);
        $this->client->loginUser($adminUser);

        $this->client->request('GET', '/category/new');
        $this->assertResponseIsSuccessful();

        $this->client->submitForm('Ajouter', [
            'category[name]' => 'Catégorie test',
            'category[color]' => '#000000',
        ]);

        $this->assertResponseRedirects('/category');


        $category = $this->categoryRepository->findOneBy(['name' => 'Catégorie test']);
        $this->assertEquals('Catégorie test', $category->getName());
    }

    public function testNewWithAccessDenied()
    {
        static::ensureKernelShutdown();
        $this->client = static::createClient();

        $this->client->request('GET', '/category/new');

        $this->assertResponseRedirects('/login');
    }

    public function testEdit()
    {
        $adminUser = $this->userRepository->findOneBy(['username' => 'admin']);
        $this->client->loginUser($adminUser);

        $category = $this->categoryRepository->findOneBy(['name' => 'Catégorie test']);

        $this->client->request('GET', '/category/' . $category->getId() . '/edit');
        $this->assertResponseIsSuccessful();

        $this->client->submitForm('Modifier', [
            'category[name]' => 'Catégorie test modifié',
            'category[color]' => '#212121',
        ]);

        $this->assertResponseRedirects('/category');

        $category = $this->categoryRepository->findOneBy(['name' => 'Catégorie test modifié']);
        $this->assertEquals('Catégorie test modifié', $category->getName());
    }

    public function testEditWithAccessDenied()
    {
        static::ensureKernelShutdown();
        $this->client = static::createClient();

        $category = $this->categoryRepository->findOneBy(['name' => 'Catégorie test modifié']);

        $this->client->request('GET', '/category/' . $category->getId() . '/edit');

        $this->assertResponseRedirects('/login');
    }

    public function testDeleteWithAccessDenied()
    {
        static::ensureKernelShutdown();
        $this->client = static::createClient();

        $category = $this->categoryRepository->findOneBy(['name' => 'Catégorie test modifié']);

        $this->client->request('GET', '/category/' . $category->getId());

        $this->client->submitForm('Supprimer');

        $this->assertResponseRedirects('/login');
    }

    public function testDelete()
    {
        $adminUser = $this->userRepository->findOneBy(['username' => 'admin']);
        $this->client->loginUser($adminUser);

        $category = $this->categoryRepository->findOneBy(['name' => 'Catégorie test modifié']);

        $this->client->request('GET', '/category/' . $category->getId());

        $this->client->submitForm('Supprimer');

        $category = $this->categoryRepository->findOneBy(['name' => 'Catégorie test modifié']);
        $this->assertNull($category);
    }
}