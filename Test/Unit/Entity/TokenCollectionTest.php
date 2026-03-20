<?php

declare(strict_types=1);

namespace Test\Unit\Entity;

use App\Entity\Token;
use App\Entity\TokenCollection;
use App\Enum\Permission;
use PHPUnit\Framework\TestCase;

class TokenCollectionTest extends TestCase
{
    private TokenCollection $collection;

    protected function setUp(): void
    {
        $this->collection = new TokenCollection([
            new Token('token1234', [Permission::Read, Permission::Write]),
            new Token('tokenReadonly', [Permission::Read]),
        ]);
    }

    public function testFindByIdReturnsToken(): void
    {
        $token = $this->collection->findById('token1234');

        $this->assertNotNull($token);
        $this->assertSame('token1234', $token->getId());
    }

    public function testFindByIdReturnsNullForNonExistent(): void
    {
        $this->assertNull($this->collection->findById('unknown'));
    }

    public function testIsIterable(): void
    {
        $ids = [];
        foreach ($this->collection as $token) {
            $ids[] = $token->getId();
        }

        $this->assertSame(['token1234', 'tokenReadonly'], $ids);
    }

    public function testCount(): void
    {
        $this->assertCount(2, $this->collection);
    }

    public function testEmptyCollection(): void
    {
        $empty = new TokenCollection([]);

        $this->assertCount(0, $empty);
        $this->assertNull($empty->findById('any'));
    }
}
