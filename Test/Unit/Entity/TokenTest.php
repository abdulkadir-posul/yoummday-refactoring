<?php

declare(strict_types=1);

namespace Test\Unit\Entity;

use App\Entity\Token;
use App\Enum\Permission;
use PHPUnit\Framework\TestCase;

class TokenTest extends TestCase
{
    public function testGetId(): void
    {
        $token = new Token('token1234', [Permission::Read, Permission::Write]);

        $this->assertSame('token1234', $token->getId());
    }

    public function testGetPermissions(): void
    {
        $permissions = [Permission::Read, Permission::Write];
        $token = new Token('token1234', $permissions);

        $this->assertSame($permissions, $token->getPermissions());
    }

    public function testHasPermissionReturnsTrue(): void
    {
        $token = new Token('token1234', [Permission::Read, Permission::Write]);

        $this->assertTrue($token->hasPermission(Permission::Read));
        $this->assertTrue($token->hasPermission(Permission::Write));
    }

    public function testHasPermissionReturnsFalse(): void
    {
        $token = new Token('tokenReadonly', [Permission::Read]);

        $this->assertFalse($token->hasPermission(Permission::Write));
    }

    public function testTokenWithNoPermissions(): void
    {
        $token = new Token('tokenEmpty', []);

        $this->assertFalse($token->hasPermission(Permission::Read));
        $this->assertFalse($token->hasPermission(Permission::Write));
    }
}
