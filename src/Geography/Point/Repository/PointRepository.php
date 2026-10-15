<?php

declare(strict_types=1);

namespace App\Geography\Point\Repository;

use App\Common\Event\AuditSaver;
use App\Geography\Point\Entity\Point;
use App\Geography\Point\Entity\PointDto;
use App\Geography\Point\Entity\PointId;
use App\FlexConditions\Contracts\ConditionInterface;

final readonly class PointRepository
{
    public function __construct(
        private AuditSaver $saver,
    ) {
    }

    /** @return PointDto[] */
    public function list(ConditionInterface $filter): array
    {
        $query = PointModel::query();
        $filter->apply($query);
        return $query->get()
            ->map(static function (PointModel $pointModel) {
                return $pointModel->toDto();
            })
            ->all();
    }

    public function insert(Point $point): PointDto
    {
        $row = new PointModel();
        return $this->save($row, $point);
    }

    public function update(Point $point): PointDto
    {
        $row = PointModel::query()->findOrFail($point->id->getValue());
        return $this->save($row, $point);
    }

    public function delete(PointId $id): void
    {
        $row = PointModel::query()->findOrFail($id->getValue());
        $this->saver->delete($row, 'point');
    }

    private function save(PointModel $row, Point $point): PointDto
    {
        $row->district_id = $point->districtId->getValue();
        $row->ordnum = $point->ordnum;
        $row->name = $point->name->getValue();
        $row->active = $point->active;
        $this->saver->save($row, 'point');
        return $row->toDto();
    }
}
