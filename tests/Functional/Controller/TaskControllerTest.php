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
        $this->goPage('/', 'Consulter la liste des tâches à faire');
    }

    /**
     * Login and access done tasks list
     */
    public function testTasksListIsDone(): void
    {
        $this->goPage('/', 'Consulter la liste des tâches terminées');
    }

    /**
     * Check if the displayed tasks are only "to do" and that they belong to its owner.
     */
    public function testDisplayedTasksList(): void
    {
        $this->displayedTasksAreCompliant('/tasks', false);
    }

    /**
     * Check if the displayed tasks are only "is done" and that they belong to its owner.
     */
    public function testDisplayedIsDoneTasksList(): void
    {
        $this->displayedTasksAreCompliant('/tasks/done', true);
    }

    /**
     * Create a task from the homepage
     */
    public function testCreate(): void
    {
        $this->goPage('/', 'Créer une nouvelle tâche');

        $this->client->submitForm('Ajouter',[
            'task[title]' => 'test 100',
            'task[content]' => 'Un contenu de test'
        ]);

        $task = $this->entityManager->getRepository(Task::class)->findOneBy(['title' => 'test 100', 'isDone' => false]);

        $this->assertEquals('user', $task->getUser()->getUsername());
    }

    /**
     * Edit a task from the "to do" list.
     */
    public function testEdit()
    {
        $task = $this->entityManager->getRepository(Task::class)->findOneBy(['user' => $this->testUser]);

        $this->client->request('GET', '/tasks/'.$task->getId().'/edit');

        $this->client->submitForm('Modifier', [
            'task[title]' => 'Une tâche à éditer',
            'task[content]' => 'Et son contenu'
        ]);

        $test = $this->entityManager->getRepository(Task::class)->findOneBy(['id' => $task->getId()]);

        $this->assertEquals('Une tâche à éditer', $test->getTitle());
    }

    /**
     * Delete a task by its owner.
     */
    public function testDelete(): void
    {
        $task = $this->entityManager->getRepository(Task::class)->findOneBy(['user' => $this->testUser]);

        $this->client->request('GET', '/tasks/'.$task->getId().'/delete');

        $test = $this->entityManager->getRepository(Task::class)->findOneBy(['id' => $task->getId()]);

        $this->assertEquals(null, $test, 'The task has not been deleted');
    }

    /**
     * Delete a task by another user is not allowed
     */
    public function testDeleteByAnotherUser()
    {
        $this->testUser = $this->entityManager->getRepository(User::class)->findOneBy(['username' => 'John']);
        $this->client->loginUser($this->testUser);

        $task = $this->entityManager->getRepository(Task::class)->findOneBy(['user' => $this->testUser]);

        $this->testUser = $this->entityManager->getRepository(User::class)->findOneBy(['username' => 'user']);
        $this->client->loginUser($this->testUser);

        $this->client->request('GET', '/tasks/'.$task->getId().'/delete');

//        $test = $this->entityManager->getRepository(Task::class)->findOneBy(['id' => $task->getId()]);
//        $this->assertEquals(true, $test, 'The task has been deleted by another user.');

        $this->assertResponseStatusCodeSame('403');
    }

    /**
     * Toggle a "to do" to "is done" task.
     */
    public function testPassIsDone(): void
    {
        $task = $this->entityManager->getRepository(Task::class)->findOneBy(['user' => $this->testUser, 'isDone' => false]);

        $this->client->request('GET', '/tasks/'.$task->getId().'/toggle');

        $test = $this->entityManager->getRepository(Task::class)->findOneBy(['id' => $task->getId()]);

        $this->assertEquals(true, $test->getIsDone());
    }

    /**
     * Toggle a "is done" to "to do" task.
     */
    public function testPassToDo(): void
    {
        $task = $this->entityManager->getRepository(Task::class)->findOneBy(['user' => $this->testUser, 'isDone' => true]);

        $this->client->request('GET', '/tasks/'.$task->getId().'/toggle');

        $test = $this->entityManager->getRepository(Task::class)->findOneBy(['id' => $task->getId()]);

        $this->assertEquals(false, $test->getIsDone());
    }

    protected function tearDown(): void
    {
        parent::tearDown();

        $this->entityManager->close();
        $this->entityManager = null;
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

    /**
     * @param $uri
     * @param $isDone
     * Check if the displayed tasks are compliant.
     */
    public function displayedTasksAreCompliant($uri, $isDone)
    {
        $tasks = $this->entityManager->getRepository(Task::class)->findBy(['user' => $this->testUser, 'isDone' => $isDone]);
        $tasksId = [];
        foreach ($tasks as $task)
        {
            array_push($tasksId, $task->getId());
        }

        $crawler = $this->client->request('GET', $uri);

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

        $this->assertStringContainsString('1', (string)$test, 'The displayed tasks are not correct');
    }
}
