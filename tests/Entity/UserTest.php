<?php

namespace App\Tests\Entity;

use App\Entity\User;
use PHPUnit\Framework\TestCase;

class UserTest extends TestCase
{
    public function testUser(): void
    {
        $entity = new User('test');
        self::assertSame('test', $entity->getUsername());
        self::assertSame('', $entity->getPassword());
        self::assertNull($entity->getSalt());
        self::assertNotContains('ROLE_ADMIN', $entity->getRoles());
        $entity->setUsername('admin');
        self::assertSame('admin', $entity->getUsername());
        self::assertContains('ROLE_ADMIN', $entity->getRoles());
        $entity->eraseCredentials();
    }
}
