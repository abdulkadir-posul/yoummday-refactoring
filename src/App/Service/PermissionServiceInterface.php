<?php

declare(strict_types=1);

namespace App\Service;

use App\Enum\Permission;

interface PermissionServiceInterface
{
    public function hasPermission(string $tokenId, Permission $permission): bool;
}
