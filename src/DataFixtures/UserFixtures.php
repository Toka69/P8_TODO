<?php


namespace App\DataFixtures;


use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserFixtures extends Fixture
{
    public const USER1 = 'user1';
    public const USER2 = 'user2';
    public const USER3 = 'user3';
    public const USER4 = 'user4';

    private UserPasswordHasherInterface $passwordHasher;

    public function __construct(UserPasswordHasherInterface $passwordHasher)
    {
        $this->passwordHasher = $passwordHasher;
    }

    public function load(ObjectManager $manager)
    {
        $user1 = new User();
        $user1->setUsername('John')
            ->setRoles((array)'ROLE_USER')
            ->setPassword($this->passwordHasher->hashPassword(
                $user1,
                'test1'
            ))
        ;
        $manager->persist($user1);
        $this->addReference(self::USER1, $user1);

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
        $this->addReference(self::USER2, $user2);

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
        $this->addReference(self::USER3, $user3);

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
        $this->addReference(self::USER4, $user4);

        $manager->flush();
    }
}
