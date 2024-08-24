FROM nginx:1.26.2-alpine3.20-slim

RUN --mount=source=.,target=/build_context \
    cp /build_context/default.conf /etc/nginx/conf.d/default.conf

# CMD [ "nginx", "-g", "daemon off;" ]
# CMD [ "tail", "-f", "/dev/null" ]
