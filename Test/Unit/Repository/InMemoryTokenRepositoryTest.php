<?php

declare(strict_types=1);

namespace Test\Unit\Repository;

use App\Entity\Token;
use App\Entity\TokenCollection;
use App\Enum\Permission;
use App\Provider\TokenDataProvider;
use App\Repository\InMemoryTokenRepository;
use PHPUnit\Framework\TestCase;

class InMemoryTokenRepositoryTest extends TestCase
{
    private InMemoryTokenRepository $repository;

    protected function setUp(): void
    {
        $provider = $this->createMock(TokenDataProvider::class);
        $provider->method('getTokens')->willReturn([
            ['token' => 'token1234', 'permissions' => ['read', 'write']],
            ['token' => 'tokenReadonly', 'permissions' => ['read']],
        ]);

        $this->repository = new InMemoryTokenRepository($provider);
    }

    public function testFindByIdReturnsTokenEntity(): void
    {
        $token = $this->repository->findById('token1234');

        $this->assertInstanceOf(Token::class, $token);
        $this->assertSame('token1234', $token->getId());
        $this->assertTrue($token->hasPermission(Permission::Read));
        $this->assertTrue($token->hasPermission(Permission::Write));
    }

    public function testFindByIdReturnsReadonlyToken(): void
    {
        $token = $this->repository->findById('tokenReadonly');

        $this->assertInstanceOf(Token::class, $token);
        $this->assertTrue($token->hasPermission(Permission::Read));
        $this->assertFalse($token->hasPermission(Permission::Write));
    }

    public function testFindByIdReturnsNullForNonExistent(): void
    {
        $this->assertNull($this->repository->findById('unknown'));
    }

    public function testFindAllReturnsTokenCollection(): void
    {
        $collection = $this->repository->findAll();

        $this->assertInstanceOf(TokenCollection::class, $collection);
        $this->assertCount(2, $collection);
    }

    public function testMapsPermissionStringsToEnums(): void
    {
        $token = $this->repository->findById('token1234');

        $permissions = $token->getPermissions();
        $this->assertContainsOnlyInstancesOf(Permission::class, $permissions);
        $this->assertSame([Permission::Read, Permission::Write], $permissions);
    }

    public function testIgnoresInvalidPermissionsInData(): void
    {
        $provider = $this->createMock(TokenDataProvider::class);
        $provider->method('getTokens')->willReturn([
            ['token' => 'tokenBad', 'permissions' => ['read', 'delete', 'write']],
        ]);

        $repository = new InMemoryTokenRepository($provider);
        $token = $repository->findById('tokenBad');

        $this->assertSame([Permission::Read, Permission::Write], $token->getPermissions());
    }
}
