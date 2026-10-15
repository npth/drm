<?php

declare(strict_types=1);

namespace App\Http\Action\Geography\Point;

use App\Geography\Point\Query\List\Fetcher;
use App\Http\Middleware\Auth\Authentificate;
use App\Http\Response\JsonResponse;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

final readonly class Listing implements RequestHandlerInterface
{
    public function __construct(
        private Fetcher $fetcher,
        private Transformer $transformer,
    ) {
    }

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $identity = Authentificate::identity($request);
        return new JsonResponse([
            'data' => $this->transformer->transform($this->fetcher->fetch($identity))
        ]);
    }
}
