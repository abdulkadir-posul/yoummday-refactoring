<?php

declare(strict_types=1);

namespace App\Response;

use App\Exception\HttpException;
use ProgPhil1337\SimpleReactApp\HTTP\Response\JSONResponse;
use ProgPhil1337\SimpleReactApp\HTTP\Response\ResponseInterface;

class ApiResponse
{
    /**
     * @param array<string, mixed> $data
     */
    public static function success(array $data, int $statusCode = 200): ResponseInterface
    {
        return new JSONResponse([
            'success' => true,
            'data' => $data,
        ], $statusCode);
    }

    public static function error(string $message, string $errorCode, int $statusCode): ResponseInterface
    {
        return new JSONResponse([
            'success' => false,
            'error' => [
                'code' => $errorCode,
                'message' => $message,
            ],
        ], $statusCode);
    }

    public static function fromException(HttpException $exception): ResponseInterface
    {
        return self::error(
            $exception->getMessage(),
            $exception->getErrorCode(),
            $exception->getStatusCode()
        );
    }
}
