<?php

declare(strict_types=1);

namespace App\Geography\Region\Repository;

use App\Geography\Region\Entity\Name;
use App\Geography\Region\Entity\Region;
use App\Geography\Region\Entity\RegionDto;
use App\Geography\Region\Entity\RegionId;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property int $ordnum
 * @property string $name
 * @property bool $active
 * @property Carbon $created_at
 * @property ?Carbon $updated_at
 * @codeCoverageIgnore
 */
final class RegionModel extends Model
{
    protected $connection = 'default';
    protected $table = 'geography.regions';

    public function toEntity(): Region
    {
        return new Region(
            RegionId::fromValue($this->id),
            $this->ordnum,
            Name::fromValue($this->name),
            $this->active,
        );
    }

    public function toDto(): RegionDto
    {
        return new RegionDto(
            $this->id,
            $this->toEntity(),
            $this->created_at?->toDateTimeImmutable(),
            $this->updated_at?->toDateTimeImmutable(),
        );
    }
}
