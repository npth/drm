<?php

declare(strict_types=1);

namespace App\Http\Action\Geography\Point;

use App\Geography\Point\Command\Insert\Command;
use App\Geography\Point\Command\Insert\Handler;
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
            $this->transformer->transformPoint(
                $this->handler->handle(
                    new Command(
                        (int)Arr::get($body, 'district_id'),
                        (int)Arr::get($body, 'ordnum'),
                        (string)Arr::get($body, 'name'),
                        (bool)Arr::get($body, 'active'),
                    )
                )
            )
        );
    }
}
