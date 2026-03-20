<?php

declare(strict_types=1);

namespace App\Handler;

use App\Enum\Permission;
use App\Response\ApiResponse;
use App\Service\PermissionServiceInterface;
use ProgPhil1337\SimpleReactApp\HTTP\Response\ResponseInterface;
use ProgPhil1337\SimpleReactApp\HTTP\Routing\Attribute\Route;
use ProgPhil1337\SimpleReactApp\HTTP\Routing\HttpMethod;
use ProgPhil1337\SimpleReactApp\HTTP\Routing\RouteParameters;
use Psr\Http\Message\ServerRequestInterface;

#[Route(httpMethod: HttpMethod::GET, uri: '/tokens/{token}/permissions/{permission}')]
class PermissionHandler extends ApiHandler
{
    public function __construct(
        private readonly PermissionServiceInterface $permissionService
    ) {
    }

    protected function handle(ServerRequestInterface $serverRequest, RouteParameters $parameters): ResponseInterface
    {
        /** @var string $tokenId */
        $tokenId = $parameters->get('token');
        /** @var string $permissionParam */
        $permissionParam = $parameters->get('permission');

        $permission = Permission::fromString($permissionParam);
        $hasPermission = $this->permissionService->hasPermission($tokenId, $permission);

        return ApiResponse::success(['has_permission' => $hasPermission]);
    }
}
