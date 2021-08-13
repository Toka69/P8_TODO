<?php

namespace App\Tests\Functional\Controller;

use App\Entity\User;
use Doctrine\ORM\EntityManager;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class RegistrationControllerTest extends WebTestCase
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

    public function testSomething(): void
    {
        $this->client->request('GET', '/register');

        $lastUser = $this->entityManager->getRepository(User::class)->findOneBy([], ['id' => 'DESC'], 1, 0);

        $i = $lastUser->getId() + 1;

        $this->client->submitForm('Ajouter', [
            'registration_form[username]' => 'jack'. $i,
            'registration_form[plainPassword][first]' => 'testtest',
            'registration_form[plainPassword][second]' => 'testtest',
            'registration_form[email]' => 'jack' . $i . '@test.com'
        ]);

        $this->client->followRedirect();

        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('button', 'Se connecter');
        $this->assertSelectorTextContains('strong', 'Superbe !');
    }

    public function testRedirectAlreadyConnected(): void
    {
        $this->client->loginUser($this->entityManager->getRepository(User::class)->findOneBy(['username' => 'user']));

        $this->client->request('GET', '/register');

        $this->client->followRedirect();

        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('h1', 'Bienvenue sur Todo List');
    }
}
