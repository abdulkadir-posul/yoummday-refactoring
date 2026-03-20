<?php

declare(strict_types=1);

namespace Test\Unit\Exception;

use App\Exception\InvalidPermissionException;
use PHPUnit\Framework\TestCase;

class InvalidPermissionExceptionTest extends TestCase
{
    public function testExceptionMessageIncludesValidValues(): void
    {
        $exception = new InvalidPermissionException('delete');

        $this->assertSame(
            'Invalid permission "delete". Valid values: read, write',
            $exception->getMessage()
        );
        $this->assertSame(422, $exception->getStatusCode());
        $this->assertSame('INVALID_PERMISSION', $exception->getErrorCode());
    }
}
