<?php

declare(strict_types = 1);

namespace cusodede\healthcheck\models;

use Throwable;
use yii\db\Expression;
use yii\db\Query;
use yii\helpers\ArrayHelper;

/**
 * Class DbHealthCheck
 * Checks if a database is accessible.
 */
class DbHealthCheck extends HealthCheck {

	/**
	 * @inheritDoc
	 * Parameters:
	 *    db: database connection (default connection will be used, if ignored)
	 *    query: optional query
	 *    degrade_timeout: optional, time (in ms) for query execution. If execution will take more time, than that,
	 *    check will return STATUS_DEGRADED
	 */
	public static function check(array $config = [], ?callable $errorHandler = null):string {
		try {
			$startTime = hrtime(true);
			(new Query())->select(ArrayHelper::getValue($config, 'query', new Expression("1")))->createCommand(
				ArrayHelper::getValue($config, 'db')
			)->execute();
			$queryTime = hrtime(true) - $startTime;
			if ((null !== $degrade_timeout = ArrayHelper::getValue(
						$config,
						'degrade_timeout'
					)) && $degrade_timeout <= $queryTime) {
				static::$DEGRADED_MESSAGE = "Execution time is too slow ({$queryTime})!";
				return static::STATUS_DEGRADED;
			}
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
