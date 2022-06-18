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
    public const HEALTHY = 'Healthy';
    public const UNHEALTHY = 'Unhealthy';
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
                $result = $checkComponent($params ?? [], $errorHandler ?? null);
            } catch (Throwable $throwable) {
                $result = false;
                self::$LAST_ERROR = $throwable->getMessage();
            }

            if ($result) {
                Yii::$app->response->setStatusCode(200, self::HEALTHY);
                Yii::$app->response->content = self::HEALTHY;
            } else {
                Yii::$app->response->setStatusCode(503, self::UNHEALTHY);
                Yii::$app->response->content = self::UNHEALTHY;
            }
        }

        return Yii::$app->response->content;
    }
}
