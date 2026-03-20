<?php

declare(strict_types=1);

namespace Test\Unit\Handler;

use App\Exception\HttpException;
use App\Exception\TokenNotFoundException;
use App\Handler\ApiHandler;
use PHPUnit\Framework\TestCase;
use ProgPhil1337\SimpleReactApp\HTTP\Response\ResponseInterface;
use ProgPhil1337\SimpleReactApp\HTTP\Routing\RouteParameters;
use Psr\Http\Message\ServerRequestInterface;

class ApiHandlerTest extends TestCase
{
    private ServerRequestInterface $request;
    private RouteParameters $params;

    protected function setUp(): void
    {
        $this->request = $this->createMock(ServerRequestInterface::class);
        $this->params = new RouteParameters([]);
    }

    private function createHandler(callable $callback): ApiHandler
    {
        return new class($callback) extends ApiHandler {
            /** @var callable */
            private $callback;

            public function __construct(callable $callback)
            {
                $this->callback = $callback;
            }

            protected function handle(ServerRequestInterface $request, RouteParameters $parameters): ResponseInterface
            {
                return ($this->callback)($request, $parameters);
            }
        };
    }

    public function testReturnsResponseFromHandle(): void
    {
        $expectedResponse = $this->createMock(ResponseInterface::class);

        $handler = $this->createHandler(fn() => $expectedResponse);
        $response = $handler($this->request, $this->params);

        $this->assertSame($expectedResponse, $response);
    }

    public function testCatchesHttpExceptionAndReturnsErrorResponse(): void
    {
        $handler = $this->createHandler(function () {
            throw new TokenNotFoundException('abc');
        });

        $response = $handler($this->request, $this->params);

        $this->assertSame(404, $response->getCode());
        $body = json_decode($response->getContent(), true);
        $this->assertFalse($body['success']);
        $this->assertSame('TOKEN_NOT_FOUND', $body['error']['code']);
    }

    public function testCatchesGenericHttpExceptionAndReturnsErrorResponse(): void
    {
        $handler = $this->createHandler(function () {
            throw new HttpException('Something went wrong', 422, 'VALIDATION_ERROR');
        });

        $response = $handler($this->request, $this->params);

        $this->assertSame(422, $response->getCode());
        $body = json_decode($response->getContent(), true);
        $this->assertFalse($body['success']);
        $this->assertSame('VALIDATION_ERROR', $body['error']['code']);
        $this->assertSame('Something went wrong', $body['error']['message']);
    }

    public function testCatchesThrowableAndReturns500(): void
    {
        $handler = $this->createHandler(function () {
            throw new \RuntimeException('Unexpected error');
        });

        $response = $handler($this->request, $this->params);

        $this->assertSame(500, $response->getCode());
        $body = json_decode($response->getContent(), true);
        $this->assertFalse($body['success']);
        $this->assertSame('INTERNAL_ERROR', $body['error']['code']);
        $this->assertSame('Internal Server Error', $body['error']['message']);
    }
}
