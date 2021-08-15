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
        $otherUser = $this->entityManager->getRepository(User::class)->findOneBy(['username' => 'Boby']);
        $this->client->request('GET', '/users/' . $otherUser->getId() . '/edit');
        $this->assertResponseIsSuccessful();
    }

    /**
     * Test that non allowed user is stopped to access to user part.
     */
    public function testSecurityRoleUser()
    {
        $this->client->followRedirects();

        $this->connectWithUser('user');

        $this->client->request('GET', '/users');
        $this->assertSelectorTextContains('strong', 'Oops !');
        $this->client->request('GET', '/users/create');
        $this->assertSelectorTextContains('strong', 'Oops !');
        $otherUserRoleUser = $this->entityManager->getRepository(User::class)->findOneBy(['username' => 'Boby']);
        $this->client->request('GET', '/users/' . $otherUserRoleUser->getId() . '/edit');
        $this->assertSelectorTextContains('strong', 'Oops !');
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
    public function testEditIfAdmin()
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

    public function testEditIfUser()
    {
        $user = $this->connectWithUser('user');

        $this->client->request('GET', '/users/' . $user->getId() . '/edit');

        $this->client->submitForm('Modifier', [
            'user[username]' => $user->getUserIdentifier() . 1,
            'user[plainPassword][first]' => 'test',
            'user[plainPassword][second]' => 'test',
            'user[email]' => $user->getUserIdentifier() . 1 . '@test.com'
        ]);

        $test = $this->entityManager->getRepository(User::class)->findOneBy(['id' => $user->getId()]);

        $this->assertEquals($user->getUserIdentifier() . 1, $test->getUsername());

        $this->client->followRedirect();

        $this->assertRouteSame(
            'homepage',
            [],
            'This is not the expected route'
        );

        $this->client->request('GET', '/users/' . $user->getId() . '/edit');

        $this->client->submitForm('Modifier', [
            'user[username]' => $user->getUserIdentifier(),
            'user[plainPassword][first]' => 'test',
            'user[plainPassword][second]' => 'test',
            'user[email]' => $user->getUserIdentifier() . '@test.com'
        ]);
    }

    /**
     * @param $user
     * Connect easily with the user of your choice.
     */
    public function connectWithUser($user)
    {
        $this->testUser = $this->entityManager->getRepository(User::class)->findOneBy(['username' => $user]);
        $this->client->loginUser($this->testUser);

        return $this->testUser;
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
        $this->assertRouteSame(
            $this->client->getRequest()->attributes->get('_route'),
            [],
            'This is not the expected route'
        );
    }
}
