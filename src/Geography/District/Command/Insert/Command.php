<?php

declare(strict_types=1);

namespace App\Geography\District\Command\Insert;

use Webmozart\Assert\Assert;

/** @codeCoverageIgnore  */
final readonly class Command
{
    public function __construct(
        public int $regionId,
        public int $ordnum,
        public string $name,
        public bool $active,
    ) {
        Assert::greaterThan($this->regionId, 0, 'Регион не задан');
        Assert::greaterThan($this->ordnum, 0, 'Порядковый номер сортировки должен быть больше 0');
        Assert::notEmpty($this->name, 'Наименование не задано');
        Assert::maxLength($this->name, 255, 'Наименование длиннее 255 символов');
    }
}
