FROM node:20.17.0-alpine3.20

SHELL [ "/usr/bin/env", "ash", "-eu", "-c" ]

ARG NORMAL_UID_GID
RUN deluser --remove-home node;
RUN adduser -D -u ${NORMAL_UID_GID} normal;
RUN adduser -D -H -u 33 www-data-from-app;
RUN addgroup normal www-data-from-app;

USER normal
WORKDIR /var/www
