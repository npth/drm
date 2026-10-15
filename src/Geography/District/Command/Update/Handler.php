<?php

declare(strict_types=1);

namespace App\Geography\District\Command\Update;

use App\Geography\District\Entity\Name;
use App\Geography\District\Entity\District;
use App\Geography\District\Entity\DistrictDto;
use App\Geography\District\Entity\DistrictId;
use App\Geography\District\Repository\DistrictRepository;
use App\Geography\Region\Entity\RegionId;

final readonly class Handler
{
    public function __construct(
        private DistrictRepository $repository
    ) {
    }

    public function handle(Command $command): DistrictDto
    {
        $district = new District(
            DistrictId::fromValue($command->id),
            RegionId::fromValue($command->regionId),
            $command->ordnum,
            Name::fromValue($command->name),
            $command->active,
        );
        return $this->repository->update($district);
    }
}
