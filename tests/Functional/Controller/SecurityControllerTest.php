<?php

namespace App\Tests\Functional\Controller;

use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class SecurityControllerTest extends WebTestCase
{
    protected $client;

    protected $entityManager;

    public function setUp(): void
    {
        $this->client = self::createClient();
        $this->entityManager = $this->client->getContainer()
            ->get('doctrine')
            ->getManager();
    }

    public function testLogin(): void
    {
        $this->client->request('GET', '/login');

        $this->client->submitForm('Se connecter', [
            '_username' => 'admin',
            '_password' => 'test'
        ]);

        $this->client->followRedirect();

        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('h1', 'Bienvenue');
    }

    public function testLogout(): void
    {
        $testUser = $this->entityManager->getRepository(User::class)->findOneBy(['username' => 'admin']);
        $this->client->loginUser($testUser);

        $this->client->followRedirects();
        $this->client->request('GET', '/logout');

        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('button', 'Se connecter');
    }

    public function testAccessWithoutLogin(){
        $this->client->request('GET', '/');
        $this->client->followRedirect();
        $this->assertRouteSame('security_login');
        $this->assertSelectorTextContains('button', 'Se connecter');

        $this->client->request('GET', '/users');
        $this->client->followRedirect();
        $this->assertRouteSame('security_login');
        $this->assertSelectorTextContains('button', 'Se connecter');

        $this->client->request('GET', '/tasks');
        $this->client->followRedirect();
        $this->assertRouteSame('security_login');
        $this->assertSelectorTextContains('button', 'Se connecter');

        $this->client->request('GET', '/login');
        $this->assertResponseIsSuccessful();
        $this->assertRouteSame('security_login');
        $this->assertSelectorTextContains('button', 'Se connecter');

        $this->client->request('GET', '/register');
        $this->assertResponseIsSuccessful();
        $this->assertRouteSame('app_register');
        $this->assertSelectorTextContains('button', 'Ajouter');
    }
}
