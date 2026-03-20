<?php

declare(strict_types=1);

namespace Test\Unit\Enum;

use App\Enum\Permission;
use App\Exception\InvalidPermissionException;
use PHPUnit\Framework\TestCase;

class PermissionTest extends TestCase
{
    public function testReadCase(): void
    {
        $permission = Permission::Read;
        $this->assertSame('read', $permission->value);
    }

    public function testWriteCase(): void
    {
        $permission = Permission::Write;
        $this->assertSame('write', $permission->value);
    }

    public function testTryFromValidValue(): void
    {
        $this->assertSame(Permission::Read, Permission::tryFrom('read'));
        $this->assertSame(Permission::Write, Permission::tryFrom('write'));
    }

    public function testTryFromInvalidValueReturnsNull(): void
    {
        $this->assertNull(Permission::tryFrom('delete'));
        $this->assertNull(Permission::tryFrom(''));
        $this->assertNull(Permission::tryFrom('READ'));
    }

    public function testFromStringWithValidValues(): void
    {
        $this->assertSame(Permission::Read, Permission::fromString('read'));
        $this->assertSame(Permission::Write, Permission::fromString('write'));
    }

    public function testFromStringWithInvalidValueThrowsException(): void
    {
        $this->expectException(InvalidPermissionException::class);
        Permission::fromString('delete');
    }

    public function testFromStringWithEmptyValueThrowsException(): void
    {
        $this->expectException(InvalidPermissionException::class);
        Permission::fromString('');
    }

    public function testFromStringIsCaseSensitive(): void
    {
        $this->expectException(InvalidPermissionException::class);
        Permission::fromString('READ');
    }
}
