#!/usr/bin/env -S bash -euo pipefail

tree /opt/couchdb/etc > /dev/stderr

# hat BlackBox AI geschrieben; bisschen angepasst
# read the variables file
while IFS='=' read -r key value; do
    # ignore comments and empty lines
    [[ "$key" =~ ^#.*$ || -z "$key" ]] && continue;
    
    # strip any leading/trailing whitespace and quotes from the value
    key="$(echo "$key" | xargs echo)";
    value="$(echo "$value" | xargs echo | sed -e 's/^"\(.*\)"$/\1/' -e "s/^'\(.*\)'$/\1/")"
    
    # assign the value to the variable
    declare "$key"="$value";
done < /opt/couchdb/etc/couchdb_credentials.sh;

COUCHDB_SYS_ADMIN_USERNAME="$COUCHDB_SYS_ADMIN_USERNAME" \
COUCHDB_SYS_ADMIN_PASSWORD="$COUCHDB_SYS_ADMIN_PASSWORD" \
envsubst '\$COUCHDB_SYS_ADMIN_USERNAME,\$COUCHDB_SYS_ADMIN_PASSWORD' \
< /opt/couchdb/etc/local.template.ini \
> /opt/couchdb/etc/local.ini;

exec /docker-entrypoint.sh "$@" > /dev/stderr;
