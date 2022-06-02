<?php

declare(strict_types=1);

namespace app\controllers;

use app\models\jobs\EmptyJob;
use dspl\healthcheck\components\web\HealthCheckAction;
use dspl\healthcheck\helpers\HealthCheckHelper;
use yii\base\ErrorException;
use yii\rest\Controller;
use Yii;
use Throwable;

/**
 * Testing tests and shit
 */
class HealthController extends Controller
{

    public static ?string $LAST_ERROR = null;

    /**
     * @inheritDoc
     */
    public function actions(): array
    {
        return [
            'db' => [
                'class' => HealthCheckAction::class,
                'componentsForCheck' => [
                    HealthCheckHelper::DB,
                ],
                'errorHandler' => [static::class, "ErrorHandler"]
            ],
            'redis' => [
                'class' => HealthCheckAction::class,
                'componentsForCheck' => [
                    HealthCheckHelper::REDIS,
                ],
                'errorHandler' => [static::class, "ErrorHandler"]
            ],
            'writable' => [
                'class' => HealthCheckAction::class,
                'componentsForCheck' => [
                    HealthCheckHelper::WRITABLE,
                ],
                'errorHandler' => [static::class, "ErrorHandler"]
            ],
            'custom' => [
                'class' => HealthCheckAction::class,
                'componentsForCheck' => [
                    function () { //кастомная проверка любого компонента системы
                        Yii::$app->queue->push(
                            new EmptyJob([
                                'message' => 'test from psb',
                            ])
                        );
                    },
                ],
                'errorHandler' => [static::class, "ErrorHandler"]
            ],
            'error' => [
                'class' => HealthCheckAction::class,
                'componentsForCheck' => [
                    function () {
                        throw new ErrorException('Something bad happened');
                    },
                ],
                'errorHandler' => [static::class, "ErrorHandler"]
            ]
        ];
    }

    /**
     * @param Throwable $error
     * @return void
     */
    public static function ErrorHandler(Throwable $error): void
    {
        static::$LAST_ERROR = $error->getMessage();
    }
}
