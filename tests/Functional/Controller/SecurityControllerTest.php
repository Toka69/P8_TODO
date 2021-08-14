<?php

namespace App\Tests\Functional\Controller;

use App\Entity\User;
use Doctrine\ORM\EntityManager;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class SecurityControllerTest extends WebTestCase
{
    /**
     * @var KernelBrowser
     */
    protected $client;

    /**
     * @var EntityManager
     */
    protected $entityManager;

    /**
     * Set Up to load kernel, doctrine and user.
     */
    public function setUp(): void
    {
        $this->client = self::createClient();
        $this->entityManager = $this->client->getContainer()
            ->get('doctrine')
            ->getManager();
    }

    /**
     * Login and check than the user is redirected on the homepage.
     */
    public function testLoginSuccess(): void
    {
        $this->client->request('GET', '/login');

        $this->client->submitForm('Se connecter', [
            '_username' => 'admin',
            '_password' => 'test'
        ]);

        $this->client->followRedirect();

        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('h1', 'Bienvenue');
        $this->assertRouteSame(
            'homepage',
            [],
            'This is not the expected route'
        );
    }

    /**
     * Login and check than the user is redirected on the login page.
     */
    public function testLoginFailure(): void
    {
        $this->client->followRedirects();
        $this->client->request('GET', '/login');

        $this->client->submitForm('Se connecter', [
            '_username' => 'admiqqdfsqdffsdfdsn',
            '_password' => 'testqsdsqdqssd'
        ]);

        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('button', 'Se connecter');
        $this->assertRouteSame(
            'security_login',
            [],
            'This is not the expected route'
        );
    }

    /**
     * Call the logout path and check than the user is redirected on the login page.
     */
    public function testLogout(): void
    {
        $testUser = $this->entityManager->getRepository(User::class)->findOneBy(['username' => 'admin']);
        $this->client->loginUser($testUser);

        $this->client->followRedirects();
        $this->client->request('GET', '/logout');

        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('button', 'Se connecter');
        $this->assertRouteSame(
            'security_login',
            [],
            'This is not the expected route'
        );
    }

    /**
     * Check some connectionless paths and verify that all are correct.
     */
    public function testAccessWithoutLogin()
    {
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

    public function testRedirectAlreadyConnected(): void
    {
        $this->client->loginUser($this->entityManager->getRepository(User::class)->findOneBy(['username' => 'user']));

        $this->client->request('GET', '/login');

        $this->client->followRedirect();

        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('h1', 'Bienvenue sur Todo List');
    }
}
