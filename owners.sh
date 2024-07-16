#!/bin/sh

# Betiğin bulunduğu dizini belirle
PROJECT_DIR=$(cd "$(dirname "$0")"; pwd)
echo "Project Directory: $PROJECT_DIR"

# Sahiplik değiştirilmesi gereken dizinler
set -f
DIRECTORIES="$PROJECT_DIR/app
$PROJECT_DIR/routes
$PROJECT_DIR/storage
$PROJECT_DIR/storage/app/private
$PROJECT_DIR/tests
$PROJECT_DIR/database"

# Dizileri gezip sahiplikleri değiştir
for DIR in $DIRECTORIES
do
    if [ -d "$DIR" ]; then
        echo "Processing $DIR"
        chown -R 1000:1000 "$DIR"
    else
        echo "Directory doesn't exist: $DIR" >&2
    fi
done

chown -R www-data:www-data /var/www/storage /var/www/bootstrap/cache
chmod -R 775 /var/www/storage /var/www/bootstrap/cache
