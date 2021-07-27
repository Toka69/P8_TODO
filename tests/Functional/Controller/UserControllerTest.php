<?php

namespace App\Tests\Functional\Controller;

use App\Entity\User;
use Doctrine\ORM\EntityManager;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class UserControllerTest extends WebTestCase
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
     * @var User
     */
    private $testUser;

    /**
     * Set Up to load kernel, doctrine and user.
     */
    public function setUp(): void
    {
        $this->client = $this->createClient();
        $this->entityManager = $this->client->getContainer()
            ->get('doctrine')
            ->getManager();

        $this->connectWithUser('admin');
    }

    /**
     * Test if ROLE_ADMIN can access to user part.
     */
    public function testSecurityRoleAdmin()
    {
        $this->client->request('GET', '/users');
        $this->assertResponseIsSuccessful();
        $this->client->request('GET', '/users/create');
        $this->assertResponseIsSuccessful();
        $otherUser = $this->entityManager->getRepository(User::class)->findOneBy(['username' => 'John']);
        $this->client->request('GET', '/users/' . $otherUser->getId() . '/edit');
        $this->assertResponseIsSuccessful();
    }

    /**
     * Test that non allowed user is stopped to access to user part.
     */
    public function testSecurityRoleUser()
    {
        $this->connectWithUser('user');

        $this->client->request('GET', '/users');
        $this->assertResponseStatusCodeSame('403');
        $this->client->request('GET', '/users/create');
        $this->assertResponseStatusCodeSame('403');
        $otherUserRoleUser = $this->entityManager->getRepository(User::class)->findOneBy(['username' => 'John']);
        $this->client->request('GET', '/users/' . $otherUserRoleUser->getId() . '/edit');
        $this->assertResponseStatusCodeSame('403');
    }

    /**
     * Create a new user from the homepage
     */
    public function testCreate()
    {
        $this->goPage('/', 'Créer un utilisateur');

        $lastUser = $this->entityManager->getRepository(User::class)->findOneBy([], ['id' => 'DESC'], 1, 0);
        $i = $lastUser->getId() + 1;
        $this->client->submitForm('Ajouter', [
            'user[username]' => 'user' . $i,
            'user[plainPassword][first]' => 'test',
            'user[plainPassword][second]' => 'test',
            'user[email]' => 'user' . $i . '@test.com'
        ]);

        $user = $this->entityManager->getRepository(User::class)->findOneBy(['email' => 'user' . $i . '@test.com']);

        $this->assertEquals('user' . $i, $user->getUsername(), 'The user hasn\'t been created in the database');
    }

    /**
     * Edit a user
     */
    public function testEdit()
    {
        $lastUser = $this->entityManager->getRepository(User::class)->findOneBy([], ['id' => 'DESC'], 1, 0);

        $this->client->request('GET', '/users/' . $lastUser->getId() . '/edit');

        $i = $lastUser->getId() + 1;

        $this->client->submitForm('Modifier', [
            'user[username]' => 'bob' . $i,
            'user[plainPassword][first]' => 'test',
            'user[plainPassword][second]' => 'test',
            'user[email]' => 'bob' . $i . '@test.com'
        ]);

        $test = $this->entityManager->getRepository(User::class)->findOneBy(['id' => $lastUser->getId()]);

        $this->assertEquals('bob' . $i, $test->getUsername());
    }

    /**
     * @param $user
     * Connect easily with the user of your choice.
     */
    public function connectWithUser($user)
    {
        $this->testUser = $this->entityManager->getRepository(User::class)->findOneBy(['username' => $user]);
        $this->client->loginUser($this->testUser);
    }

    /**
     * @param $uri
     * @param $linkToClick
     * Click on the link and check that the page is correctly render and that's the expected route.
     */
    public function goPage($uri, $linkToClick)
    {
        $crawler = $this->client->request('GET', $uri);
        $link = $crawler->selectLink($linkToClick)->link();
        $this->client->click($link);

        $this->assertResponseIsSuccessful();
        $this->assertRouteSame($this->client->getRequest()->attributes->get('_route'), [], 'This is not the expected route');
    }
}
