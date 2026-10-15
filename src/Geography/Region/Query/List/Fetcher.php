<?php

declare(strict_types=1);

namespace App\Geography\Region\Query\List;

use App\Auth\Entity\User;
use App\FlexConditions\Conditions\EmptyCondition;
use App\Geography\Region\Entity\RegionDto;
use App\Geography\Region\Repository\RegionRepository;

final readonly class Fetcher
{
    public function __construct(
        private RegionRepository $repository,
    ) {
    }

    /** @return RegionDto[] */
    public function fetch(User $user): array
    {
        return $this->repository->list(new EmptyCondition());
    }
}
