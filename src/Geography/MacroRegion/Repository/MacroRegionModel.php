<?php

declare(strict_types=1);

namespace App\Geography\MacroRegion\Repository;

use App\Geography\MacroRegion\Entity\Code;
use App\Geography\MacroRegion\Entity\MacroRegion;
use App\Geography\MacroRegion\Entity\MacroRegionDto;
use App\Geography\MacroRegion\Entity\MacroRegionId;
use App\Geography\MacroRegion\Entity\Name;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property string $code
 * @property string $name
 * @property int[] $regions
 * @property Carbon $created_at
 * @property ?Carbon $updated_at
 * @codeCoverageIgnore
 */
final class MacroRegionModel extends Model
{
    protected $connection = 'default';
    protected $table = 'geography.macroregions';

    protected $casts = [
        'regions' => 'array',
    ];

    public function toEntity(): MacroRegion
    {
        return new MacroRegion(
            MacroRegionId::fromValue($this->id),
            Code::fromValue($this->code),
            Name::fromValue($this->name),
            $this->regions ?? [],
        );
    }

    public function toDto(): MacroRegionDto
    {
        return new MacroRegionDto(
            $this->id,
            $this->toEntity(),
            $this->created_at?->toDateTimeImmutable(),
            $this->updated_at?->toDateTimeImmutable(),
        );
    }
}
