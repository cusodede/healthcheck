# Healthсheck

Пакет предоставляет реализацию проверок жизнеспособности приложения

[Спецификация](https://confluence.veon.com/pages/viewpage.action?pageId=173167558)

- Database
- Redis
- Permission folder
- Доступны кастомные проверки

## Подключение

```php
class HealthController extends yii\rest\Controller {
    ...
    /**
     * @inheritDoc
     */
    public function actions():array {
        return [
            'index' => [
                'class' => HealthCheckAction::class,
                'componentsForCheck' => [
                    HealthCheckHelper::DB,
                    HealthCheckHelper::REDIS,
                    HealthCheckHelper::WRITABLE,
                    function() { //кастомная проверка любого компонента системы
                        Yii::$app->mqHealthCheck->push(new EmptyJob([
                            'message' => 'test from psb',
                        ]));
                    },
                ],
                'errorHandler' => function($error) { //навеска на отлов ошибок
                    SysExceptions::log($error);
                }
            ]
        ];
    }

    ...
}
```

## Установка

```json
"require": {
    "dspl/healthcheck": "^1.0.0"
}
```

```json
"repositories": [
    {
        "type": "vcs",
        "url": "https://git.vimpelcom.ru/products/dspl/healthcheck"
    }
]
```

Создаем файл auth.json

```bash
composer config gitlab-token.git.vimpelcom.ru <ДОМЕННЫЙ ЛОГИН> <ТОКЕН>
```

## Решение ошибок

> fatal: unable to access 'https://git.vimpelcom.ru/products/dspl/validators.git/': server certificate verification
> failed. CAfile: none CRLfile: none

Получим сертификат с git.vimpelcom.ru

```bash
openssl s_client -showcerts -servername git.vimpelcom.ru -connect git.vimpelcom.ru:443 </dev/null 2>/dev/null | sed -n -e '/BEGIN\ CERTIFICATE/,/END\ CERTIFICATE/ p'  > /usr/local/share/ca-certificates/git.vimpelcom.ru.pem
```

Добавим полученный сертификат к остальным

```bash
cat /usr/local/share/ca-certificates/git.vimpelcom.ru.pem | tee -a /etc/ssl/certs/ca-certificates.crt
```

## Запуск тестов

Подготовка окружение (при первом использовании)

```bash
make build
```

Запуск тестов

```bash
make test
```

## todo лист

