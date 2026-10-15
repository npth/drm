<?php

declare(strict_types=1);

namespace App\Geography\District\Command\Delete;

/** @codeCoverageIgnore  */
final readonly class Command
{
    public function __construct(
        public int $id
    ) {
    }
}
