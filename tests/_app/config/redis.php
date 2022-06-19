<?php

declare(strict_types = 1);

use yii\redis\Connection;

return [
	'class' => Connection::class,
	'hostname' => $_ENV['REDIS_HOSTNAME'],
	'port' => $_ENV['REDIS_PORT'],
	'database' => $_ENV['REDIS_DATABASE']
];
