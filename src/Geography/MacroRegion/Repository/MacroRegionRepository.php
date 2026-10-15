<?php

declare(strict_types=1);

namespace App\Geography\MacroRegion\Repository;

use App\Common\Event\AuditSaver;
use App\Geography\MacroRegion\Entity\MacroRegion;
use App\Geography\MacroRegion\Entity\MacroRegionDto;
use App\Geography\MacroRegion\Entity\MacroRegionId;
use App\FlexConditions\Contracts\ConditionInterface;
use Illuminate\Database\Eloquent\Builder;

final readonly class MacroRegionRepository
{
    public function __construct(
        private AuditSaver $saver,
    ) {
    }

    /** @return MacroRegionDto[] */
    public function list(ConditionInterface $filter): array
    {
        $query = MacroRegionModel::query();
        $filter->apply($query);

        return $query->get()
            ->map(static function (MacroRegionModel $model) {
                return $model->toDto();
            })
            ->all();
    }

    public function hasRegionIds(array $regionIds, ?MacroRegionId $excludeMacroRegionId = null): bool
    {
        if (empty($regionIds)) {
            return false;
        }

        $query = MacroRegionModel::query();

        $query->where(function (Builder $q) use ($regionIds) {
            foreach ($regionIds as $index => $regionId) {
                if ($index === 0) {
                    $q->whereJsonContains('regions', $regionId);
                } else {
                    $q->orWhereJsonContains('regions', $regionId);
                }
            }
        });

        if (!is_null($excludeMacroRegionId)) {
            $query->where('id', '!=', $excludeMacroRegionId->getValue());
        }

        return (bool)$query->first('id');
    }

    public function insert(MacroRegion $macroRegion): MacroRegionDto
    {
        $row = new MacroRegionModel();
        return $this->save($row, $macroRegion);
    }

    public function update(MacroRegion $macroRegion): MacroRegionDto
    {
        $row = MacroRegionModel::query()->findOrFail($macroRegion->id->getValue());
        return $this->save($row, $macroRegion);
    }

    public function delete(MacroRegionId $id): void
    {
        $row = MacroRegionModel::query()->findOrFail($id->getValue());
        $this->saver->delete($row, 'macro_region');
    }

    private function save(MacroRegionModel $row, MacroRegion $macroRegion): MacroRegionDto
    {
        $row->code = $macroRegion->code->getValue();
        $row->name = $macroRegion->name->getValue();
        $row->regions = $macroRegion->regionIds;

        $this->saver->save($row, 'macro_region');

        return $row->toDto();
    }
}
