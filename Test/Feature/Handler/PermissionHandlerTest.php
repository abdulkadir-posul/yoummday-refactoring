<?php

declare(strict_types=1);

namespace Test\Feature\Handler;

use App\Enum\Permission;
use App\Exception\TokenNotFoundException;
use App\Handler\PermissionHandler;
use App\Service\PermissionServiceInterface;
use PHPUnit\Framework\TestCase;
use ProgPhil1337\SimpleReactApp\HTTP\Routing\RouteParameters;
use Psr\Http\Message\ServerRequestInterface;

class PermissionHandlerTest extends TestCase
{
    private PermissionServiceInterface $permissionService;
    private ServerRequestInterface $request;

    protected function setUp(): void
    {
        $this->permissionService = $this->createMock(PermissionServiceInterface::class);
        $this->request = $this->createMock(ServerRequestInterface::class);
    }

    private function createHandler(): PermissionHandler
    {
        return new PermissionHandler($this->permissionService);
    }

    private function invokeHandler(string $token, string $permission): array
    {
        $handler = $this->createHandler();
        $params = new RouteParameters(['token' => $token, 'permission' => $permission]);

        $response = $handler($this->request, $params);

        return [
            'code' => $response->getCode(),
            'body' => json_decode($response->getContent(), true),
        ];
    }

    public function testTokenWithPermissionReturns200WithTrue(): void
    {
        $this->permissionService
            ->method('hasPermission')
            ->with('token1234', Permission::Read)
            ->willReturn(true);

        $result = $this->invokeHandler('token1234', 'read');

        $this->assertSame(200, $result['code']);
        $this->assertTrue($result['body']['success']);
        $this->assertTrue($result['body']['data']['has_permission']);
    }

    public function testTokenWithoutPermissionReturns200WithFalse(): void
    {
        $this->permissionService
            ->method('hasPermission')
            ->with('tokenReadonly', Permission::Write)
            ->willReturn(false);

        $result = $this->invokeHandler('tokenReadonly', 'write');

        $this->assertSame(200, $result['code']);
        $this->assertTrue($result['body']['success']);
        $this->assertFalse($result['body']['data']['has_permission']);
    }

    public function testNonExistentTokenReturns404(): void
    {
        $this->permissionService
            ->method('hasPermission')
            ->willThrowException(new TokenNotFoundException('unknown'));

        $result = $this->invokeHandler('unknown', 'read');

        $this->assertSame(404, $result['code']);
        $this->assertFalse($result['body']['success']);
        $this->assertSame('TOKEN_NOT_FOUND', $result['body']['error']['code']);
    }

    public function testInvalidPermissionReturns422(): void
    {
        $result = $this->invokeHandler('token1234', 'delete');

        $this->assertSame(422, $result['code']);
        $this->assertFalse($result['body']['success']);
        $this->assertSame('INVALID_PERMISSION', $result['body']['error']['code']);
    }
}
