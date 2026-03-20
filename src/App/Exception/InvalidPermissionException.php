<?php

declare(strict_types=1);

namespace App\Exception;

use App\Enum\Permission;

class InvalidPermissionException extends HttpException
{
    public function __construct(string $permission)
    {
        $validValues = implode(', ', array_map(
            fn(Permission $p) => $p->value,
            Permission::cases()
        ));

        parent::__construct(
            sprintf('Invalid permission "%s". Valid values: %s', $permission, $validValues),
            422,
            'INVALID_PERMISSION'
        );
    }
}
