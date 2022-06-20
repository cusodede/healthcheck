# Healthсheck

Пакет предоставляет реализацию проверок жизнеспособности приложения.

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

