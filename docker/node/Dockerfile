FROM node:20.17.0-alpine3.20 AS prod

SHELL [ "/usr/bin/env", "ash", "-eu", "-c" ]

ARG NORMAL_UID_GID
RUN deluser --remove-home node;
RUN adduser -D -u ${NORMAL_UID_GID} normal;
RUN adduser -D -H -u 33 www-data-from-app;
RUN addgroup normal www-data-from-app;

USER normal
WORKDIR /var/www
ENTRYPOINT []
CMD []
# prod: Assets einmalig kompilieren NACH php artisan ziggy:generate --types
# prod-srr: Hier wird der node-Container gar nicht benutzt.

FROM prod AS dev
CMD ["npm", "run", "dev"]
# dev: andauernden Vite Dev Server starten
