<?php

declare(strict_types=1);

namespace App\Geography\Point\Command\Delete;

use App\Geography\Point\Entity\PointId;
use App\Geography\Point\Repository\PointRepository;

final readonly class Handler
{
    public function __construct(
        private PointRepository $repository,
    ) {
    }

    public function handle(Command $command): void
    {
        $this->repository->delete(PointId::fromValue($command->id));
    }
}
