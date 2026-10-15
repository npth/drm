<?php

declare(strict_types=1);

namespace App\Geography\Point\Command\Insert;

use Webmozart\Assert\Assert;

/** @codeCoverageIgnore  */
final readonly class Command
{
    public function __construct(
        public int $districtId,
        public int $ordnum,
        public string $name,
        public bool $active,
    ) {
        Assert::greaterThan($this->districtId, 0, 'ID района должен быть больше 0');
        Assert::greaterThan($this->ordnum, 0, 'Порядковый номер должен быть больше 0');
        Assert::notEmpty($this->name, 'Название не может быть пустым');
        Assert::maxLength($this->name, 255, 'Название не должно превышать 255 символов');
    }
}
