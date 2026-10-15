<?php

declare(strict_types=1);

namespace App\Http\Action;

use App\Auth\Query\FindIdByCredentials\Fetcher;
use App\Auth\Query\FindIdByCredentials\Query;
use App\Common\Event\AuditDispatcher;
use App\Common\Event\AuditEvent;
use App\Http\Response\HtmlResponse;
use App\OAuth\Entity\User;
use eftec\bladeone\BladeOne;
use League\OAuth2\Server\AuthorizationServer;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

final readonly class Authorize implements RequestHandlerInterface
{
    public function __construct(
        private AuthorizationServer $server,
        private BladeOne $template,
        private Fetcher $usersFetcher,
        private ResponseFactoryInterface $response,
        private AuditDispatcher $auditDispatcher,
    ) {
    }

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        /**
         * @var array{
         *     state?: ?string,
         *     provider?: ?string
         * } $params
         */
        $params = $request->getQueryParams();

        $authRequest = $this->server->validateAuthorizationRequest($request);

        if ($request->getMethod() === 'POST') {
            /**
             * @var array{
             *     email?: ?string,
             *     password?: ?string
             * } $body
             */
            $body = $request->getParsedBody();

            $user = $this->usersFetcher->fetch(new Query($body['email'] ?? '', $body['password'] ?? ''));

            if ($user === null) {
                $this->auditDispatcher->collect(new AuditEvent(
                    'login',
                    'authorize',
                    $body['email'] ?? '',
                    'Не удалось осуществить вход',
                ));
                return new HtmlResponse(
                    $this->template->run('authorize', ['error' => 'Не удалось осуществить вход']),
                    400
                );
            }

            if (!$user->isActive) {
                $this->auditDispatcher->collect(new AuditEvent(
                    'login',
                    'authorize',
                    $body['email'] ?? '',
                    'Учетная запись не активна',
                ));
                return new HtmlResponse(
                    $this->template->run('authorize', ['error' => 'Учетная запись не активна']),
                    409
                );
            }

            $authRequest->setUser(new User((string)$user->id));
            $authRequest->setAuthorizationApproved(true);

            $this->auditDispatcher->collect(new AuditEvent(
                'login',
                'authorize',
                $body['email'] ?? '',
                'Успешный вход',
            ));

            return $this->server->completeAuthorizationRequest($authRequest, $this->response->createResponse());
        }

        return new HtmlResponse($this->template->run('authorize'));
    }
}
