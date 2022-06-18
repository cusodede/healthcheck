<?php

declare(strict_types=1);

namespace dspl\healthcheck\models;

use Throwable;

/**
 * Class HealthCheck
 */
abstract class HealthCheck implements HealthCheckInterface
{

    /**
     * @var string|null
     */
    public static ?string $LAST_ERROR = null;

    /**
     * @param Throwable $error
     * @return void
     */
    public static function ErrorHandler(Throwable $error): void
    {
        static::$LAST_ERROR = $error->getMessage();
    }
}
