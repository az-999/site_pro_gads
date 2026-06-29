#!/bin/bash
set -e

cd /var/www/html

if [ ! -f vendor/autoload.php ]; then
    composer install --no-interaction --prefer-dist
fi

mkdir -p runtime/uploads web/assets
chown -R www-data:www-data runtime web/assets

exec apache2-foreground
