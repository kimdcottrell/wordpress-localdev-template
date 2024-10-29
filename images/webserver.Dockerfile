

# syntax=docker/dockerfile:1
FROM nginx:latest AS base

FROM base AS dev

# creating a new group inside the container so group_add can be used in the dockerfile.
# this group will exist in all containers, and will be the same group as the user editing code on the local machine.
# we're also creating a new user 
# this makes it so the containers and local machine can all play nice with each other.
ENV LOCAL_MACHINE_GID=1000
RUN groupmod --gid 1000 www-data

# this exists since if you're not running Docker Desktop, certain folders will change to 1033:1033 on your local machine,
# rendering them unwritable to the local machine user.
ENV LOCAL_MACHINE_UID=1000
RUN usermod --gid 1000 --uid 1000 www-data
