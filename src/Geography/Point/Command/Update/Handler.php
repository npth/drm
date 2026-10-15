<?php

declare(strict_types=1);

namespace App\Geography\Point\Command\Update;

use App\Geography\Point\Entity\Name;
use App\Geography\Point\Entity\Point;
use App\Geography\Point\Entity\PointDto;
use App\Geography\Point\Entity\PointId;
use App\Geography\Point\Repository\PointRepository;
use App\Geography\District\Entity\DistrictId;

final readonly class Handler
{
    public function __construct(
        private PointRepository $repository
    ) {
    }

    public function handle(Command $command): PointDto
    {
        $point = new Point(
            PointId::fromValue($command->id),
            DistrictId::fromValue($command->districtId),
            $command->ordnum,
            Name::fromValue($command->name),
            $command->active,
        );
        return $this->repository->update($point);
    }
}
