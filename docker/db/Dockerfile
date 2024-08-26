FROM couchdb:3.3.3

SHELL [ "/usr/bin/env", "bash", "-euo", "pipefail", "-O", "extglob", "-c" ]

ARG SYS_ADMIN_USER=''
ARG SYS_ADMIN_PASSWORD=''
# ARG COUCHDB_USER=''
# ARG COUCHDB_PASSWORD=''

RUN --mount=type=cache,target=/var/cache/apt,sharing=private \
    --mount=type=cache,target=/var/lib/apt,sharing=private \
    rm -f /etc/apt/apt.conf.d/docker-clean; \
    echo 'Binary::apt::APT::Keep-Downloaded-Packages "true";' > /etc/apt/apt.conf.d/keep-cache; \
    apt-get update --quiet --quiet; \
    apt-get install --no-install-recommends --quiet --quiet \
        gettext;

RUN --mount=source=.,target=/build_context \
    SYS_ADMIN_USER="$SYS_ADMIN_USER" \
    SYS_ADMIN_PASSWORD="$SYS_ADMIN_PASSWORD" \
    envsubst '\$SYS_ADMIN_USER,\$SYS_ADMIN_PASSWORD' \
        < /build_context/local.ini \
        > /opt/couchdb/etc/local.ini;

# RUN --mount=source=.,target=/build_context \
#     mv /docker-entrypoint.sh /docker-entrypoint.sh.backup; \
#     sed -E -e 's/^(\s*)exec ?(.*"\$@")$/\1\2 \&/g' /docker-entrypoint.sh.backup > /docker-entrypoint.sh; \
#     rm /docker-entrypoint.sh.backup;

# RUN --mount=source=.,target=/build_context \
#     SYS_ADMIN_USER="$SYS_ADMIN_USER" \
#     SYS_ADMIN_PASSWORD="$SYS_ADMIN_PASSWORD" \
#     COUCHDB_USER="$COUCHDB_USER" \
#     COUCHDB_PASSWORD="$COUCHDB_PASSWORD" \
#         envsubst '\$COUCHDB_ADMIN_USER,\$COUCHDB_ADMIN_PASSWORD,\$COUCHDB_USER,\$COUCHDB_PASSWORD' \
#         < /build_context/docker-entrypoint-override.bash \
#         > /docker-entrypoint-override.bash; \
#     chmod 0111 /docker-entrypoint-override.bash;

# ENTRYPOINT [ "/docker-entrypoint-override.bash" ]
# CMD [ "/opt/couchdb/bin/couchdb" ]
# ENTRYPOINT [ "tail", "-f", "/dev/null" ]
# CMD []
