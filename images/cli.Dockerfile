

# syntax=docker/dockerfile:1
ARG  LOCAL_PHP_VERSION=8.3
FROM wordpress:cli-php${LOCAL_PHP_VERSION} AS base

FROM base AS dev

# creating a new group inside the container so group_add can be used in the dockerfile.
# this group will exist in all containers, and will be the same group as the user editing code on the local machine.
# this makes it so the containers and local machine can all play nice with each other.
USER root
ENV LOCAL_MACHINE_GID=1000
RUN addgroup -g ${LOCAL_MACHINE_GID} dev

# swap back to the user used in base image
USER www-data