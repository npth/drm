<?php

declare(strict_types=1);

namespace App\Geography\District\Entity;

use App\Geography\Region\Entity\RegionId;

final readonly class District
{
    public function __construct(
        public DistrictId $id,
        public RegionId $regionId,
        public int $ordnum,
        public Name $name,
        public bool $active,
    ) {
    }
}
