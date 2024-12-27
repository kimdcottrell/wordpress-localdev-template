# syntax=docker/dockerfile:1
ARG  LOCAL_PHP_VERSION=8.3
FROM wordpress:php${LOCAL_PHP_VERSION}-fpm AS dev
ENV LOCAL_PHP_VERSION=${LOCAL_PHP_VERSION}

ENV LOCAL_MACHINE_GID=1000
ENV LOCAL_MACHINE_UID=1000
RUN usermod -u ${LOCAL_MACHINE_UID} -o www-data && groupmod -g ${LOCAL_MACHINE_GID} -o www-data

# if we copy this over directly, we replace the wordpress image's docker-entrypoint
COPY --from=node:23-slim /usr/local/bin /tmp/usr/local/bin
COPY --from=node:23-slim /usr/local/lib/node_modules /usr/local/lib/node_modules
COPY --from=node:23-slim /opt /opt

# move over all the node executables without the docker-entrypoint.sh from the node image
# fix all the permissions issues
RUN rm -f /tmp/usr/local/bin/docker-entrypoint.sh; \
    cp -r /tmp/usr/local/bin /usr/local; \
    mkdir -p /home/www-data; \
    chown -R www-data /home/www-data; \
    chown -R www-data /var

USER www-data

# add both the node_modules bin and the composer vendor bin dirs to path so the executables work how you'd expect
ENV PATH="${PATH}:/var/www/vendor/bin:/var/www/html/wp-content/themes/kdc-twentytwentyfour/node_modules/.bin"

EXPOSE 9000
