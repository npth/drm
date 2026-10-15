<?php

declare(strict_types=1);

namespace App\Geography\Region\Entity;

final readonly class RegionDto
{
    public function __construct(
        public int $id,
        public Region $region,
        public ?\DateTimeImmutable $createdAt,
        public ?\DateTimeImmutable $updatedAt,
    ) {
    }
}
