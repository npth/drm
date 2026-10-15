<?php

declare(strict_types=1);

namespace App\Geography\Point\Entity;

use App\Geography\District\Entity\DistrictId;

final readonly class Point
{
    public function __construct(
        public PointId $id,
        public DistrictId $districtId,
        public int $ordnum,
        public Name $name,
        public bool $active,
    ) {
    }
}
