<?php

declare(strict_types=1);

namespace App\Http\Action;

use App\Access\Entity\Environment;
use App\Http\Response\JsonResponse;
use BcMath\Number;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

final class TestAction implements RequestHandlerInterface
{
    public function __construct()
    {
    }

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        ini_set('date.timezone', 'Europe/Moscow');


        return new JsonResponse([
            'environment' => Environment::default(),
            'test' => new Number('12.1234'),
        ]);
    }
}
