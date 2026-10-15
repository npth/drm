<?php

declare(strict_types=1);

use Illuminate\Database\Capsule\Manager;
use Psr\Container\ContainerInterface;

http_response_code(500);

require __DIR__ . '/../vendor/autoload.php';

require __DIR__ . '/../config/environment.php';

ini_set('date.timezone', getenv('PHP_TZ'));

/** @var ContainerInterface $container */
$container = require __DIR__ . '/../config/container.php';

$app = (require __DIR__ . '/../config/app.php')($container);
$container->get(Manager::class);

\App\Log::info('start');
\App\Log::info($_SERVER['REQUEST_URI']);
$app->run();
