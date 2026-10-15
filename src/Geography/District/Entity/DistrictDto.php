<?php

declare(strict_types=1);

namespace App\Geography\District\Entity;

final readonly class DistrictDto
{
    public function __construct(
        public int $id,
        public District $district,
        public ?\DateTimeImmutable $createdAt,
        public ?\DateTimeImmutable $updatedAt,
    ) {
    }
}
