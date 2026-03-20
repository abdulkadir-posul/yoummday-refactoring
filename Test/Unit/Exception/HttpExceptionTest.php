<?php

declare(strict_types=1);

namespace Test\Unit\Exception;

use App\Exception\HttpException;
use PHPUnit\Framework\TestCase;

class HttpExceptionTest extends TestCase
{
    public function testExceptionCarriesStatusCodeAndErrorCode(): void
    {
        $exception = new HttpException('Something went wrong', 500, 'INTERNAL_ERROR');

        $this->assertSame('Something went wrong', $exception->getMessage());
        $this->assertSame(500, $exception->getStatusCode());
        $this->assertSame('INTERNAL_ERROR', $exception->getErrorCode());
    }
}
