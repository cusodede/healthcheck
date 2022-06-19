<?php

declare(strict_types=1);

namespace dspl\healthcheck\components\web;

use dspl\healthcheck\models\HealthCheckInterface;
use Throwable;
use Yii;
use yii\base\Action;
use yii\web\Response;

/**
 * Действие проверки работоспособности приложения
 * Class HealthCheckAction
 *
 */
class HealthCheckAction extends Action
{
    public static string $LAST_ERROR;

    /**
     * Список компонентов для проверки
     * @var HealthCheckInterface[]
     */
    public array $healthCheckComponents = [];

    /**
     * Функция обработки ошибок если она нужна
     * @var mixed
     */
    public mixed $errorHandler = null;

    /**
     * Запуск экшена
     * @return string
     */
    public function run(): string
    {
        Yii::$app->response->format = Response::FORMAT_RAW;
        Yii::$app->response->headers->set('Content-Type', 'text/plain; charset=utf-8');
        Yii::$app->response->headers->set('Cache-Control', 'no-cache, no-store, must-revalidate');

        foreach ($this->healthCheckComponents as $checkComponentConfig) {
            if (is_array($checkComponentConfig)) {//[class, params, errorHandler]
                $checkComponentConfig = array_pad($checkComponentConfig, 3, null);
                [$checkComponent, $params, $errorHandler] = $checkComponentConfig;
            } else {
                $checkComponent = $checkComponentConfig;//just class
            }

            $checkComponent = is_callable($checkComponent) ? $checkComponent : [$checkComponent, 'check'];

            try {
                Yii::$app->response->content = $checkComponent($params ?? [], $errorHandler ?? null);
            } catch (Throwable $throwable) {
                Yii::$app->response->content = HealthCheckInterface::STATUS_UNHEALTHY;
                self::$LAST_ERROR = $throwable->getMessage();
            }

            switch (Yii::$app->response->content) {
                case HealthCheckInterface::STATUS_HEALTHY:
                    Yii::$app->response->setStatusCode(200, HealthCheckInterface::STATUS_HEALTHY);
                    break;
                case HealthCheckInterface::STATUS_UNHEALTHY:
                    Yii::$app->response->setStatusCode(503, HealthCheckInterface::STATUS_UNHEALTHY);
                    break;
                case HealthCheckInterface::STATUS_DEGRADED:
                    Yii::$app->response->setStatusCode(200, HealthCheckInterface::STATUS_DEGRADED);
                    break;
            }
        }

        return Yii::$app->response->content;
    }
}
