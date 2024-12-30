#!/usr/bin/env -S bash -euo pipefail -O extglob

# Hier nur Dinge erledigen, die voraussetzen, dass
# - bereits Docker Volumes gemountet sind, oder
# - bereits Host-Verzeichnisse bzw. Host-Dateien gemountet sind.

rm -rf /var/log/*;
mkdir /var/log/php;
mkdir /var/log/php-fpm;
touch /var/log/php/error.log;
touch /var/log/php/xdebug.log;
touch /var/log/php-fpm/global.log;
touch /var/log/php-fpm/access.log;
touch /var/log/php-fpm/xdebug.log;

chown -R www-data:www-data /var/log/php;
chown -R www-data:www-data /var/log/php-fpm;
find /var/log/php     -type d -exec chmod u=rwx,g=rwxs,o= {} \;
find /var/log/php-fpm -type d -exec chmod u=rwx,g=rwxs,o= {} \;
find /var/log/php     -type f -exec chmod u=rw,g=rw,o= {} \;
find /var/log/php-fpm -type f -exec chmod u=rw,g=rw,o= {} \;

chown -R www-data:www-data storage;
chown -R www-data:www-data bootstrap/cache;
find storage         -type d -exec chmod u=rwx,g=rwxs,o= {} \;
find bootstrap/cache -type d -exec chmod u=rwx,g=rwxs,o= {} \;
find storage         -type f -exec chmod u=rw,g=rw,o= {} \;
find bootstrap/cache -type f -exec chmod u=rw,g=rw,o= {} \;

chown www-data:www-data /var/www/.env;
# auch lesbar für init-Routine des DB-Containers.
# auch bearbeitbar für User normal, der in der Gruppe www-data
chmod u=rw,g=rw,o= /var/www/.env;

exec gosu www-data "$@";
