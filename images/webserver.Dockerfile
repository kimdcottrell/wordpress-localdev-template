

# syntax=docker/dockerfile:1
FROM nginx:latest AS dev

ENV LOCAL_MACHINE_GID=1000
ENV LOCAL_MACHINE_UID=1000
RUN usermod -u ${LOCAL_MACHINE_UID} -o nginx && groupmod -g ${LOCAL_MACHINE_GID} -o nginx

