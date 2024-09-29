FROM node:20.17.0-alpine3.20

SHELL [ "/usr/bin/env", "ash", "-eu", "-c" ]

ARG NORMAL_UID_GID
RUN adduser -D -u ${NORMAL_UID_GID} normal;

USER normal
WORKDIR /var/www
