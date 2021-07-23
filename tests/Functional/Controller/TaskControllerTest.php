<?php

namespace App\Tests\Functional\Controller;

use App\Entity\User;
use Doctrine\ORM\EntityManager;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class TaskControllerTest extends WebTestCase
{
    /**
     * @var EntityManager
     */
    private $entityManager;

    /**
     * @var KernelBrowser
     */
    private $client;

    /**
     * Set Up to load kernel and user
     */
    protected function setUp(): void
    {
        $this->client = $this->createClient();
        $this->entityManager = $this->client->getContainer()
            ->get('doctrine')
            ->getManager();

        $testUser = $this->entityManager->getRepository(User::class)->findOneBy(['username' => 'user']);
        $this->client->loginUser($testUser);
    }

    /**
     * Login and access tasks list
     */
    public function testTasksList(): void
    {
        $crawler = $this->client->request('GET', '/');

        $this->assertResponseIsSuccessful();
        $this->assertResponseStatusCodeSame('200');
    }

    /**
     * Login and access done tasks list
     */
    public function testTasksListIsDone(): void
    {
        $crawler = $this->client->request('GET', '/tasks/done');

        $this->assertResponseIsSuccessful();
        $this->assertResponseStatusCodeSame('200');
    }

    protected function tearDown(): void
    {
        parent::tearDown();

        $this->entityManager->close();
        $this->entityManager = null;
    }
}
