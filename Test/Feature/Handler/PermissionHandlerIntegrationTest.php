<?php

declare(strict_types=1);

namespace Test\Feature\Handler;

use App\Handler\PermissionHandler;
use App\Provider\TokenDataProvider;
use App\Repository\InMemoryTokenRepository;
use App\Service\PermissionService;
use PHPUnit\Framework\TestCase;
use ProgPhil1337\SimpleReactApp\HTTP\Routing\RouteParameters;
use Psr\Http\Message\ServerRequestInterface;

class PermissionHandlerIntegrationTest extends TestCase
{
    private PermissionHandler $handler;
    private ServerRequestInterface $request;

    protected function setUp(): void
    {
        $tokenDataProvider = new TokenDataProvider();
        $tokenRepository = new InMemoryTokenRepository($tokenDataProvider);
        $permissionService = new PermissionService($tokenRepository);

        $this->handler = new PermissionHandler($permissionService);
        $this->request = $this->createMock(ServerRequestInterface::class);
    }

    private function invoke(string $token, string $permission): array
    {
        $params = new RouteParameters(['token' => $token, 'permission' => $permission]);
        $response = ($this->handler)($this->request, $params);

        return [
            'code' => $response->getCode(),
            'body' => json_decode($response->getContent(), true),
        ];
    }

    public function testToken1234HasReadPermission(): void
    {
        $result = $this->invoke('token1234', 'read');

        $this->assertSame(200, $result['code']);
        $this->assertTrue($result['body']['success']);
        $this->assertTrue($result['body']['data']['has_permission']);
    }

    public function testToken1234HasWritePermission(): void
    {
        $result = $this->invoke('token1234', 'write');

        $this->assertSame(200, $result['code']);
        $this->assertTrue($result['body']['success']);
        $this->assertTrue($result['body']['data']['has_permission']);
    }

    public function testTokenReadonlyHasReadPermission(): void
    {
        $result = $this->invoke('tokenReadonly', 'read');

        $this->assertSame(200, $result['code']);
        $this->assertTrue($result['body']['success']);
        $this->assertTrue($result['body']['data']['has_permission']);
    }

    public function testTokenReadonlyDoesNotHaveWritePermission(): void
    {
        $result = $this->invoke('tokenReadonly', 'write');

        $this->assertSame(200, $result['code']);
        $this->assertTrue($result['body']['success']);
        $this->assertFalse($result['body']['data']['has_permission']);
    }

    public function testNonExistentTokenReturns404(): void
    {
        $result = $this->invoke('nonExistentToken', 'read');

        $this->assertSame(404, $result['code']);
        $this->assertFalse($result['body']['success']);
        $this->assertSame('TOKEN_NOT_FOUND', $result['body']['error']['code']);
    }

    public function testInvalidPermissionReturns422(): void
    {
        $result = $this->invoke('token1234', 'admin');

        $this->assertSame(422, $result['code']);
        $this->assertFalse($result['body']['success']);
        $this->assertSame('INVALID_PERMISSION', $result['body']['error']['code']);
    }

    public function testEmptyPermissionReturns422(): void
    {
        $result = $this->invoke('token1234', '');

        $this->assertSame(422, $result['code']);
        $this->assertSame('INVALID_PERMISSION', $result['body']['error']['code']);
    }
}
