<?php

declare(strict_types = 1);

namespace cusodede\healthcheck\models;

use Throwable;
use Yii;
use yii\db\Exception;
use yii\helpers\ArrayHelper;

/**
 * Class RedisHealthCheck
 * Checks if a redis instance is accessible
 */
class RedisHealthCheck extends HealthCheck {

	public static string $key = 'healthcheck_key';
	public static string $value = 'healthcheck_value';

	/**
	 * @inheritDoc
	 * Parameters:
	 *    key: optional key
	 *    value: optional value
	 */
	public static function check(array $config = [], ?callable $errorHandler = null):string {
		$errorHandler = $errorHandler??[__CLASS__, 'errorHandler'];
		try {
			$key = ArrayHelper::getValue($config, 'key', static::$key);
			$value = ArrayHelper::getValue($config, 'value', static::$value);

			Yii::$app->redis->setex($key, 12, $value);

			if (Yii::$app->redis->get($key) !== $value) {
				$errorHandler(new Exception('Ошибка при получении ключа из redis'));
				return static::STATUS_UNHEALTHY;
			}
			return static::STATUS_HEALTHY;
		} catch (Throwable $throwable) {
			$errorHandler($throwable);
			return static::STATUS_UNHEALTHY;
		}
	}
}
