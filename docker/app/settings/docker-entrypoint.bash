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
chmod 750 /var/log/php;
chmod 750 /var/log/php-fpm;
chmod 640 /var/log/php/*;
chmod 640 /var/log/php-fpm/*;

exec gosu www-data "$@";
