#!/usr/bin/env -S bash -euo pipefail -O extglob

# Hier nur Dinge erledigen, die voraussetzen, dass
# - bereits Docker Volumes gemountet sind, oder
# - bereits Host-Verzeichnisse bzw. Host-Dateien gemountet sind.

# $@ - Kommando
function _sudo() {
	echo www-data | sudo -p '' -S "$@";
}

_sudo rm -rf /var/log/*;
_sudo mkdir /var/log/php;
_sudo mkdir /var/log/php-fpm;
_sudo touch /var/log/php/error.log;
_sudo touch /var/log/php/xdebug.log;
_sudo touch /var/log/php-fpm/global.log;
_sudo touch /var/log/php-fpm/access.log;
_sudo touch /var/log/php-fpm/xdebug.log;
_sudo chown -R www-data:www-data /var/log/php;
_sudo chown -R www-data:www-data /var/log/php-fpm;
_sudo chmod 750 /var/log/php;
_sudo chmod 750 /var/log/php-fpm;
_sudo chmod 640 /var/log/php/*;
_sudo chmod 640 /var/log/php-fpm/*;

# sehr wichtig!
_sudo bash -c 'export SUDO_FORCE_REMOVE=yes;
	gpasswd -d www-data sudo > /dev/null;
	apt-get purge --auto-remove --no-install-recommends --quiet --quiet sudo > /dev/null;'

exec "$@";
