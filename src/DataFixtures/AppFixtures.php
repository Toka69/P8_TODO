<?php

namespace App\DataFixtures;

use App\Entity\Task;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use App\Entity\User;

class AppFixtures extends Fixture
{
    private $passwordHasher;

    public function __construct(UserPasswordHasherInterface $passwordHasher)
    {
        $this->passwordHasher = $passwordHasher;
    }

    public function load(ObjectManager $manager)
    {
        /**** Users ****/
        $user1 = new User();
        $user1->setUsername('John')
            ->setRoles((array)'ROLE_USER')
            ->setPassword($this->passwordHasher->hashPassword(
                $user1,
                'test1'
            ))
        ;
        $manager->persist($user1);

        $user2 = new User();
        $user2->setUsername('Boby')
            ->setRoles((array)'ROLE_ADMIN')
            ->setPassword($this->passwordHasher->hashPassword(
                $user2,
                'test2'
            ))
            ->setEmail('boby@test.com')
        ;
        $manager->persist($user2);

        $user3 = new User();
        $user3->setUsername('admin')
            ->setRoles((array)'ROLE_ADMIN')
            ->setPassword($this->passwordHasher->hashPassword(
                $user3,
                'test'
            ))
            ->setEmail('admin@test.com')
        ;
        $manager->persist($user3);

        $user4 = new User();
        $user4->setUsername('user')
            ->setRoles((array)'ROLE_USER')
            ->setPassword($this->passwordHasher->hashPassword(
                $user4,
                'test'
            ))
            ->setEmail('test@test.com')
        ;
        $manager->persist($user4);

        /**** Tasks ****/
        $task1 = new Task();
        $task1->setUser($user1)
            ->setTitle('Do my shopping')
            ->setContent('Don\'t forget, it\'s the most important!')
            ->setIsDone(0)
        ;
        $manager->persist($task1);

        $task2 = new Task();
        $task2->setUser($user2)
            ->setTitle('Wash my car')
            ->setContent('Like new!')
            ->setIsDone(1)
        ;
        $manager->persist($task2);

        $task3 = new Task();
        $task3->setUser($user3)
            ->setTitle('test 1')
            ->setContent('test')
            ->setIsDone(0)
        ;
        $manager->persist($task3);

        $task4 = new Task();
        $task4->setUser($user3)
            ->setTitle('test 2')
            ->setContent('test')
            ->setIsDone(0)
        ;
        $manager->persist($task4);

        $task5 = new Task();
        $task5->setUser($user3)
            ->setTitle('test 3')
            ->setContent('test')
            ->setIsDone(1)
        ;
        $manager->persist($task5);

        $task6 = new Task();
        $task6->setUser($user4)
            ->setTitle('test 1')
            ->setContent('test')
            ->setIsDone(0)
        ;
        $manager->persist($task6);

        $task7 = new Task();
        $task7->setUser($user4)
            ->setTitle('test 2')
            ->setContent('test')
            ->setIsDone(0)
        ;
        $manager->persist($task7);

        $task8 = new Task();
        $task8->setUser($user4)
            ->setTitle('test 3')
            ->setContent('test')
            ->setIsDone(1)
        ;
        $manager->persist($task8);

        $manager->flush();
    }
}
