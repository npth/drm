<?php

declare(strict_types=1);

namespace App\Geography\Point\Command\Update;

/** @codeCoverageIgnore  */
final readonly class Command
{
    public function __construct(
        public int $id,
        public int $districtId,
        public int $ordnum,
        public string $name,
        public bool $active,
    ) {
    }
}
