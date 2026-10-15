<?php

declare(strict_types=1);

namespace App\Geography\MacroRegion\Entity;

final readonly class MacroRegion
{
    /**
     * @param int[] $regionIds Список ID регионов
     */
    public function __construct(
        public MacroRegionId $id,
        public Code $code,
        public Name $name,
        public array $regionIds,
    ) {
    }
}
