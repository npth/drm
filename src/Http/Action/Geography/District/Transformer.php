<?php

declare(strict_types=1);

namespace App\Http\Action\Geography\District;

use App\Geography\District\Entity\DistrictDto;

final readonly class Transformer
{
    /**
     * @param DistrictDto[] $districts
     * @return array<object{id: int, name: string, note: ?string}>
     */
    public function transform(array $districts): array
    {
        return array_map(function (DistrictDto $district) {
            return $this->transformDistrict($district);
        }, $districts);
    }

    public function transformDistrict(DistrictDto $dto): array
    {
        return [
            'id' => $dto->id,
            'region_id' => $dto->district->regionId->getValue(),
            'ordnum' => $dto->district->ordnum,
            'name' => $dto->district->name->getValue(),
            'active' => $dto->district->active,
            'created_at' => $dto->createdAt?->format('c'),
            'updated_at' => $dto->updatedAt?->format('c'),
        ];
    }
}
