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

        $manager->flush();
    }
}
