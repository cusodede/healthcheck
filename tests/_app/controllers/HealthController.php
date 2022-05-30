<?php

declare(strict_types=1);

namespace app\controllers;

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
            'db' => [
                'class' => HealthCheckAction::class,
                'componentsForCheck' => [
                    HealthCheckHelper::DB,
                ],
                'errorHandler' => function (Throwable $error) { //навеска на отлов ошибок
                    Yii::debug($error->getMessage());
                }
            ],
            'redis' => [
                'class' => HealthCheckAction::class,
                'componentsForCheck' => [
                    HealthCheckHelper::REDIS,
                ],
                'errorHandler' => function (Throwable $error) { //навеска на отлов ошибок
                    Yii::debug($error->getMessage());
                }
            ],
            'writable' => [
                'class' => HealthCheckAction::class,
                'componentsForCheck' => [
                    HealthCheckHelper::WRITABLE,
                ],
                'errorHandler' => function (Throwable $error) { //навеска на отлов ошибок
                    Yii::debug($error->getMessage());
                }
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
                'errorHandler' => function (Throwable $error) { //навеска на отлов ошибок
                    Yii::debug($error->getMessage());
                }
            ],
        ];
    }
}
