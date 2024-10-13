#!/bin/sh -eu

# Hier nur Dinge erledigen, die voraussetzen, dass
# - bereits Docker Volumes gemountet sind, oder
# - bereits Host-Verzeichnisse bzw. Host-Dateien gemountet sind.

echo &> /dev/null; # Hinweistext bei erster Ausführung weg
rm -rf /var/log/*;
mkdir /var/log/nginx;
touch /var/log/nginx/error.log;
touch /var/log/nginx/access.log;
chown -R nginx:www-data-from-app /var/log/nginx;
chmod 750 /var/log/nginx;
chmod 640 /var/log/nginx/*;
