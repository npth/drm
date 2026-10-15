<?php

declare(strict_types=1);

namespace App\Geography\MacroRegion\Entity;

final readonly class MacroRegionDto
{
    /**
     * @param int[] $regionIds
     */
    public function __construct(
        public int $id,
        public MacroRegion $macroRegion,
        public ?\DateTimeImmutable $createdAt,
        public ?\DateTimeImmutable $updatedAt,
    ) {
    }
}
