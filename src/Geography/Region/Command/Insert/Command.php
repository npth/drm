<?php

declare(strict_types=1);

namespace App\Geography\Region\Command\Insert;

/** @codeCoverageIgnore  */
final readonly class Command
{
    public function __construct(
        public int $ordnum,
        public string $name,
        public bool $active,
    ) {
    }
}
