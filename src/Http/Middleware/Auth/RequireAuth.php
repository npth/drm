<?php

declare(strict_types=1);

namespace App\Http\Middleware\Auth;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

final class RequireAuth implements MiddlewareInterface
{
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $identity = Authentificate::identity($request);

        if ($identity === null) {
            throw new NotAuthentificated('Необходимо пройти аутентификацию');
        }

        return $handler->handle($request);
    }
}
