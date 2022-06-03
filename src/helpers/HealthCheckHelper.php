<?php

declare(strict_types=1);

namespace dspl\healthcheck\helpers;

use Throwable;
use Yii;
use yii\base\Exception as BaseException;
use yii\base\InvalidConfigException;
use yii\db\Exception;
use yii\db\Expression;
use yii\db\Query;

/**
 * Помогает проверять компоненты системы
 * Class HealthCheckHelper
 * @package dspl\healthcheck\helpers
 */
class HealthCheckHelper
{
    public const DB = 'Health DB';
    public const REDIS = 'Health REDIS';
    public const WRITABLE = 'Writable files';

    /**
     * Запускает проверку компонента основываясь на его идентификаторе
     * @param string $checkType
     * @return void
     * @throws BaseException
     * @throws Exception
     * @throws Throwable
     */
    public function check(string $checkType): void
    {
        switch ($checkType) {
            case self::DB:
                $this->checkDbHealth();
                break;
            case self::REDIS:
                $this->checkRedis();
                break;
            case self::WRITABLE:
                $this->checkFileIsWritable();
                break;
            default:
                throw new Exception("Не назначена проверка для типа - {$checkType}");
        }
    }

    /**
     * Проверяем доступны ли папки для записи
     * @return void
     * @throws InvalidConfigException
     */
    private function checkFileIsWritable(): void
    {
        $assetsFolder = Yii::getAlias(Yii::$app->assetManager->basePath);
        $runtimeFolder = Yii::getAlias('@runtime');
        $runtimeLogFolder = Yii::getAlias('@runtime/logs');

        if (false === is_writable($assetsFolder)) {
            throw new InvalidConfigException("Папка $assetsFolder не доступна для записи");
        }

        if (false === is_writable($runtimeFolder)) {
            throw new InvalidConfigException("Папка $runtimeFolder не доступна для записи");
        }

        if (false === is_writable($runtimeLogFolder)) {
            throw new InvalidConfigException("Папка $runtimeLogFolder не доступна для записи");
        }
    }

    /**
     * Проверяем работу redis
     * @return void
     * @throws BaseException
     * @throws Exception
     */
    private function checkRedis(): void
    {
        $keyValue = Yii::$app->security->generateRandomString(12);

        Yii::$app->redis->setex($keyValue, 12, $keyValue);
        $keyFromRedis = Yii::$app->redis->get($keyValue);

        if ($keyFromRedis !== $keyValue) {
            throw new Exception('Ошибка при получении ключа из redis');
        }
    }

    /**
     * Проверяем db подключение
     * @throws Exception
     */
    private function checkDbHealth(): void
    {
        (new Query())->select(new Expression("1"))->createCommand(Yii::$app->db)->execute();
    }
}
