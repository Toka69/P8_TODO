<?php

namespace App\Tests\Unit\Entity;

use App\Entity\User;
use PHPUnit\Framework\TestCase;

/**
 * Class UserTest
 * @package App\Tests\Unit\Entity
 */
class UserTest extends TestCase
{
    private const USER_USERNAME = 'user';

    private const USER_EMAIL = 'user@test.com';

    private const USER_PASSWORD = 'test';

    private const USER_ROLES = ['ROLE_USER'];

    /**
     * Test User entity getters and setters.
     *
     * @return void
     */
    public function testGetterSetter(): void
    {
        $user = new User();

        $this->assertInstanceOf(User::class, $user);
        $this->assertEquals(null, $user->getId());
        $this->assertEquals(null, $user->getUserIdentifier());
        $this->assertEquals(null, $user->getEmail());
        $this->assertEquals(['ROLE_USER'], $user->getRoles());

        $user->setUsername(self::USER_USERNAME);
        $this->assertEquals(self::USER_USERNAME, $user->getUserIdentifier());
        $user->setEmail(self::USER_EMAIL);
        $this->assertEquals(self::USER_EMAIL, $user->getEmail());
        $user->setPassword(self::USER_PASSWORD);
        $this->assertEquals(self::USER_PASSWORD, $user->getPassword());
        $user->setRoles(self::USER_ROLES);
        $this->assertEquals(self::USER_ROLES, $user->getRoles());
    }
}
