<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use Illuminate\Database\Capsule\Manager;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Throwable;

final readonly class TransactionMiddleware implements MiddlewareInterface
{
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        Manager::connection()->beginTransaction();
        try {
            $response = $handler->handle($request);
            Manager::connection()->commit();
            return $response;
        } catch (Throwable $exception) {
            Manager::connection()->rollBack();
            throw $exception;
        }
    }
}
