FROM node:20.17.0-alpine3.20

SHELL [ "/usr/bin/env", "ash", "-eu", "-c" ]

ARG NORMAL_UID_GID
RUN deluser --remove-home node; \
	adduser -D -u ${NORMAL_UID_GID} normal; \
	adduser -D -H -u 33 www-data-from-app; \
	addgroup normal www-data-from-app;

USER normal
WORKDIR /var/www
