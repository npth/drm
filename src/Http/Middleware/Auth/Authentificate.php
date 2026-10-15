<?php

declare(strict_types=1);

namespace App\Http\Middleware\Auth;

use App\Auth\Entity\User;
use App\Auth\Query\FindUserById\Fetcher;
use App\Auth\Query\FindUserById\Query;
use League\OAuth2\Server\Exception\OAuthServerException;
use League\OAuth2\Server\ResourceServer;
use LogicException;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

class Authentificate implements MiddlewareInterface
{
    private const string ATTRIBUTE = 'identity';

    private Fetcher $userFetcher;

    /** @psalm-api */
    public function __construct(
        Fetcher $userFetcher,
        private ResourceServer $server,
        private ResponseFactoryInterface $response
    ) {
        $this->userFetcher = $userFetcher;
    }

    public static function identity(ServerRequestInterface $request): ?User
    {
        $identity = $request->getAttribute(self::ATTRIBUTE);

        if ($identity !== null && !$identity instanceof User) {
            throw new LogicException('Invalid identity.');
        }

        return $identity;
    }

    public static function getCurrentUser(ServerRequestInterface $request): User
    {
        $identity = $request->getAttribute(self::ATTRIBUTE);

        if (!$identity instanceof User) {
            throw new \DomainException('Пользователь не аутентифицирован');
        }

        return $identity;
    }

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        if (!$request->hasHeader('authorization')) {
            return $handler->handle($request);
        }

        try {
            $request = $this->server->validateAuthenticatedRequest($request);
        } catch (OAuthServerException $exception) {
            return $exception->generateHttpResponse($this->response->createResponse());
        }

        $user = $this->userFetcher->fetch(new Query((int)$request->getAttribute('oauth_user_id')));

        if ($user === null) {
            throw new OAuthServerException('Пользователь не найден.', 101, 'invalid_user', 401);
        }
        // создание идентификатора

        // все дальнейшие действия с атрибутом

        return $handler->handle(
            $request->withAttribute(self::ATTRIBUTE, $user)
            ->withAttribute('oauth_session_id', 1111)
        );
    }
}
