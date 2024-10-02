#!/bin/sh -eu

# Hier nur Dinge erledigen, die voraussetzen, dass
# - bereits Docker Volumes gemountet sind, oder
# - bereits Host-Verzeichnisse bzw. Host-Dateien gemountet sind.

# $@ - Kommando
function _sudo() {
	echo nginx | sudo -p '' -S "$@";
}

_sudo echo &> /dev/null; # Hinweistext bei erster Ausführung weg
_sudo rm -rf /var/log/*;
_sudo mkdir /var/log/nginx;
_sudo touch /var/log/nginx/error.log;
_sudo touch /var/log/nginx/access.log;
_sudo chown -R nginx:www-data-from-app /var/log/nginx;
_sudo chmod 750 /var/log/nginx;
_sudo chmod 640 /var/log/nginx/*;

# sehr wichtig!
sudo sh -c 'delgroup nginx wheel > /dev/null;
	apk del --purge --no-interactive --quiet sudo > /dev/null 2>&1;'
