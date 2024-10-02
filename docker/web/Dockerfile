FROM nginxinc/nginx-unprivileged:1.26.2-alpine3.20-slim

SHELL [ "/usr/bin/env", "ash", "-eu", "-c" ]

USER root

RUN --mount=source=.,target=/build_context \
	apk --no-interactive --quiet update; \
	apk --no-interactive --quiet add sudo; \
	sed -i -e 's/# %wheel ALL=(ALL:ALL) ALL/%wheel ALL=(ALL:ALL) ALL/' /etc/sudoers; \
	addgroup -g 33 www-data-from-app; \
	addgroup nginx www-data-from-app; \
	echo -ne 'nginx\nnginx\n' | passwd nginx; \
	addgroup nginx wheel; \
	cp /build_context/default.conf /etc/nginx/conf.d; \
	chown -R nginx:nginx /etc/nginx; \
	cp -T /build_context/entrypoint-extension.sh /docker-entrypoint.d/99-entrypoint-extension.sh; \
	chown -R nginx:nginx /docker-entrypoint.d; \
	chmod -R 0500 /docker-entrypoint.d;

USER nginx
