FROM ubuntu:22.04 AS common

SHELL [ "/usr/bin/env", "bash", "-euo", "pipefail", "-O", "extglob", "-c" ]

ENV DEBIAN_FRONTEND=noninteractive

ARG PHP_VERSION=8.3
ARG PHP_FPM_INI_DIR=/etc/php/${PHP_VERSION}/fpm
ARG PHP_CLI_INI_DIR=/etc/php/${PHP_VERSION}/cli

RUN --mount=type=cache,target=/var/cache/apt,sharing=private \
	--mount=type=cache,target=/var/lib/apt,sharing=private \
	rm -f /etc/apt/apt.conf.d/docker-clean; \
	echo 'Binary::apt::APT::Keep-Downloaded-Packages "true";' > /etc/apt/apt.conf.d/keep-cache; \
	apt-get update --quiet --quiet; \
	apt-get install --no-install-recommends --quiet --quiet \
		curl \
		ca-certificates \
		unzip \
		gpg-agent \
		software-properties-common \
		gosu; \
	add-apt-repository ppa:ondrej/php;
# Die Warnung bei add-apt-repository ppa:ondrej/php kann man ignorieren.

RUN --mount=type=cache,target=/var/cache/apt,sharing=private \
	--mount=type=cache,target=/var/lib/apt,sharing=private \
	apt-get update --quiet --quiet; \
	apt-get install --no-install-recommends --quiet --quiet \
		php${PHP_VERSION} \
		php${PHP_VERSION}-cli \
		php${PHP_VERSION}-fpm \
		php${PHP_VERSION}-mbstring \
		php${PHP_VERSION}-curl \
		php${PHP_VERSION}-zip \
		php${PHP_VERSION}-dom \
		php${PHP_VERSION}-xml \
		php${PHP_VERSION}-mysql \
		php${PHP_VERSION}-opcache \
		php${PHP_VERSION}-xdebug \
		php${PHP_VERSION}-gd;

RUN mkdir -p /run/php/fpm/sock; \
	chown -h -R www-data:www-data /run/php; \
	ln -s "/usr/sbin/php-fpm${PHP_VERSION}" /usr/local/sbin/php-fpm;

RUN --mount=source=composer,target=/build_context \
	--mount=type=cache,target=/var/cache/getcomposer,sharing=private \
	cd /var/cache/getcomposer; \
	if test -f composer.phar; then \
		echo "aus Cache von RUN --mount=cache wiedergeladen" > ~/getcomposer_cache.log; \
	else \
		curl -L -o composer.phar https://getcomposer.org/download/2.7.8/composer.phar; \
		echo "neu heruntergeladen" > ~/getcomposer_cache.log; \
	fi; \
	sha256sum -c /build_context/composer.phar.sha256sum; \
	cp composer.phar /usr/local/sbin/composer; \
	chmod 0555 /usr/local/sbin/composer;

RUN --mount=type=cache,target=/var/cache/apt,sharing=private \
	--mount=type=cache,target=/var/lib/apt,sharing=private \
	apt-get update --quiet --quiet; \
	apt-get install --no-install-recommends --quiet --quiet \
		python3 \
		python3-venv; \
	mkdir -p /var/python; \
	chown -R www-data:www-data /var/python;
USER www-data
RUN --mount=type=cache,target=/var/cache/pip,gid=33,uid=33,sharing=private \
    --mount=from=python,target=/var/python/src \
	python3 -m venv /var/python; \
	cd /var/python; \
	source bin/activate; \
	pip --cache-dir=/var/cache/pip install -r src/requirements.txt;
USER root

ARG NORMAL_UID_GID
RUN useradd -m -U -u ${NORMAL_UID_GID} normal; \
	gpasswd -a normal www-data; 
	
RUN --mount=source=settings,target=/build_context \
	cp /build_context/30-settings-fpm.ini ${PHP_FPM_INI_DIR}/conf.d; \
	cp /build_context/30-settings-cli.ini ${PHP_CLI_INI_DIR}/conf.d; \
	cp /build_context/php-fpm.conf ${PHP_FPM_INI_DIR}; \
	cp /build_context/www.conf ${PHP_FPM_INI_DIR}/pool.d; \
	cp /build_context/docker-entrypoint.bash /; \
	chmod 0755 /docker-entrypoint.bash;

# USER muss noch root sein
WORKDIR /var/www
ENTRYPOINT ["/docker-entrypoint.bash"]

FROM common AS dev
CMD ["php-fpm"]

FROM common AS prod
CMD ["php-fpm"]

FROM common AS prod-ssr
RUN --mount=source=settings,target=/build_context \
	cp /build_context/prod-ssr.bash /; \
	chmod 0755 /prod-ssr.bash;

ENV NVM_DIR="/usr/local/sbin/.nvm";
RUN \
	mkdir -p "$NVM_DIR"; \
	curl -o- https://raw.githubusercontent.com/nvm-sh/nvm/v0.40.1/install.sh | bash; \
	source "$NVM_DIR/nvm.sh"; \
	nvm install 20.17.0;

CMD ["/prod-ssr.bash"]
