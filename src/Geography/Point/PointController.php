<?php

declare(strict_types=1);

namespace App\Geography\Point;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use InvalidArgumentException;

/**
 * Контроллер API для населённых пунктов
 */
class PointController
{
    private PointService $pointService;

    public function __construct(PointService $pointService)
    {
        $this->pointService = $pointService;
    }

    /**
     * GET /api/geography/points
     * Получить список населённых пунктов
     */
    public function index(Request $request, Response $response): Response
    {
        $params = $request->getQueryParams();

        $filters = [];
        if (!empty($params['district_id'])) {
            $filters['district_id'] = $params['district_id'];
        }
        if (!empty($params['type'])) {
            $filters['type'] = $params['type'];
        }
        if (!empty($params['search'])) {
            $filters['search'] = $params['search'];
        }

        $page = max(1, (int) ($params['page'] ?? 1));
        $perPage = min(100, max(1, (int) ($params['per_page'] ?? 50)));

        $result = $this->pointService->getList($filters, $page, $perPage);

        $payload = json_encode($result, JSON_UNESCAPED_UNICODE);
        $response->getBody()->write($payload);

        return $response
            ->withHeader('Content-Type', 'application/json; charset=utf-8')
            ->withStatus(200);
    }

    /**
     * GET /api/geography/points/{id}
     * Получить населённый пункт по ID
     */
    public function show(Request $request, Response $response, array $args): Response
    {
        try {
            $id = $args['id'];
            $point = $this->pointService->getById($id);

            $payload = json_encode(['data' => $point], JSON_UNESCAPED_UNICODE);
            $response->getBody()->write($payload);

            return $response
                ->withHeader('Content-Type', 'application/json; charset=utf-8')
                ->withStatus(200);
        } catch (\RuntimeException $e) {
            return $this->errorResponse($response, $e->getMessage(), $e->getCode() ?: 404);
        }
    }

    /**
     * POST /api/geography/points
     * Создать населённый пункт
     */
    public function store(Request $request, Response $response): Response
    {
        try {
            $body = $request->getParsedBody();
            $data = $body ?? json_decode((string) $request->getBody(), true);

            if (!is_array($data)) {
                return $this->errorResponse($response, 'Некорректное тело запроса', 400);
            }

            $point = $this->pointService->create($data);

            $payload = json_encode(['data' => $point, 'message' => 'Населённый пункт создан'], JSON_UNESCAPED_UNICODE);
            $response->getBody()->write($payload);

            return $response
                ->withHeader('Content-Type', 'application/json; charset=utf-8')
                ->withStatus(201);
        } catch (InvalidArgumentException $e) {
            return $this->errorResponse($response, $e->getMessage(), $e->getCode() ?: 422);
        } catch (\RuntimeException $e) {
            return $this->errorResponse($response, $e->getMessage(), $e->getCode() ?: 500);
        }
    }

    /**
     * PUT /api/geography/points/{id}
     * Обновить населённый пункт
     */
    public function update(Request $request, Response $response, array $args): Response
    {
        try {
            $id = $args['id'];
            $body = $request->getParsedBody();
            $data = $body ?? json_decode((string) $request->getBody(), true);

            if (!is_array($data)) {
                return $this->errorResponse($response, 'Некорректное тело запроса', 400);
            }

            $point = $this->pointService->update($id, $data);

            $payload = json_encode(['data' => $point, 'message' => 'Населённый пункт обновлён'], JSON_UNESCAPED_UNICODE);
            $response->getBody()->write($payload);

            return $response
                ->withHeader('Content-Type', 'application/json; charset=utf-8')
                ->withStatus(200);
        } catch (InvalidArgumentException $e) {
            return $this->errorResponse($response, $e->getMessage(), $e->getCode() ?: 422);
        } catch (\RuntimeException $e) {
            return $this->errorResponse($response, $e->getMessage(), $e->getCode() ?: 404);
        }
    }

    /**
     * DELETE /api/geography/points/{id}
     * Удалить населённый пункт
     */
    public function destroy(Request $request, Response $response, array $args): Response
    {
        try {
            $id = $args['id'];
            $this->pointService->delete($id);

            $payload = json_encode(['message' => 'Населённый пункт удалён'], JSON_UNESCAPED_UNICODE);
            $response->getBody()->write($payload);

            return $response
                ->withHeader('Content-Type', 'application/json; charset=utf-8')
                ->withStatus(200);
        } catch (\RuntimeException $e) {
            return $this->errorResponse($response, $e->getMessage(), $e->getCode() ?: 404);
        }
    }

    /**
     * Формирование error-ответа
     */
    private function errorResponse(Response $response, string $message, int $status): Response
    {
        $payload = json_encode(['error' => $message], JSON_UNESCAPED_UNICODE);
        $response->getBody()->write($payload);

        return $response
            ->withHeader('Content-Type', 'application/json; charset=utf-8')
            ->withStatus($status);
    }
}
