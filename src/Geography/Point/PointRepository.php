<?php

declare(strict_types=1);

namespace App\Geography\Point;

use PDO;

/**
 * Репозиторий для работы с населёнными пунктами
 */
class PointRepository
{
    private PDO $db;

    public function __construct(PDO $db)
    {
        $this->db = $db;
    }

    /**
     * Получить все населённые пункты
     *
     * @param array $filters Фильтры (district_id, type, search)
     * @param int $limit
     * @param int $offset
     * @return Point[]
     */
    public function findAll(array $filters = [], int $limit = 50, int $offset = 0): array
    {
        $sql = 'SELECT * FROM points WHERE 1=1';
        $params = [];

        if (!empty($filters['district_id'])) {
            $sql .= ' AND district_id = :district_id';
            $params[':district_id'] = $filters['district_id'];
        }

        if (!empty($filters['type'])) {
            $sql .= ' AND type = :type';
            $params[':type'] = $filters['type'];
        }

        if (!empty($filters['search'])) {
            $sql .= ' AND (name ILIKE :search OR code ILIKE :search)';
            $params[':search'] = '%' . $filters['search'] . '%';
        }

        $sql .= ' ORDER BY name ASC LIMIT :limit OFFSET :offset';

        $stmt = $this->db->prepare($sql);
        foreach ($params as $key => $value) {
            $stmt->bindValue($key, $value);
        }
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();

        $points = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $points[] = Point::fromArray($row);
        }

        return $points;
    }

    /**
     * Найти населённый пункт по ID
     */
    public function findById(string $id): ?Point
    {
        $stmt = $this->db->prepare('SELECT * FROM points WHERE id = :id');
        $stmt->execute([':id' => $id]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        return $row ? Point::fromArray($row) : null;
    }

    /**
     * Создать населённый пункт
     */
    public function create(Point $point): Point
    {
        $sql = 'INSERT INTO points (id, name, code, district_id, type, description, created_at, updated_at)
                VALUES (:id, :name, :code, :district_id, :type, :description, :created_at, :updated_at)
                RETURNING *';

        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            ':id' => $point->getId() ?? $this->generateUuid(),
            ':name' => $point->getName(),
            ':code' => $point->getCode(),
            ':district_id' => $point->getDistrictId(),
            ':type' => $point->getType(),
            ':description' => $point->getDescription(),
            ':created_at' => $point->getCreatedAt(),
            ':updated_at' => $point->getUpdatedAt(),
        ]);

        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return Point::fromArray($row);
    }

    /**
     * Обновить населённый пункт
     */
    public function update(Point $point): ?Point
    {
        $sql = 'UPDATE points
                SET name = :name,
                    code = :code,
                    district_id = :district_id,
                    type = :type,
                    description = :description,
                    updated_at = :updated_at
                WHERE id = :id
                RETURNING *';

        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            ':id' => $point->getId(),
            ':name' => $point->getName(),
            ':code' => $point->getCode(),
            ':district_id' => $point->getDistrictId(),
            ':type' => $point->getType(),
            ':description' => $point->getDescription(),
            ':updated_at' => date('Y-m-d H:i:s'),
        ]);

        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row ? Point::fromArray($row) : null;
    }

    /**
     * Удалить населённый пункт
     */
    public function delete(string $id): bool
    {
        $stmt = $this->db->prepare('DELETE FROM points WHERE id = :id');
        return $stmt->execute([':id' => $id]);
    }

    /**
     * Подсчитать количество записей
     */
    public function count(array $filters = []): int
    {
        $sql = 'SELECT COUNT(*) FROM points WHERE 1=1';
        $params = [];

        if (!empty($filters['district_id'])) {
            $sql .= ' AND district_id = :district_id';
            $params[':district_id'] = $filters['district_id'];
        }

        if (!empty($filters['type'])) {
            $sql .= ' AND type = :type';
            $params[':type'] = $filters['type'];
        }

        if (!empty($filters['search'])) {
            $sql .= ' AND (name ILIKE :search OR code ILIKE :search)';
            $params[':search'] = '%' . $filters['search'] . '%';
        }

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);

        return (int) $stmt->fetchColumn();
    }

    /**
     * Проверить существование по коду
     */
    public function existsByCode(string $code, ?string $excludeId = null): bool
    {
        $sql = 'SELECT COUNT(*) FROM points WHERE code = :code';
        $params = [':code' => $code];

        if ($excludeId) {
            $sql .= ' AND id != :exclude_id';
            $params[':exclude_id'] = $excludeId;
        }

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);

        return (int) $stmt->fetchColumn() > 0;
    }

    /**
     * Генерация UUID v4
     */
    private function generateUuid(): string
    {
        return sprintf(
            '%04x%04x-%04x-%04x-%04x-%04x%04x%04x',
            mt_rand(0, 0xffff), mt_rand(0, 0xffff),
            mt_rand(0, 0xffff),
            mt_rand(0, 0x0fff) | 0x4000,
            mt_rand(0, 0x3fff) | 0x8000,
            mt_rand(0, 0xffff), mt_rand(0, 0xffff), mt_rand(0, 0xffff)
        );
    }
}
