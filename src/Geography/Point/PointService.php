<?php

declare(strict_types=1);

namespace App\Geography\Point;

use App\Geography\District\DistrictRepository;
use InvalidArgumentException;

/**
 * Сервис для бизнес-логики населённых пунктов
 */
class PointService
{
    private PointRepository $pointRepository;
    private DistrictRepository $districtRepository;

    public function __construct(
        PointRepository $pointRepository,
        DistrictRepository $districtRepository
    ) {
        $this->pointRepository = $pointRepository;
        $this->districtRepository = $districtRepository;
    }

    /**
     * Получить список населённых пунктов с фильтрацией и пагинацией
     *
     * @param array $filters
     * @param int $page
     * @param int $perPage
     * @return array
     */
    public function getList(array $filters = [], int $page = 1, int $perPage = 50): array
    {
        $offset = ($page - 1) * $perPage;
        $points = $this->pointRepository->findAll($filters, $perPage, $offset);
        $total = $this->pointRepository->count($filters);

        return [
            'data' => array_map(fn(Point $point) => $point->toArray(), $points),
            'pagination' => [
                'total' => $total,
                'page' => $page,
                'per_page' => $perPage,
                'total_pages' => (int) ceil($total / $perPage),
            ],
        ];
    }

    /**
     * Получить населённый пункт по ID
     */
    public function getById(string $id): array
    {
        $point = $this->pointRepository->findById($id);

        if (!$point) {
            throw new \RuntimeException('Населённый пункт не найден', 404);
        }

        return $point->toArray();
    }

    /**
     * Создать населённый пункт
     */
    public function create(array $data): array
    {
        $this->validate($data);

        // Проверка существования района
        $district = $this->districtRepository->findById($data['district_id']);
        if (!$district) {
            throw new InvalidArgumentException('Район не найден: ' . $data['district_id'], 422);
        }

        // Проверка уникальности кода
        if ($this->pointRepository->existsByCode($data['code'])) {
            throw new InvalidArgumentException('Населённый пункт с таким кодом уже существует', 422);
        }

        $point = new Point(
            null,
            $data['name'],
            $data['code'],
            $data['district_id'],
            $data['type'],
            $data['description'] ?? null
        );

        $created = $this->pointRepository->create($point);

        return $created->toArray();
    }

    /**
     * Обновить населённый пункт
     */
    public function update(string $id, array $data): array
    {
        $existing = $this->pointRepository->findById($id);
        if (!$existing) {
            throw new \RuntimeException('Населённый пункт не найден', 404);
        }

        $this->validate($data, $id);

        // Проверка существования района
        if (isset($data['district_id'])) {
            $district = $this->districtRepository->findById($data['district_id']);
            if (!$district) {
                throw new InvalidArgumentException('Район не найден: ' . $data['district_id'], 422);
            }
        }

        // Проверка уникальности кода
        if (isset($data['code']) && $this->pointRepository->existsByCode($data['code'], $id)) {
            throw new InvalidArgumentException('Населённый пункт с таким кодом уже существует', 422);
        }

        $point = new Point(
            $id,
            $data['name'] ?? $existing->getName(),
            $data['code'] ?? $existing->getCode(),
            $data['district_id'] ?? $existing->getDistrictId(),
            $data['type'] ?? $existing->getType(),
            $data['description'] ?? $existing->getDescription(),
            $existing->getCreatedAt(),
            null
        );

        $updated = $this->pointRepository->update($point);

        return $updated->toArray();
    }

    /**
     * Удалить населённый пункт
     */
    public function delete(string $id): bool
    {
        $existing = $this->pointRepository->findById($id);
        if (!$existing) {
            throw new \RuntimeException('Населённый пункт не найден', 404);
        }

        return $this->pointRepository->delete($id);
    }

    /**
     * Валидация данных
     */
    private function validate(array $data, ?string $excludeId = null): void
    {
        $errors = [];

        if (empty($data['name'])) {
            $errors[] = 'Поле "name" обязательно';
        }

        if (empty($data['code'])) {
            $errors[] = 'Поле "code" обязательно';
        }

        if (empty($data['district_id'])) {
            $errors[] = 'Поле "district_id" обязательно';
        }

        if (empty($data['type'])) {
            $errors[] = 'Поле "type" обязательно';
        } else {
            $allowedTypes = ['city', 'town', 'village', 'settlement'];
            if (!in_array($data['type'], $allowedTypes, true)) {
                $errors[] = 'Недопустимое значение type. Допустимые: ' . implode(', ', $allowedTypes);
            }
        }

        if (!empty($errors)) {
            throw new InvalidArgumentException(implode('; ', $errors), 422);
        }
    }
}
