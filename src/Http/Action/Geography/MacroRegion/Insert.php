<?php

declare(strict_types=1);

namespace App\Http\Action\Geography\MacroRegion;

use App\Geography\MacroRegion\Command\Insert\Command;
use App\Geography\MacroRegion\Command\Insert\Handler;
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
            $this->transformer->transformMacroRegion(
                $this->handler->handle(
                    new Command(
                        (string)Arr::get($body, 'code'),
                        (string)Arr::get($body, 'name'),
                        (array)Arr::get($body, 'regions'),
                    )
                )
            )
        );
    }
}
