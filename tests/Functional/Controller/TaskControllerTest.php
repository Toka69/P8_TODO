<?php

namespace App\Tests\Functional\Controller;

use App\Entity\Task;
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
     * @var User
     */
    private $testUser;

    /**
     * Set Up to load kernel and user
     */
    protected function setUp(): void
    {
        $this->client = $this->createClient();
        $this->entityManager = $this->client->getContainer()
            ->get('doctrine')
            ->getManager();

        $this->testUser = $this->entityManager->getRepository(User::class)->findOneBy(['username' => 'user']);
        $this->client->loginUser($this->testUser);
    }

    /**
     * Login and access tasks list
     */
    public function testTasksList(): void
    {
        $this->client->request('GET', '/');

        $this->assertResponseIsSuccessful();
    }

    /**
     * Login and access done tasks list
     */
    public function testTasksListIsDone(): void
    {
        $this->client->request('GET', '/tasks/done');

        $this->assertResponseIsSuccessful();
    }

    /**
     * Check if the displayed tasks belong to this owner.
     */
    public function testDisplayedTasksList(): void
    {
        $tasks = $this->entityManager->getRepository(Task::class)->findBy(['user' => $this->testUser]);
        $tasksId = [];
        foreach ($tasks as $task)
        {
            array_push($tasksId, $task->getId());
        }

        $crawler = $this->client->request('GET', '/tasks');

        $nodeValues = $crawler->filter('h4 > a')->each(function ($node, $i) {
            return $node->attr('href');
        });

        $tasksListTest = [];
        $test = false;
        foreach($nodeValues as $nodeValue)
        {
            $result = [];
            $nodeId = strstr(strstr($nodeValue, 'tasks/'), '/edit', true);
            for($i=0; $i < count($tasksId); $i++){
                $nodeContain = false;
                if(str_contains($nodeId, $tasksId[$i]))
                {
                    $nodeContain = true;
                }
                array_push($result, $nodeContain);
            }

            $arrayContain = false;
            if (in_array(true, $result))
            {
                $arrayContain = true;
            }
            array_push($tasksListTest, $arrayContain);
        }

        if (!in_array(false, $tasksListTest))
        {
            $test = true;
        }

        $this->assertStringContainsString('1', (string)$test);
    }

    protected function tearDown(): void
    {
        parent::tearDown();

        $this->entityManager->close();
        $this->entityManager = null;
    }
}
