<?php

declare(strict_types=1);

namespace _app\controllers;

use app\models\jobs\EmptyJob;
use dspl\healthcheck\components\web\HealthCheckAction;
use dspl\healthcheck\helpers\HealthCheckHelper;
use yii\rest\Controller;
use Yii;
use Throwable;

/**
 * Testing tests and shit
 */
class HealthController extends Controller
{
    /**
     * @inheritDoc
     */
    public function actions(): array
    {
        return [
            'index' => [
                'class' => HealthCheckAction::class,
                'componentsForCheck' => [
                    HealthCheckHelper::DB,
                    HealthCheckHelper::REDIS,
                    HealthCheckHelper::WRITABLE,
                    function () { //кастомная проверка любого компонента системы
                        Yii::$app->queue->push(
                            new EmptyJob([
                                'message' => 'test from psb',
                            ])
                        );
                    },
                ],
                'errorHandler' => function (Throwable $error) { //навеска на отлов ошибок
                    Yii::$app->log($error->getMessage());
                }
            ]
        ];
    }
}
