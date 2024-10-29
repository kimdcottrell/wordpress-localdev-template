# syntax=docker/dockerfile:1
ARG  LOCAL_PHP_VERSION=8.3
FROM wordpress:php${LOCAL_PHP_VERSION}-fpm AS base

FROM base AS dev

# creating a new group inside the container so group_add can be used in the dockerfile.
# this group will exist in all containers, and will be the same group as the user editing code on the local machine.
# this makes it so the containers and local machine can all play nice with each other.
ENV LOCAL_MACHINE_GID=1000
RUN groupadd -g ${LOCAL_MACHINE_GID} dev