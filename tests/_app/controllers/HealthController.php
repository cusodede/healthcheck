<?php

declare(strict_types = 1);

namespace app\controllers;

use app\models\jobs\EmptyJob;
use cusodede\healthcheck\components\web\HealthCheckAction;
use cusodede\healthcheck\models\DbHealthCheck;
use cusodede\healthcheck\models\HealthCheckInterface;
use cusodede\healthcheck\models\PathAccessHealthCheck;
use cusodede\healthcheck\models\RedisHealthCheck;
use yii\base\ErrorException;
use yii\rest\Controller;
use Yii;

/**
 * Testing tests and shit
 */
class HealthController extends Controller {

	public static ?string $LAST_ERROR = null;

	/**
	 * @inheritDoc
	 */
	public function actions():array {
		return [
			'db' => [
				'class' => HealthCheckAction::class,
				'healthCheckComponents' => [
					DbHealthCheck::class,
				]
			],
			'db_timeout' => [
				'class' => HealthCheckAction::class,
				'healthCheckComponents' => [
					[DbHealthCheck::class, ['degrade_timeout' => 1]]
				]
			],
			'redis' => [
				'class' => HealthCheckAction::class,
				'healthCheckComponents' => [
					RedisHealthCheck::class
				],

			],
			'writable' => [
				'class' => HealthCheckAction::class,
				'healthCheckComponents' => [
					[
						PathAccessHealthCheck::class,
						['path' => [Yii::$app->assetManager->basePath, '@runtime', '@runtime/logs']]
					]
				],
			],
			'custom' => [
				'class' => HealthCheckAction::class,
				'healthCheckComponents' => [
					function() { //кастомная проверка любого компонента системы
						/** @noinspection PhpMethodParametersCountMismatchInspection */
						Yii::$app->queue->push(
							new EmptyJob([
								'message' => 'test from psb',
							])
						);
						return HealthCheckInterface::STATUS_HEALTHY;
					},
				],
			],
			'error' => [
				'class' => HealthCheckAction::class,
				'healthCheckComponents' => [
					function() {
						throw new ErrorException('Something bad happened');
					}
				],
			]
		];
	}

}
