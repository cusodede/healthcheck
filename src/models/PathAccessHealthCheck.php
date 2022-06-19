<?php

declare(strict_types = 1);

namespace cusodede\healthcheck\models;

use Throwable;
use Yii;
use yii\base\Exception;
use yii\helpers\ArrayHelper;

/**
 * Class PathAccessHealthCheck
 * Checks if a filepath writable
 */
class PathAccessHealthCheck extends HealthCheck {

	/**
	 * @inheritDoc
	 * Parameters:
	 *    path: required, paths, checked for write access. Can be a directory, file or a scheme of any type, that PHP can handle.
	 *    It can be an array of paths entries as well.
	 */
	public static function check(array $config = [], ?callable $errorHandler = null):string {
		$errorHandler = $errorHandler??[__CLASS__, 'errorHandler'];
		try {
			$paths = ArrayHelper::getValue($config, 'path', ['@runtime']);
			if (!is_array($paths)) {
				$paths = [$paths];
			}
			foreach ($paths as $path) {
				if (!is_writable(Yii::getAlias($path))) {
					$errorHandler(new Exception("Path {$path} is not accessible for writing"));
					return static::STATUS_UNHEALTHY;
				}
			}
			return static::STATUS_HEALTHY;
		} catch (Throwable $throwable) {
			$errorHandler($throwable);
			return static::STATUS_HEALTHY;
		}
	}
}
