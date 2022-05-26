<?php
declare(strict_types=1);

namespace dspl\healthcheck\components\web;

use dspl\healthcheck\helpers\HealthCheckHelper;
use pozitronik\sys_exceptions\models\SysExceptions;
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

    public array $componentsForCheck = [];

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
     * Run action
     * @return string
     */
    public function run(): string
    {
        Yii::$app->response->format = Response::FORMAT_RAW;
        Yii::$app->response->headers->set('Content-Type', 'text/plain; charset=utf-8');
        Yii::$app->response->headers->set('Cache-Control', 'no-cache, no-store, must-revalidate');

        $checkHelper = new HealthCheckHelper();
        try {
            foreach ($this->componentsForCheck as $componentId) {
                $checkHelper->check($componentId);
            }
            Yii::$app->response->setStatusCode(200, self::HEALTHY);
            Yii::$app->response->content = self::HEALTHY;
        } catch (Throwable $throwable) {
            SysExceptions::log($throwable);
            Yii::$app->response->setStatusCode(503, self::UNHEALTHY);
            Yii::$app->response->content = self::UNHEALTHY;
        }

        return Yii::$app->response->content;
    }
}
