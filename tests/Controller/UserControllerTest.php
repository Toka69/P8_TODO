<?php

namespace App\Tests\Controller;

use App\Repository\TaskRepository;
use App\Repository\UserRepository;
use Facebook\WebDriver\Exception\NoSuchElementException;
use Facebook\WebDriver\Exception\TimeoutException;
use Symfony\Component\Panther\DomCrawler\Crawler;
use Symfony\Component\Panther\PantherTestCase;

class UserControllerTest extends PantherTestCase
{
    /**
     * @throws NoSuchElementException
     * @throws TimeoutException
     */
    public function testTasksList(): void
    {
        self::bootKernel();
        $container = static::getContainer();

        //We get tasks from user
        $userRepository = $container->get(UserRepository::class);
        $user = $userRepository->findOneBy(['username' => 'user']);

        $taskRepository = $container->get(TaskRepository::class);
        $tasks = $taskRepository->findBy(['user' => $user]);

        $tasksId = [];
        foreach ($tasks as $task)
        {
            array_push($tasksId, $task->getId());
        }

        //launch the web client
        $client = static::createPantherClient();
        $crawler = $client->request('GET', '/login');
        $client->waitForVisibility('#login-form');

        //Connection with the user
        $form = $crawler->filter('#login-form')->form(
            [
                '_username' => 'user',
                '_password' => 'test'
            ]
        );

        $client->submit($form);

        //Go to the tasks list
        $crawler = $client->request('GET', '/tasks');

        $nodeValues = $crawler->filter('h4 > a')->each(function (Crawler $node, $i)
        {
            return $node->getAttribute('href');
        });

        //Check if the displayed tasks belong to this owner.
        $tasksListTest = [];
        $test = false;
        foreach($nodeValues as $nodeValue)
        {
            $result = [];
            $nodeId = strstr(strstr($nodeValue, 'tasks/'), '/edit', true);

            dump($nodeId);
            for($i=0; $i < count($tasksId); $i++){
                $nodeContain = false;
                if(str_contains($nodeId, $tasksId[$i]))
                {
                    $nodeContain = true;
                }
                array_push($result, $nodeContain);
            }

            dump($result);

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
}
