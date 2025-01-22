<?php

namespace App\Tests\Controller;

use App\Repository\UserRepository;
use App\Repository\CoasterRepository;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;


class CoasterControllerTest extends WebTestCase
{
    private ?KernelBrowser $client = null;
    private ?UserRepository $userRepository = null;
    private ?CoasterRepository $coasterRepository = null;

    protected function setUp(): void
    {
        $this->client = static::createClient();
        $this->userRepository = static::getContainer()->get(UserRepository::class);
        $this->coasterRepository = static::getContainer()->get(CoasterRepository::class);
    }

    public function testIndex()
    {
        $this->client->request('GET', '/coaster');
        $this->assertResponseIsSuccessful();
    }

    public function testNew()
    {
        // Récupération de l'utilisateur admin
        $adminUser = $this->userRepository->findOneBy(['username' => 'admin']);
        $this->client->loginUser($adminUser);

        // Accès à la page d'ajout
        $this->client->request('GET', '/coaster/add');
        $this->assertResponseIsSuccessful(); // Vérification du chargement de la page

        // Envoi du formulaire avec des données valides
        $this->client->submitForm('Ajouter', [
            'coaster[name]' => 'Coaster test',
            'coaster[maxSpeed]' => 120,
            'coaster[maxHeight]' => 80,
            'coaster[length]' => 1200,
            'coaster[operating]' => true,
            'coaster[park]' => 1, // ID valide
        ]);

        // Vérification de la redirection après soumission
        $this->assertResponseRedirects('/coaster'); // Assurez-vous que l'URL est correcte

        // Suivre la redirection pour vérifier la page cible
        $this->client->followRedirect();
        $this->assertResponseIsSuccessful(); // Vérification de la page cible après redirection

        // Vérification dans la base de données
        $newCoaster = $this->coasterRepository->findOneBy(['name' => 'Coaster test']);
        $this->assertEquals('Coaster test', $newCoaster->getName());
    }

    public function testAddWithAccessDenied()
    {
        // Assurez-vous que le noyau précédent est bien arrêté
        static::ensureKernelShutdown();

        // Créez un nouveau client
        $client = static::createClient();

        // Envoyez une requête GET à la route
        $client->request('GET', '/coaster/add');

        // Vérifiez que la réponse redirige vers /login
        $this->assertResponseRedirects('/login');
    }

    public function testEdit()
    {
        $adminUser = $this->userRepository->findOneBy(['username' => 'admin']);
        $this->client->loginUser($adminUser);

        $coaster = $this->coasterRepository->findOneBy(['name' => 'Coaster test']);

        // Accès à la page d'édition
        $this->client->request('GET', '/coaster/' . $coaster->getId() . '/edit');
        $this->assertResponseIsSuccessful(); // Vérification du chargement de la page

        // Envoi du formulaire avec des données valides
        $this->client->submitForm('Modifier', [
            'coaster[name]' => 'Coaster test modifié',
            'coaster[maxSpeed]' => 190,
            'coaster[maxHeight]' => 90,
            'coaster[length]' => 23,
            'coaster[operating]' => false,
            'coaster[park]' => 1, // ID valide
        ]);

        // Vérification de la redirection après soumission
        $this->assertResponseRedirects('/coaster'); // Assurez-vous que l'URL est correcte

        // Suivre la redirection pour vérifier la page cible
        $this->client->followRedirect();
        $this->assertResponseIsSuccessful(); // Vérification de la page cible après redirection

        // Vérification dans la base de données
        $editedCoaster = $this->coasterRepository->findOneBy(['name' => 'Coaster test modifié']);
        $this->assertEquals('Coaster test modifié', $editedCoaster->getName());
    }

    public function testEditWithAccessDenied()
    {
        // Assurez-vous que le noyau précédent est bien arrêté
        static::ensureKernelShutdown();

        // Créez un nouveau client
        $client = static::createClient();

        $coaster = $this->coasterRepository->findOneBy(['name' => 'Coaster test modifié']);

        // Envoyez une requête GET à la route
        $client->request('GET', '/coaster/' . $coaster->getId() . '/edit');

        // Vérifiez que la réponse redirige vers /login
        $this->assertResponseRedirects('/login');
    }

    public function testDeleteWithAccessDenied()
    {
        // Assurez-vous que le noyau précédent est bien arrêté
        static::ensureKernelShutdown();

        // Créez un nouveau client
        $client = static::createClient();

        $coaster = $this->coasterRepository->findOneBy(['name' => 'Coaster test modifié']);

        // Envoyez une requête GET à la route
        $client->request('GET', '/coaster/' . $coaster->getId() . '/delete');

        // Vérifiez que la réponse redirige vers /login
        $this->assertResponseRedirects('/login');
    }

    public function testDelete()
    {
        $adminUser = $this->userRepository->findOneBy(['username' => 'admin']);
        $this->client->loginUser($adminUser);

        $coaster = $this->coasterRepository->findOneBy(['name' => 'Coaster test modifié']);

        // Accès à la page de suppression
        $this->client->request('GET', '/coaster/' . $coaster->getId() . '/delete');
        $this->assertResponseIsSuccessful(); // Vérification du chargement de la page

        $this->client->submitForm('Supprimer');

        $coaster = $this->coasterRepository->findOneBy(['name' => 'Coaster test modifié']);
        $this->assertNull($coaster);
    }
}