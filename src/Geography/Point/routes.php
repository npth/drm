<?php

declare(strict_types=1);

/**
 * Маршруты API для справочника "Населённые пункты"
 *
 * Базовый путь: /api/geography/points
 */

use Slim\App;
use App\Geography\Point\PointController;
use App\Geography\Point\PointService;
use App\Geography\Point\PointRepository;

return function (App $app) {
    $container = $app->getContainer();

    // Репозиторий
    $container->set(PointRepository::class, function ($c) {
        return new PointRepository($c->get('db'));
    });

    // Сервис
    $container->set(PointService::class, function ($c) {
        return new PointService(
            $c->get(PointRepository::class),
            $c->get(\App\Geography\District\DistrictRepository::class)
        );
    });

    // Контроллер
    $container->set(PointController::class, function ($c) {
        return new PointController($c->get(PointService::class));
    });

    // Маршруты
    $app->group('/api/geography/points', function ($group) {
        $group->get('', PointController::class . ':index');
        $group->get('/{id}', PointController::class . ':show');
        $group->post('', PointController::class . ':store');
        $group->put('/{id}', PointController::class . ':update');
        $group->delete('/{id}', PointController::class . ':destroy');
    });
};
