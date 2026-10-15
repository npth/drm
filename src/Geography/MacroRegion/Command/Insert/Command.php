<?php

declare(strict_types=1);

namespace App\Geography\MacroRegion\Command\Insert;

use Webmozart\Assert\Assert;

/** @codeCoverageIgnore */
final readonly class Command
{
    /**
     * @param int[] $regionIds
     */
    public function __construct(
        public string $code,
        public string $name,
        public array $regionIds,
    ) {
        Assert::notEmpty($this->regionIds);
    }
}
