<?php

declare(strict_types=1);

namespace App\Geography\District\Query\List;

use App\Auth\Entity\User;
use App\FlexConditions\Conditions\EmptyCondition;
use App\Geography\District\Entity\DistrictDto;
use App\Geography\District\Repository\DistrictRepository;

final readonly class Fetcher
{
    public function __construct(
        private DistrictRepository $repository,
    ) {
    }

    /** @return DistrictDto[] */
    public function fetch(User $user): array
    {
        return $this->repository->list(new EmptyCondition());
    }
}
