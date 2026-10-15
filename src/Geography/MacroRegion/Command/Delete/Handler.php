<?php

declare(strict_types=1);

namespace App\Geography\MacroRegion\Command\Delete;

use App\Geography\MacroRegion\Entity\MacroRegionId;
use App\Geography\MacroRegion\Repository\MacroRegionRepository;

final readonly class Handler
{
    public function __construct(
        private MacroRegionRepository $repository,
    ) {
    }

    public function handle(Command $command): void
    {
        $this->repository->delete(MacroRegionId::fromValue($command->id));
    }
}
