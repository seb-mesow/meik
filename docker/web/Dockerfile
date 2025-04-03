FROM nginxinc/nginx-unprivileged:1.26.2-alpine3.20-slim AS common

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
	mkdir /run/nginx; \
	chown -hR root:nginx /run/nginx; \
	chmod u=rwx,g=rwx,o= /run/nginx; \
	\
	cp -T /build_context/entrypoint-extension.sh /docker-entrypoint.d/99-entrypoint-extension.sh; \
	sed -i 's/exec "$@"/exec gosu nginx "$@";/' /docker-entrypoint.sh; \
	chown -hR root:nginx /docker-entrypoint.d; \
	find /docker-entrypoint.d -type d -exec chmod u=rx,g=rx,o= '{}' +; \
	find /docker-entrypoint.d -type f -exec chmod u=rx,g=rx,o= '{}' +; \
	\
	addgroup -g 33 www-data-from-app; \
	addgroup nginx www-data-from-app;

FROM common AS dev
RUN --mount=source=.,target=/build_context \
	cp /build_context/nginx.dev.conf /etc/nginx/nginx.conf; \
	cp /build_context/server.dev.conf /etc/nginx/conf.d; \
	cp /build_context/presentation.dev.conf /etc/nginx/conf.d; \
	chown -hR root:nginx /etc/nginx; \
	find /etc/nginx -type d -exec chmod u=rx,g=rx,o= '{}' +; \
	find /etc/nginx -type f -exec chmod u=r,g=r,o= '{}' +;

FROM common AS prod
ARG DOMAIN_NAME=''
ARG PRIVATE_KEY_FILENAME=''
ARG CERTIFICATE_FILENAME=''
RUN --mount=source=.,target=/build_context \
	--mount=type=cache,target=/var/cache/apk,sharing=private \
	apk --no-interactive --quiet add \
		gettext \
	; \
	\
	envsubst '\$DOMAIN_NAME,\$PRIVATE_KEY_FILENAME,\$CERTIFICATE_FILENAME' \
	< /build_context/server.prod.conf \
	> /etc/nginx/conf.d/server.prod.conf; \
	\
	envsubst '\$DOMAIN_NAME,\$PRIVATE_KEY_FILENAME,\$CERTIFICATE_FILENAME' \
	< /build_context/presentation.prod.conf \
	> /etc/nginx/conf.d/presentation.prod.conf; \
	\
	apk --no-interactive --quiet del \
		gettext \
	; \
	\
	cp /build_context/nginx.prod.conf /etc/nginx/nginx.conf; \
	cp /build_context/fastcgi_pass.conf /etc/nginx/fastcgi_pass.conf; \
	cp /build_context/fastcgi_params.conf /etc/nginx/fastcgi_params; \
	\
	chown -hR root:nginx /etc/nginx; \
	find /etc/nginx -type d -exec chmod u=rx,g=rx,o= '{}' +; \
	find /etc/nginx -type f -exec chmod u=r,g=r,o= '{}' +;
