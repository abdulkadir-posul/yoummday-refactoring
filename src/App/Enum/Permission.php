<?php

declare(strict_types=1);

namespace App\Enum;

enum Permission: string
{
    case Read = 'read';
    case Write = 'write';

    public static function fromString(string $value): self
    {
        return self::tryFrom($value) ?? throw new \App\Exception\InvalidPermissionException($value);
    }
}
