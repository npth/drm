<?php

declare(strict_types=1);

namespace App\Geography\District\Repository;

use App\Geography\District\Entity\Name;
use App\Geography\District\Entity\District;
use App\Geography\District\Entity\DistrictDto;
use App\Geography\District\Entity\DistrictId;
use App\Geography\Region\Entity\RegionId;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property int $region_id
 * @property int $ordnum
 * @property string $name
 * @property bool $active
 * @property Carbon $created_at
 * @property ?Carbon $updated_at
 * @codeCoverageIgnore
 */
final class DistrictModel extends Model
{
    protected $connection = 'default';
    protected $table = 'geography.districts';

    public function toEntity(): District
    {
        return new District(
            DistrictId::fromValue($this->id),
            RegionId::fromValue($this->region_id),
            $this->ordnum,
            Name::fromValue($this->name),
            $this->active,
        );
    }

    public function toDto(): DistrictDto
    {
        return new DistrictDto(
            $this->id,
            $this->toEntity(),
            $this->created_at?->toDateTimeImmutable(),
            $this->updated_at?->toDateTimeImmutable(),
        );
    }
}
