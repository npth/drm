<?php

declare(strict_types=1);

namespace App\Geography\Point\Entity;

final readonly class PointDto
{
    public function __construct(
        public int $id,
        public Point $point,
        public ?\DateTimeImmutable $createdAt,
        public ?\DateTimeImmutable $updatedAt,
    ) {
    }
}
