<?php

declare(strict_types=1);

namespace App\Http\Action\Geography\MacroRegion;

use App\Geography\MacroRegion\Entity\MacroRegionDto;

final readonly class Transformer
{
    /**
     * @param MacroRegionDto[] $macroRegions
     * @return array<object{id: int, name: string, note: ?string}>
     */
    public function transform(array $macroRegions): array
    {
        return array_map(function (MacroRegionDto $macroRegion) {
            return $this->transformMacroRegion($macroRegion);
        }, $macroRegions);
    }

    public function transformMacroRegion(MacroRegionDto $dto): array
    {
        return [
            'id' => $dto->id,
            'name' => $dto->macroRegion->name->getValue(),
            'code' => $dto->macroRegion->code->getValue(),
            'regions' => $dto->macroRegion->regionIds,
            'created_at' => $dto->createdAt?->format('c'),
            'updated_at' => $dto->updatedAt?->format('c'),
        ];
    }
}
