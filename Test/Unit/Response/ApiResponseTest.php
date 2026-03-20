<?php

declare(strict_types=1);

namespace Test\Unit\Response;

use App\Exception\HttpException;
use App\Response\ApiResponse;
use PHPUnit\Framework\TestCase;

class ApiResponseTest extends TestCase
{
    public function testSuccessResponse(): void
    {
        $response = ApiResponse::success(['has_permission' => true]);

        $this->assertSame(200, $response->getCode());

        $body = json_decode($response->getContent(), true);
        $this->assertTrue($body['success']);
        $this->assertTrue($body['data']['has_permission']);
    }

    public function testSuccessResponseWithCustomStatusCode(): void
    {
        $response = ApiResponse::success(['id' => 1], 201);

        $this->assertSame(201, $response->getCode());
    }

    public function testErrorResponse(): void
    {
        $response = ApiResponse::error('Not found', 'TOKEN_NOT_FOUND', 404);

        $this->assertSame(404, $response->getCode());

        $body = json_decode($response->getContent(), true);
        $this->assertFalse($body['success']);
        $this->assertSame('TOKEN_NOT_FOUND', $body['error']['code']);
        $this->assertSame('Not found', $body['error']['message']);
    }

    public function testFromException(): void
    {
        $exception = new HttpException('Server error', 500, 'INTERNAL_ERROR');
        $response = ApiResponse::fromException($exception);

        $this->assertSame(500, $response->getCode());

        $body = json_decode($response->getContent(), true);
        $this->assertFalse($body['success']);
        $this->assertSame('INTERNAL_ERROR', $body['error']['code']);
    }
}
