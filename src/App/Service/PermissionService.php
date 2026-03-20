<?php

declare(strict_types=1);

namespace App\Service;

use App\Enum\Permission;
use App\Exception\TokenNotFoundException;
use App\Repository\TokenRepositoryInterface;

class PermissionService implements PermissionServiceInterface
{
    public function __construct(
        private readonly TokenRepositoryInterface $tokenRepository
    ) {
    }

    public function hasPermission(string $tokenId, Permission $permission): bool
    {
        $token = $this->tokenRepository->findById($tokenId);

        if ($token === null) {
            throw new TokenNotFoundException($tokenId);
        }

        return $token->hasPermission($permission);
    }
}
