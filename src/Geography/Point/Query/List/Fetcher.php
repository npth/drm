<?php

declare(strict_types=1);

namespace App\Geography\Point\Query\List;

use App\Auth\Entity\User;
use App\FlexConditions\Conditions\EmptyCondition;
use App\Geography\Point\Entity\PointDto;
use App\Geography\Point\Repository\PointRepository;

final readonly class Fetcher
{
    public function __construct(
        private PointRepository $repository,
    ) {
    }

    /** @return PointDto[] */
    public function fetch(User $user): array
    {
        return $this->repository->list(new EmptyCondition());
    }
}
