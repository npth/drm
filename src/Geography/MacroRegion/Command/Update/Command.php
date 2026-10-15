<?php

declare(strict_types=1);

namespace App\Geography\MacroRegion\Command\Update;

use Webmozart\Assert\Assert;

/** @codeCoverageIgnore */
final readonly class Command
{
    /**
     * @param int[] $regionIds
     */
    public function __construct(
        public int $id,
        public string $code,
        public string $name,
        public array $regionIds,
    ) {
        Assert::notEmpty($this->regionIds);
    }
}
