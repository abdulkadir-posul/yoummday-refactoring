<?php

declare(strict_types=1);

namespace Test\Unit\Service;

use App\Entity\Token;
use App\Enum\Permission;
use App\Exception\TokenNotFoundException;
use App\Repository\TokenRepositoryInterface;
use App\Service\PermissionService;
use PHPUnit\Framework\TestCase;

class PermissionServiceTest extends TestCase
{
    private PermissionService $service;
    private TokenRepositoryInterface $repository;

    protected function setUp(): void
    {
        $this->repository = $this->createMock(TokenRepositoryInterface::class);
        $this->service = new PermissionService($this->repository);
    }

    public function testTokenWithReadPermissionReturnsTrue(): void
    {
        $token = new Token('token1234', [Permission::Read, Permission::Write]);
        $this->repository->method('findById')->with('token1234')->willReturn($token);

        $this->assertTrue($this->service->hasPermission('token1234', Permission::Read));
    }

    public function testTokenWithWritePermissionReturnsTrue(): void
    {
        $token = new Token('token1234', [Permission::Read, Permission::Write]);
        $this->repository->method('findById')->with('token1234')->willReturn($token);

        $this->assertTrue($this->service->hasPermission('token1234', Permission::Write));
    }

    public function testReadonlyTokenWithReadPermissionReturnsTrue(): void
    {
        $token = new Token('tokenReadonly', [Permission::Read]);
        $this->repository->method('findById')->with('tokenReadonly')->willReturn($token);

        $this->assertTrue($this->service->hasPermission('tokenReadonly', Permission::Read));
    }

    public function testReadonlyTokenWithWritePermissionReturnsFalse(): void
    {
        $token = new Token('tokenReadonly', [Permission::Read]);
        $this->repository->method('findById')->with('tokenReadonly')->willReturn($token);

        $this->assertFalse($this->service->hasPermission('tokenReadonly', Permission::Write));
    }

    public function testNonExistentTokenThrowsException(): void
    {
        $this->repository->method('findById')->with('invalidToken')->willReturn(null);

        $this->expectException(TokenNotFoundException::class);
        $this->service->hasPermission('invalidToken', Permission::Read);
    }
}
