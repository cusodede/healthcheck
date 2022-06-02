<?php
declare(strict_types=1);

namespace dspl\healthcheck\components\web;

use dspl\healthcheck\helpers\HealthCheckHelper;
use Throwable;
use Yii;
use yii\base\Action;
use yii\base\InvalidConfigException;
use yii\web\Response;

/**
 * Действие проверки работоспособности приложения
 * Class HealthCheckAction
 * @package dspl\healthcheck\components\web
 */
class HealthCheckAction extends Action
{
    public const HEALTHY = 'Healthy';
    public const UNHEALTHY = 'Unhealthy';

    /**
     * Список компонентов для проверки
     * @var array
     */
    public array $componentsForCheck = [];

    /**
     * Функция обработки ошибок если она нужна
     * @var mixed
     */
    public mixed $errorHandler = null;

    /**
     * @inheritDoc
     */
    public function init(): void
    {
        if ([] === $this->componentsForCheck) {
            throw new InvalidConfigException('Не указан ни один из компонентов для проверки');
        }
    }

    /**
     * Запуск экшена
     * @return string
     */
    public function run(): string
    {
        Yii::$app->response->format = Response::FORMAT_RAW;
        Yii::$app->response->headers->set('Content-Type', 'text/plain; charset=utf-8');
        Yii::$app->response->headers->set('Cache-Control', 'no-cache, no-store, must-revalidate');

        $checkHelper = new HealthCheckHelper();
        try {
            foreach ($this->componentsForCheck as $checkComponent) {
                if (is_callable($checkComponent)) {
                    $checkComponent();
                } else {
                    $checkHelper->check($checkComponent);
                }
            }
            Yii::$app->response->setStatusCode(200, self::HEALTHY);
            Yii::$app->response->content = self::HEALTHY;
        } catch (Throwable $throwable) {
            $func = $this->errorHandler;
            if (is_callable($func, true)) {
                $func($throwable);
            }
            Yii::$app->response->setStatusCode(503, self::UNHEALTHY);
            Yii::$app->response->content = self::UNHEALTHY;
        }

        return Yii::$app->response->content;
    }
}
