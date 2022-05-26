<?php
declare(strict_types=1);

namespace dspl\healthcheck\helpers;

use app\modules\api\models\jobs\EmptyJob;
use app\modules\s3\helpers\S3Helper;
use app\modules\s3\models\S3;
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
    public const RABBITMQ = 'Health RABBITMQ';
    public const MINIO = 'Health MINIO';
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
            case self::RABBITMQ:
                $this->checkRabbitMq();
                break;
            case self::MINIO:
                $this->checkS3Health();
                break;
            case self::REDIS:
                $this->checkRedis();
                break;
            case self::WRITABLE:
                $this->checkFileIsWritable();
                break;
            default:
                throw new InvalidConfigException("Не назначена проверка для типа - {$checkType}");
        }
    }

    /**
     * Проверяем доступны ли папки для записи
     * @return void
     * @throws InvalidConfigException
     */
    private function checkFileIsWritable(): void
    {
        //min chmod 007 runtime
        $assetsFolder = Yii::getAlias('@webroot/assets');
        $runtimeFolder = Yii::getAlias('@runtime');
        $runtimeLogFolder = Yii::getAlias('@runtime/logs');

        $perms = substr(sprintf('%o', fileperms($assetsFolder)), -1);
        if (false === is_writable($assetsFolder) || 7 !== (int)$perms) {
            throw new InvalidConfigException("Папка $assetsFolder не доступна для записи");
        }

        if (false === is_writable($runtimeFolder)) {
            throw new InvalidConfigException("Папка $runtimeFolder не доступна для записи");
        }

        if (false === is_writable($runtimeLogFolder)) {
            throw new InvalidConfigException("Папка $runtimeFolder не доступна для записи");
        }
    }

    /**
     * Пушим в тестовую очередь
     * Проверяем доступность rabbitMq
     * @return void
     */
    private function checkRabbitMq(): void
    {
        $rabbitMQ = Yii::$app->mqHealthCheck;
        $rabbitMQ->push(
            new EmptyJob([
                'message' => 'test from dpl',
            ])
        );
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
     * Проверяем доступность s3 подключения
     * @return void
     * @throws Throwable
     * @throws BaseException
     */
    private function checkS3Health(): void
    {
        $s3 = (new S3());
        $s3->setTimeout(5);
        $s3->setConnectTimeout(3);
        $s3->getListBucketMap();
        $filePathForUpload = self::GetRandomTempFileName('test', 'csv');
        file_put_contents($filePathForUpload, '');
        $cloudObject = S3Helper::FileToStorage($filePathForUpload);
        unlink($filePathForUpload);
        $filePathFromS3 = S3Helper::StorageToFile($cloudObject->id);
        if (false === file_exists($filePathFromS3)) {
            unlink($filePathFromS3);
            throw new BaseException('Ошибка при загрузке файла');
        }
        unlink($filePathFromS3);
    }

    /**
     * Проверяем db подключение
     * @throws Exception
     */
    private function checkDbHealth(): void
    {
        (new Query())->select(new Expression("1"))->createCommand(Yii::$app->db)->execute();
    }

    /**
     * Возвращает случайное имя файла во временном каталоге с заданным префиксом и расширением
     * @param string|null $prefix Префикс имени файла
     * @param string|null $ext Расширение файла (без точки). Если не указано, будет использовано расширение 'tmp'
     * @return string
     * @throws BaseException
     */
    public static function GetRandomTempFileName(?string $prefix = null, ?string $ext = null): string
    {
        return sys_get_temp_dir() . DIRECTORY_SEPARATOR . ($prefix ?? '') . Yii::$app->security->generateRandomString(
                6
            ) . '.' . ($ext ?? 'tmp');
    }
}
