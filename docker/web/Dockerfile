FROM nginxinc/nginx-unprivileged:1.26.2-alpine3.20-slim

SHELL [ "/usr/bin/env", "ash", "-eu", "-c" ]

USER root

ARG GOSU_VERSION=1.17
ARG GOSU_PUP_KEY_FINGERPRINT=B42F6819007F00F88E364FD4036A9C25BF357DD4

RUN --mount=type=cache,target=/var/cache/apk,sharing=private \
	apk --no-interactive --quiet update; \
	apk --no-interactive --quiet add --no-cache --virtual .gosu-deps \
		ca-certificates \
		dpkg \
		gnupg \
	; \
	dpkgArch="$(dpkg --print-architecture | awk -F- '{ print $NF }')"; \
	wget -O /usr/local/bin/gosu "https://github.com/tianon/gosu/releases/download/$GOSU_VERSION/gosu-$dpkgArch"; \
	wget -O /usr/local/bin/gosu.asc "https://github.com/tianon/gosu/releases/download/$GOSU_VERSION/gosu-$dpkgArch.asc"; \
	# verify the signature
	export GNUPGHOME="$(mktemp -d)"; \
	gpg --batch --keyserver hkps://keys.openpgp.org --recv-keys "$GOSU_PUP_KEY_FINGERPRINT"; \
	gpg --batch --verify /usr/local/bin/gosu.asc /usr/local/bin/gosu; \
	gpgconf --kill all; \
	rm -rf "$GNUPGHOME" /usr/local/bin/gosu.asc; \
	# clean up fetch dependencies
	apk --no-interactive --quiet del --no-network .gosu-deps; \
	chmod +x /usr/local/bin/gosu; \
	# verify that the binary works
	gosu --version; \
	gosu nobody true;

ARG GOSU_VERSION=1.17
ARG GOSU_PUP_KEY_FINGERPRINT=B42F6819007F00F88E364FD4036A9C25BF357DD4

RUN --mount=type=cache,target=/var/cache/apk,sharing=private \
	apk --no-interactive --quiet update; \
	apk --no-interactive --quiet add --no-cache --virtual .gosu-deps \
		ca-certificates \
		dpkg \
		gnupg \
	; \
	dpkgArch="$(dpkg --print-architecture | awk -F- '{ print $NF }')"; \
	wget -O /usr/local/bin/gosu "https://github.com/tianon/gosu/releases/download/$GOSU_VERSION/gosu-$dpkgArch"; \
	wget -O /usr/local/bin/gosu.asc "https://github.com/tianon/gosu/releases/download/$GOSU_VERSION/gosu-$dpkgArch.asc"; \
	# verify the signature
	export GNUPGHOME="$(mktemp -d)"; \
	gpg --batch --keyserver hkps://keys.openpgp.org --recv-keys "$GOSU_PUP_KEY_FINGERPRINT"; \
	gpg --batch --verify /usr/local/bin/gosu.asc /usr/local/bin/gosu; \
	gpgconf --kill all; \
	rm -rf "$GNUPGHOME" /usr/local/bin/gosu.asc; \
	# clean up fetch dependencies
	apk --no-interactive --quiet del --no-network .gosu-deps; \
	chmod +x /usr/local/bin/gosu; \
	# verify that the binary works
	gosu --version; \
	gosu nobody true;

RUN --mount=source=.,target=/build_context \
	cp /build_context/default.conf /etc/nginx/conf.d; \
	chown -hR root:nginx /etc/nginx; \
	find /etc/nginx -type d -exec chmod u=rx,g=rx,o= '{}' +; \
	find /etc/nginx -type f -exec chmod u=r,g=r,o= '{}' +; \
	cp -T /build_context/entrypoint-extension.sh /docker-entrypoint.d/99-entrypoint-extension.sh; \
	sed -i 's/exec "$@"/exec gosu nginx "$@";/' /docker-entrypoint.sh; \
	chown -hR root:nginx /docker-entrypoint.d; \
	find /docker-entrypoint.d -type d -exec chmod u=rx,g=rx,o= '{}' +; \
	find /docker-entrypoint.d -type f -exec chmod u=rx,g=rx,o= '{}' +; \
	addgroup -g 33 www-data-from-app; \
	addgroup nginx www-data-from-app;
