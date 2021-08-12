<?php

namespace App\DataFixtures;

use App\Entity\Task;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

/**
 * Class TaskFixtures
 * @package App\DataFixtures
 */
class TaskFixtures extends Fixture implements DependentFixtureInterface
{

    public function load(ObjectManager $manager)
    {
        $task1 = new Task();
        $task1->setUser($this->getReference(UserFixtures::USER1))
            ->setTitle('Do my shopping')
            ->setContent('Don\'t forget, it\'s the most important!')
            ->setIsDone(0)
        ;
        $manager->persist($task1);

        $task2 = new Task();
        $task2->setUser($this->getReference(UserFixtures::USER1))
            ->setTitle('Wash my car')
            ->setContent('Like new!')
            ->setIsDone(1)
        ;
        $manager->persist($task2);

        $task3 = new Task();
        $task3->setUser($this->getReference(UserFixtures::USER1))
            ->setTitle('test 1')
            ->setContent('test')
            ->setIsDone(0)
        ;
        $manager->persist($task3);

        $task4 = new Task();
        $task4->setUser($this->getReference(UserFixtures::USER1))
            ->setTitle('test 2')
            ->setContent('test')
            ->setIsDone(0)
        ;
        $manager->persist($task4);

        $task5 = new Task();
        $task5->setUser($this->getReference(UserFixtures::USER1))
            ->setTitle('test 3')
            ->setContent('test')
            ->setIsDone(1)
        ;
        $manager->persist($task5);

        $task6 = new Task();
        $task6->setUser($this->getReference(UserFixtures::USER1))
            ->setTitle('test 4')
            ->setContent('test')
            ->setIsDone(0)
        ;
        $manager->persist($task6);

        $task7 = new Task();
        $task7->setUser($this->getReference(UserFixtures::USER1))
            ->setTitle('test 5')
            ->setContent('test')
            ->setIsDone(0)
        ;
        $manager->persist($task7);

        $task8 = new Task();
        $task8->setUser($this->getReference(UserFixtures::USER1))
            ->setTitle('test 6')
            ->setContent('test')
            ->setIsDone(1)
        ;
        $manager->persist($task8);

        $manager->flush();
    }

    public function getDependencies(): array
    {
        return [
            UserFixtures::class,
        ];
    }
}
