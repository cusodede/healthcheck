<?php

declare(strict_types=1);

namespace dspl\healthcheck\models;

use Throwable;
use yii\db\Expression;
use yii\db\Query;
use yii\helpers\ArrayHelper;

/**
 * Class DbHealthCheck
 * Checks if a database is accessible.
 */
class DbHealthCheck extends HealthCheck
{

    /**
     * @inheritDoc
     * Parameters:
     *    db: database connection (default connection will be used, if ignored)
     *    query: optional query
     */
    public static function check(array $config = [], ?callable $errorHandler = null): string
    {
        try {
            (new Query())->select(ArrayHelper::getValue($config, 'query', new Expression("1")))->createCommand(
                ArrayHelper::getValue($config, 'db')
            )->execute();
            return static::STATUS_HEALTHY;
        } catch (Throwable $throwable) {
            if (null === $errorHandler) {
                static::ErrorHandler($throwable);
            } else {
                $errorHandler($throwable);
            }
            return static::STATUS_UNHEALTHY;
        }
    }
}
