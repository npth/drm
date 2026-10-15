<?php

declare(strict_types=1);

namespace App\Geography\MacroRegion\Command\Update;

use App\Geography\MacroRegion\Entity\Code;
use App\Geography\MacroRegion\Entity\MacroRegion;
use App\Geography\MacroRegion\Entity\MacroRegionDto;
use App\Geography\MacroRegion\Entity\MacroRegionId;
use App\Geography\MacroRegion\Entity\Name;
use App\Geography\MacroRegion\Repository\MacroRegionRepository;

final readonly class Handler
{
    public function __construct(
        private MacroRegionRepository $repository
    ) {
    }

    public function handle(Command $command): MacroRegionDto
    {
        $macroRegionId = MacroRegionId::fromValue($command->id);
        if ($this->repository->hasRegionIds($command->regionIds, $macroRegionId)) {
            throw new \InvalidArgumentException(
                "Один или несколько регионов уже присутствуют в других макрорегионах."
            );
        }

        $macroRegion = new MacroRegion(
            $macroRegionId,
            Code::fromValue($command->code),
            Name::fromValue($command->name),
            $command->regionIds,
        );

        return $this->repository->update($macroRegion);
    }
}
