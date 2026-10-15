<?php

declare(strict_types=1);

namespace App\Geography\Point;

/**
 * Сущность "Населённый пункт"
 */
class Point
{
    private ?string $id;
    private string $name;
    private string $code;
    private string $district_id;
    private string $type;
    private ?string $description;
    private string $created_at;
    private string $updated_at;

    public function __construct(
        ?string $id,
        string $name,
        string $code,
        string $district_id,
        string $type,
        ?string $description = null,
        ?string $created_at = null,
        ?string $updated_at = null
    ) {
        $this->id = $id;
        $this->name = $name;
        $this->code = $code;
        $this->district_id = $district_id;
        $this->type = $type;
        $this->description = $description;
        $this->created_at = $created_at ?? date('Y-m-d H:i:s');
        $this->updated_at = $updated_at ?? date('Y-m-d H:i:s');
    }

    public function getId(): ?string
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getCode(): string
    {
        return $this->code;
    }

    public function getDistrictId(): string
    {
        return $this->district_id;
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function getCreatedAt(): string
    {
        return $this->created_at;
    }

    public function getUpdatedAt(): string
    {
        return $this->updated_at;
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'code' => $this->code,
            'district_id' => $this->district_id,
            'type' => $this->type,
            'description' => $this->description,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }

    public static function fromArray(array $data): self
    {
        return new self(
            $data['id'] ?? null,
            $data['name'],
            $data['code'],
            $data['district_id'],
            $data['type'],
            $data['description'] ?? null,
            $data['created_at'] ?? null,
            $data['updated_at'] ?? null
        );
    }
}
