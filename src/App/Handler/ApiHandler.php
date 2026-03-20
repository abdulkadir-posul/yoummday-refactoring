<?php

declare(strict_types=1);

namespace App\Handler;

use App\Exception\HttpException;
use App\Response\ApiResponse;
use ProgPhil1337\SimpleReactApp\HTTP\Response\ResponseInterface;
use ProgPhil1337\SimpleReactApp\HTTP\Routing\Handler\HandlerInterface;
use ProgPhil1337\SimpleReactApp\HTTP\Routing\RouteParameters;
use Psr\Http\Message\ServerRequestInterface;

abstract class ApiHandler implements HandlerInterface
{
    public function __invoke(ServerRequestInterface $serverRequest, RouteParameters $parameters): ResponseInterface
    {
        try {
            return $this->handle($serverRequest, $parameters);
        } catch (HttpException $e) {
            return ApiResponse::fromException($e);
        } catch (\Throwable) {
            return ApiResponse::error('Internal Server Error', 'INTERNAL_ERROR', 500);
        }
    }

    abstract protected function handle(ServerRequestInterface $serverRequest, RouteParameters $parameters): ResponseInterface;
}
