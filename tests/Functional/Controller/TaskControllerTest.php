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
        $this->pageWorks('/', 'Consulter la liste des tâches à faire');
    }

    /**
     * Login and access done tasks list
     */
    public function testTasksListIsDone(): void
    {
        $this->pageWorks('/tasks/done', 'Consulter la liste des tâches terminées');
    }

    /**
     * @param $uri
     * @param $linkToClick
     * Click on the link and check that the page is correctly render an that's the expected route.
     */
    public function pageWorks($uri, $linkToClick)
    {
        $crawler = $this->client->request('GET', $uri);
        $link = $crawler->selectLink($linkToClick)->link();
        $this->client->click($link);

        $this->assertResponseIsSuccessful();
        $this->assertRouteSame($this->client->getRequest()->attributes->get('_route'));
    }

    /**
     * Check if the displayed tasks are only "to do" and that they belong to its owner.
     */
    public function testDisplayedTasksList(): void
    {
        $tasks = $this->entityManager->getRepository(Task::class)->findBy(['user' => $this->testUser, 'isDone' => false]);
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

    /**
     * Check if the displayed tasks are only "is done" and that they belong to its owner.
     */
    public function testDisplayedIsDoneTasksList(): void
    {
        $tasks = $this->entityManager->getRepository(Task::class)->findBy(['user' => $this->testUser, 'isDone' => true]);
        $tasksId = [];
        foreach ($tasks as $task)
        {
            array_push($tasksId, $task->getId());
        }

        $crawler = $this->client->request('GET', '/tasks/done');

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
