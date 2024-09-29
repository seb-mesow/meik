FROM ubuntu:22.04

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
        ca-certificates \
        curl \
        gpg-agent \
        software-properties-common; \
    add-apt-repository ppa:ondrej/php;

RUN --mount=type=cache,target=/var/cache/apt,sharing=private \
    --mount=type=cache,target=/var/lib/apt,sharing=private \
    apt-get update --quiet --quiet; \
    apt-get install --no-install-recommends --quiet --quiet \
        unzip \
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
        php${PHP_VERSION}-xdebug;

RUN mkdir -p /run/php/fpm/sock; \
    chown -h -R www-data:www-data /run/php; \
    ln -s "/usr/sbin/php-fpm${PHP_VERSION}" /usr/local/sbin/php-fpm;

# TODO nur temporär
# RUN usermod -aG root www-data

RUN --mount=source=.,target=/build_context \
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

RUN --mount=source=.,target=/build_context \
    cp /build_context/30-settings-fpm.ini ${PHP_FPM_INI_DIR}/conf.d; \
    cp /build_context/30-settings-cli.ini ${PHP_CLI_INI_DIR}/conf.d; \
    cp /build_context/php-fpm.conf ${PHP_FPM_INI_DIR}; \
    cp /build_context/www.conf ${PHP_FPM_INI_DIR}/pool.d;

ARG NORMAL_UID_GID
RUN useradd -m -U -u ${NORMAL_UID_GID} normal; \
    usermod -a -G www-data normal;

USER www-data
WORKDIR /var/www
# CMD [ "bash", "-c", "php-fpm & tail -f /dev/null & wait" ]
CMD ["php-fpm"]
# CMD ["tail", "-f", "/dev/null"]
