ARG UBUNTU_VERSION

FROM ubuntu:${UBUNTU_VERSION}

SHELL [ "/usr/bin/env", "bash", "-euo", "pipefail", "-O", "extglob", "-c" ]

ENV DEBIAN_FRONTEND=noninteractive

ARG PHP_VERSION

RUN --mount=type=cache,target=/var/cache/apt,sharing=locked \
    --mount=type=cache,target=/var/lib/apt,sharing=locked \
    rm -f /etc/apt/apt.conf.d/docker-clean; \
    echo 'Binary::apt::APT::Keep-Downloaded-Packages "true";' > /etc/apt/apt.conf.d/keep-cache; \
    apt-get update --quiet --quiet; \
    apt-get install --no-install-recommends --quiet --quiet \
        ca-certificates \
        curl \
        gpg-agent \
        software-properties-common; \
    add-apt-repository ppa:ondrej/php;

RUN --mount=type=cache,target=/var/cache/apt,sharing=locked \
    --mount=type=cache,target=/var/lib/apt,sharing=locked \
    apt-get update --quiet --quiet; \
    apt-get install --no-install-recommends --quiet --quiet \
        php${PHP_VERSION} \
        php${PHP_VERSION}-cli \
        php${PHP_VERSION}-fpm \
        php${PHP_VERSION}-xdebug;

RUN ln -s "/usr/sbin/php-fpm${PHP_VERSION}" /usr/local/sbin/php-fpm;

WORKDIR /var/www

CMD ["php-fpm"]
