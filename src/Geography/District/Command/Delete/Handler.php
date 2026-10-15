<?php

declare(strict_types=1);

namespace App\Geography\District\Command\Delete;

use App\Geography\District\Entity\DistrictId;
use App\Geography\District\Repository\DistrictRepository;

final readonly class Handler
{
    public function __construct(
        private DistrictRepository $repository,
    ) {
    }

    public function handle(Command $command): void
    {
        $this->repository->delete(DistrictId::fromValue($command->id));
    }
}
