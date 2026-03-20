<?php

declare(strict_types=1);

namespace Test\Unit\Exception;

use App\Exception\TokenNotFoundException;
use PHPUnit\Framework\TestCase;

class TokenNotFoundExceptionTest extends TestCase
{
    public function testExceptionMessage(): void
    {
        $exception = new TokenNotFoundException('abc123');

        $this->assertSame('Token "abc123" not found', $exception->getMessage());
        $this->assertSame(404, $exception->getStatusCode());
        $this->assertSame('TOKEN_NOT_FOUND', $exception->getErrorCode());
    }
}
