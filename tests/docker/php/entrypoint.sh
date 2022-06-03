#!/bin/sh

set -eu

composer clearcache
flock tests/runtime/composer-install.lock composer update --prefer-dist --no-interaction

php --version
set -x
exec "$@"
