<?php

declare(strict_types=1);

namespace Tests\Unit\Models;

use App\Models\User;
use PHPUnit\Framework\TestCase;

final class UserTest extends TestCase
{
    private function user(string $role): User
    {
        return new User(1, 'Martin', 'Bob', 'bob@klaxon.test', '0600000002', $role, 'hash');
    }

    public function testFullNameCombinesFirstAndLastName(): void
    {
        $this->assertSame('Bob Martin', $this->user('utilisateur')->fullName());
    }

    public function testOnlyAdminRoleIsAdmin(): void
    {
        $this->assertTrue($this->user('admin')->isAdmin());
        $this->assertFalse($this->user('utilisateur')->isAdmin());
    }
}
