<?php

declare(strict_types=1);

namespace App\Geography\Region\Repository;

use App\Common\Event\AuditSaver;
use App\Geography\Region\Entity\Region;
use App\Geography\Region\Entity\RegionDto;
use App\Geography\Region\Entity\RegionId;
use App\FlexConditions\Contracts\ConditionInterface;

final readonly class RegionRepository
{
    public function __construct(
        private AuditSaver $saver,
    ) {
    }

    /** @return RegionDto[] */
    public function list(ConditionInterface $filter): array
    {
        $query = RegionModel::query();
        $filter->apply($query);
        return $query->get()
            ->map(static function (RegionModel $regionModel) {
                return $regionModel->toDto();
            })
            ->all();
    }

    public function insert(Region $region): RegionDto
    {
        $row = new RegionModel();
        return $this->save($row, $region);
    }

    public function update(Region $region): RegionDto
    {
        $row = RegionModel::query()->findOrFail($region->id->getValue());
        return $this->save($row, $region);
    }

    public function delete(RegionId $id): void
    {
        $row = RegionModel::query()->findOrFail($id->getValue());
        $this->saver->delete($row, 'region');
    }

    private function save(RegionModel $row, Region $region): RegionDto
    {
        $row->ordnum = $region->ordnum;
        $row->name = $region->name->getValue();
        $row->active = $region->active;
        $this->saver->save($row, 'region');
        return $row->toDto();
    }
}
