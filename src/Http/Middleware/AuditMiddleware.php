<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use App\Common\Event\AuditDispatcher;
use App\Http\Middleware\Auth\Authentificate;
use App\Log\Log\Entity\Log;
use App\Log\Log\Repository\LogRepository;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

readonly class AuditMiddleware implements MiddlewareInterface
{
    public function __construct(
        private AuditDispatcher $dispatcher,
        private LogRepository $logRepository,
    ) {
    }

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $response = $handler->handle($request);

        $identity = Authentificate::identity($request);

        $events = $this->dispatcher->flush();
        foreach ($events as $event) {
            try {
                $this->logRepository->insert(Log::fromEvent('u', $identity?->getId()?->getValue(), $event));
            } catch (\Exception $e) {
                error_log("Failed to save audit log: " . $e->getMessage());
            }
        }
        return $response;
    }
}
