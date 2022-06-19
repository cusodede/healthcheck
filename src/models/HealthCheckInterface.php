<?php

declare(strict_types=1);

namespace dspl\healthcheck\models;

/**
 * Interface HealthCheckInterface
 */
interface HealthCheckInterface
{
    public const STATUS_HEALTHY = 'Healthy';
    public const STATUS_UNHEALTHY = 'Unhealthy';
    public const STATUS_DEGRADED = 'Degraded';

    /**
     * Does a check witch parameters
     * @param array $config
     * @param callable|null $errorHandler Optional error handler
     * @return string One of STATUS_* constants
     */
    public static function check(array $config = [], ?callable $errorHandler = null): string;

}
