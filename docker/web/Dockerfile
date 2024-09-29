FROM nginxinc/nginx-unprivileged:1.26.2-alpine3.20-slim

SHELL [ "/usr/bin/env", "ash", "-eu", "-c" ]

USER root

RUN --mount=source=.,target=/build_context \
	addgroup -g 33 www-data-from-app; \
	addgroup nginx www-data-from-app; \
	cp /build_context/default.conf /etc/nginx/conf.d/default.conf

USER nginx

# CMD [ "nginx", "-g", "daemon off;" ]
# CMD [ "tail", "-f", "/dev/null" ]
