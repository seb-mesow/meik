#!/usr/bin/env -S bash -euxo pipefail

# $1 - user - "admin" or "normal"
# $2 - HTTP method like PUT GET POST DELETE
# $3 - path, must start with /
# [$4] - JSON
function curl_db() {
    local user="$1";
    local method="$2";
    local path="$3";
    if [[ "$user" == "admin" ]] ; then
        # Trennung User und Passwort durch Doppelpunkt
        local auth='${COUCHDB_ADMIN_USER}:${COUCHDB_ADMIN_PASSWORD}'
    else
        local auth='${COUCHDB_USER}:${COUCHDB_PASSWORD}'
    fi
    local -a opts;
    if [[ "$#" -ge 4 ]] ; then
        opts=("-d" "$4");
    fi
    curl -s \
        -u "$auth" \
        -H 'Content-Type: application/json' \
        -H 'Accept: application/json' \
        -X "$method" \
        "${opts[@]}" \
        "localhost:5984$path";
}

{
    echo "begin_wait" > /root/create_users.log;
    declare -i tries=0;
    declare -i max_tries=20;
    while ! curl_db admin GET /
    do
        if (( ++tries >= max_tries ))  ; then
            exit 1;
        fi
        sleep .5;
    done
    if curl_db admin PUT '/_users/org.couchdb.user:${COUCHDB_USER}' '{
        "type": "user",
        "name": "$COUCHDB_USER",
        "password": "$COUCHDB_PASSWORD",
        "roles": []
    }'
    then
        echo "success" >> /root/create_users.log;
        exit 0;
    else
        echo "failure" >> /root/create_users.log;
        exit 1;
    fi
} &

exec tini -- /docker-entrypoint.sh "$@";
