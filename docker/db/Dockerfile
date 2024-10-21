FROM couchdb:3.4.1

SHELL [ "/usr/bin/env", "bash", "-euo", "pipefail", "-O", "extglob", "-c" ]

RUN --mount=type=cache,target=/var/cache/apt,sharing=private \
    --mount=type=cache,target=/var/lib/apt,sharing=private \
    rm -f /etc/apt/apt.conf.d/docker-clean; \
    echo 'Binary::apt::APT::Keep-Downloaded-Packages "true";' > /etc/apt/apt.conf.d/keep-cache; \
    apt-get update --quiet --quiet; \
    apt-get install --no-install-recommends --quiet --quiet \
        gettext \
		tree;

RUN --mount=target=/build_context \
	cp /build_context/docker-entrypoint-override.bash /; \
	chmod +x /docker-entrypoint-override.bash; \
	cp /build_context/local.template.ini /opt/couchdb/etc/; \
	tree  /opt/couchdb/etc/;

ENTRYPOINT ["tini", "--", "/docker-entrypoint-override.bash"]
CMD ["/opt/couchdb/bin/couchdb"]
