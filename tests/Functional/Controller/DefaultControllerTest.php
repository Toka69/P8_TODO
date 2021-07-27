<?php

namespace App\Tests\Functional\Controller;

use App\Entity\User;
use Doctrine\ORM\EntityManager;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class DefaultControllerTest extends WebTestCase
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
     * Login and test the homepage.
     */
    public function testHomepage(): void
    {
        $this->testUser = $this->entityManager->getRepository(User::class)->findOneBy(['username' => 'user']);
        $this->client->loginUser($this->testUser);

        $crawler = $this->client->request('GET', '/');

        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('h1', 'Bienvenue');
    }
}
