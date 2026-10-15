<?php

declare(strict_types=1);

namespace App\Geography\MacroRegion\Query\List;

use App\Auth\Entity\User;
use App\FlexConditions\Conditions\EmptyCondition;
use App\Geography\MacroRegion\Entity\MacroRegionDto;
use App\Geography\MacroRegion\Repository\MacroRegionRepository;

final readonly class Fetcher
{
    public function __construct(
        private MacroRegionRepository $repository,
    ) {
    }

    /** @return MacroRegionDto[] */
    public function fetch(User $user): array
    {
        return $this->repository->list(new EmptyCondition());
    }
}
