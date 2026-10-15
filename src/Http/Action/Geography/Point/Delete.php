<?php

declare(strict_types=1);

namespace App\Http\Action\Geography\Point;

use App\Geography\Point\Command\Delete\Command;
use App\Geography\Point\Command\Delete\Handler;
use App\Http\Response\EmptyResponse;
use Illuminate\Support\Arr;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

final class Delete implements RequestHandlerInterface
{
    public function __construct(
        private Handler $handler
    ) {
    }

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $body = $request->getParsedBody();
        $this->handler->handle(
            new Command(
                (int)Arr::get($body, 'id')
            )
        );
        return new EmptyResponse();
    }
}
