<?php

declare(strict_types=1);

namespace App\Geography\Point\Repository;

use App\Geography\Point\Entity\Name;
use App\Geography\Point\Entity\Point;
use App\Geography\Point\Entity\PointDto;
use App\Geography\Point\Entity\PointId;
use App\Geography\District\Entity\DistrictId;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property int $district_id
 * @property int $ordnum
 * @property string $name
 * @property bool $active
 * @property Carbon $created_at
 * @property ?Carbon $updated_at
 * @codeCoverageIgnore
 */
final class PointModel extends Model
{
    protected $connection = 'default';
    protected $table = 'geography.points';

    public function toEntity(): Point
    {
        return new Point(
            PointId::fromValue($this->id),
            DistrictId::fromValue($this->district_id),
            $this->ordnum,
            Name::fromValue($this->name),
            $this->active,
        );
    }

    public function toDto(): PointDto
    {
        return new PointDto(
            $this->id,
            $this->toEntity(),
            $this->created_at?->toDateTimeImmutable(),
            $this->updated_at?->toDateTimeImmutable(),
        );
    }
}
