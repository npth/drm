<?php

declare(strict_types=1);

namespace App\Geography\District\Repository;

use App\Common\Event\AuditSaver;
use App\Geography\District\Entity\District;
use App\Geography\District\Entity\DistrictDto;
use App\Geography\District\Entity\DistrictId;
use App\FlexConditions\Contracts\ConditionInterface;

final readonly class DistrictRepository
{
    public function __construct(
        private AuditSaver $saver,
    ) {
    }

    /** @return DistrictDto[] */
    public function list(ConditionInterface $filter): array
    {
        $query = DistrictModel::query();
        $filter->apply($query);
        return $query->get()
            ->map(static function (DistrictModel $districtModel) {
                return $districtModel->toDto();
            })
            ->all();
    }

    public function insert(District $district): DistrictDto
    {
        $row = new DistrictModel();
        return $this->save($row, $district);
    }

    public function update(District $district): DistrictDto
    {
        $row = DistrictModel::query()->findOrFail($district->id->getValue());
        return $this->save($row, $district);
    }

    public function delete(DistrictId $id): void
    {
        $row = DistrictModel::query()->findOrFail($id->getValue());
        $this->saver->delete($row, 'district');
    }

    private function save(DistrictModel $row, District $district): DistrictDto
    {
        $row->region_id = $district->regionId->getValue();
        $row->ordnum = $district->ordnum;
        $row->name = $district->name->getValue();
        $row->active = $district->active;
        $this->saver->save($row, 'district');
        return $row->toDto();
    }
}
