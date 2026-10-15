<?php

declare(strict_types=1);

namespace App\Http\Action\Geography\District;

use App\Geography\District\Command\Insert\Command;
use App\Geography\District\Command\Insert\Handler;
use App\Http\Response\JsonResponse;
use Illuminate\Support\Arr;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

final readonly class Insert implements RequestHandlerInterface
{
    public function __construct(
        private Handler $handler,
        private Transformer $transformer,
    ) {
    }

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $body = $request->getParsedBody();
        return new JsonResponse(
            $this->transformer->transformDistrict(
                $this->handler->handle(
                    new Command(
                        (int)Arr::get($body, 'region_id'),
                        (int)Arr::get($body, 'ordnum'),
                        (string)Arr::get($body, 'name'),
                        (bool)Arr::get($body, 'active'),
                    )
                )
            )
        );
    }
}
