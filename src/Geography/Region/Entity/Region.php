<?php

declare(strict_types=1);

namespace App\Geography\Region\Entity;

final readonly class Region
{
    public function __construct(
        public RegionId $id,
        public int $ordnum,
        public Name $name,
        public bool $active,
    ) {
    }
}
