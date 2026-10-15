<?php

declare(strict_types=1);

namespace App\Geography\Region\Command\Delete;

use App\Geography\Region\Entity\RegionId;
use App\Geography\Region\Repository\RegionRepository;

final readonly class Handler
{
    public function __construct(
        private RegionRepository $repository,
    ) {
    }

    public function handle(Command $command): void
    {
        $this->repository->delete(RegionId::fromValue($command->id));
    }
}
