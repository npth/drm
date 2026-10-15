<?php

declare(strict_types=1);

namespace App\Http\Action\Geography\Region;

use App\Geography\Region\Entity\RegionDto;

final readonly class Transformer
{
    /**
     * @param RegionDto[] $regions
     * @return array<object{id: int, name: string, note: ?string}>
     */
    public function transform(array $regions): array
    {
        return array_map(function (RegionDto $region) {
            return $this->transformRegion($region);
        }, $regions);
    }

    public function transformRegion(RegionDto $dto): array
    {
        return [
            'id' => $dto->id,
            'ordnum' => $dto->region->ordnum,
            'name' => $dto->region->name->getValue(),
            'active' => $dto->region->active,
            'created_at' => $dto->createdAt?->format('c'),
            'updated_at' => $dto->updatedAt?->format('c'),
        ];
    }
}
