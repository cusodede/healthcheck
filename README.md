# Healthсheck

Пакет предоставляет реализацию проверок жизнеспособности приложения

[Спецификация](https://confluence.veon.com/pages/viewpage.action?pageId=173167558)

## Установка

```json
"require": {
    "cusodede/healthcheck": "^1.0.0"
}
```

```json
"repositories": [
    {
        "type": "vcs",
        "url": "https://github.com/cusodede/healthcheck"
    }
]
```
## Запуск тестов

### Локально

```bash
php vendor/bin/codecept run
```

### docker

Подготовка окружения (при первом использовании)

```bash
make build
```

Запуск тестов

```bash
make test
```

## todo лист

