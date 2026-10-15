<?php

declare(strict_types=1);

namespace App\Http\Action\Geography\Point;

use App\Geography\Point\Entity\PointDto;

final readonly class Transformer
{
    /**
     * @param PointDto[] $points
     * @return array<object{id: int, name: string, note: ?string}>
     */
    public function transform(array $points): array
    {
        return array_map(function (PointDto $point) {
            return $this->transformPoint($point);
        }, $points);
    }

    public function transformPoint(PointDto $dto): array
    {
        return [
            'id' => $dto->id,
            'district_id' => $dto->point->districtId->getValue(),
            'ordnum' => $dto->point->ordnum,
            'name' => $dto->point->name->getValue(),
            'active' => $dto->point->active,
            'created_at' => $dto->createdAt?->format('c'),
            'updated_at' => $dto->updatedAt?->format('c'),
        ];
    }
}
