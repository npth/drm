<?php

declare(strict_types=1);

namespace App\Geography\Region\Command\Update;

use App\Geography\Region\Entity\Name;
use App\Geography\Region\Entity\Region;
use App\Geography\Region\Entity\RegionDto;
use App\Geography\Region\Entity\RegionId;
use App\Geography\Region\Repository\RegionRepository;

final readonly class Handler
{
    public function __construct(
        private RegionRepository $repository
    ) {
    }

    public function handle(Command $command): RegionDto
    {
        $region = new Region(
            RegionId::fromValue($command->id),
            $command->ordnum,
            Name::fromValue($command->name),
            $command->active,
        );
        return $this->repository->update($region);
    }
}
