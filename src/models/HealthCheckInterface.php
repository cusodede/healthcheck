<?php

declare(strict_types=1);

namespace dspl\healthcheck\models;

/**
 * Interface HealthCheckInterface
 */
interface HealthCheckInterface
{

    /**
     * Does a check witch parameters
     * @param array $config
     * @param callable|null $errorHandler Optional error handler
     * @return bool Is the check success?
     */
    public static function check(array $config = [], ?callable $errorHandler = null): bool;

}
