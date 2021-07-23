<?php

namespace App\Tests\Functional\Controller;

use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class TaskControllerTest extends WebTestCase
{
    private $entityManager;

    private $client;

    protected function setUp(): void
    {
        $this->client = $this->createClient();
        $this->entityManager = $this->client->getContainer()
            ->get('doctrine')
            ->getManager();
    }

    public function tasksListTest(): void
    {
        $testUser = $this->entityManager->getRepository(User::class)->findOneBy(['username' => 'user']);
        $this->client->loginUser($testUser);
        $crawler = $this->client->request('GET', '/tasks');

        $this->assertResponseIsSuccessful();

    }

    protected function tearDown(): void
    {
        parent::tearDown();

        // doing this is recommended to avoid memory leaks
        $this->entityManager->close();
        $this->entityManager = null;
    }
}
