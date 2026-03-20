<?php

declare(strict_types=1);

namespace App\Exception;

class TokenNotFoundException extends HttpException
{
    public function __construct(string $token)
    {
        parent::__construct(
            sprintf('Token "%s" not found', $token),
            404,
            'TOKEN_NOT_FOUND'
        );
    }
}
