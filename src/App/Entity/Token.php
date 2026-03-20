<?php

declare(strict_types=1);

namespace App\Entity;

use App\Enum\Permission;

class Token
{
    /**
     * @param Permission[] $permissions
     */
    public function __construct(
        private readonly string $id,
        private readonly array $permissions
    ) {
    }

    public function getId(): string
    {
        return $this->id;
    }

    /**
     * @return Permission[]
     */
    public function getPermissions(): array
    {
        return $this->permissions;
    }

    public function hasPermission(Permission $permission): bool
    {
        return in_array($permission, $this->permissions, true);
    }
}
