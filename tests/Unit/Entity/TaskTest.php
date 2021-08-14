<?php

namespace App\Tests\Unit\Entity;

use App\Entity\Task;
use App\Entity\User;
use DateTimeImmutable;
use PHPUnit\Framework\TestCase;

/**
 * Class TaskTest.
 */
class TaskTest extends TestCase
{
    private const TASK_TITLE = 'Ceci est une tâche';

    private const TASK_CONTENT = "Ceci est la description d'une tâche";

    /**
     * Test Task entity getters and setters.
     *
     * @return void
     */
    public function testGetterSetter(): void
    {
        $task = new Task();

        $this->assertInstanceOf(Task::class, $task);
        $this->assertEquals(null, $task->getId());
        $this->assertEquals(null, $task->getTitle());
        $this->assertEquals(null, $task->getContent());
        $this->assertEquals(false, $task->getIsDone());
        $this->assertIsObject($task->getCreatedAt());
        $this->assertEquals(null, $task->getUser());

        $task->setTitle(self::TASK_TITLE);
        $this->assertEquals(self::TASK_TITLE, $task->getTitle());
        $task->setContent(self::TASK_CONTENT);
        $this->assertEquals(self::TASK_CONTENT, $task->getContent());
        $task->setCreatedAt(new DateTimeImmutable());
        $this->assertInstanceOf(DateTimeImmutable::class, $task->getCreatedAt());

        $user = new User();
        $task->setUser($user);
        $this->assertInstanceOf(User::class, $task->getUser());
    }
}
