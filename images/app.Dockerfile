# syntax=docker/dockerfile:1
ARG  LOCAL_PHP_VERSION=8.3
FROM wordpress:php${LOCAL_PHP_VERSION}-fpm AS base

FROM base AS dev

# grab all the executables from node's image. 
# this is faster than downloading and installing everything.
COPY --from=node:23-slim /usr/local/bin /usr/local/bin
COPY --from=node:23-slim /usr/local/lib/node_modules /usr/local/lib/node_modules
COPY --from=node:23-slim /opt /opt

# creating a new group inside the container so group_add can be used in the dockerfile.
# this group will exist in all containers, and will be the same group as the user editing code on the local machine.
# this makes it so the containers and local machine can all play nice with each other.
ENV LOCAL_MACHINE_GID=1000
RUN groupmod --gid 1000 www-data

# this exists since if you're not running Docker Desktop, certain folders will change to 1033:1033 on your local machine,
# rendering them unwritable to the local machine user.
ENV LOCAL_MACHINE_UID=1000
RUN usermod --gid 1000 --uid 1000 www-data

RUN <<"BASHRC" cat >> /root/.bashrc 

node_modules_bins=$(
    # add all node_modules in all project-prefixed themes and plugins
    find /var/www/html/wp-content/*/${PROJECT_PREFIX}-*/node_modules -maxdepth 0 -type d \
        | sed 's_$_/.bin_' \
        | tr '\n' ':' \
        | sed 's/:$//'
)

# also add in the composer bin dir incase it's ever used
export PATH="${PATH}:${node_modules_bins}:/var/www/vendor/bin"

# can be used to easily add in a new path while container is up.
# handy for testing things.
set_path(){
    for i in "$@";
    do
        # Check if the directory exists
        [ -d "$i" ] || continue

        # Check if it is not already in your $PATH.
        echo "$PATH" | grep -Eq "(^|:)$i(:|$)" && continue

        # Then append it to $PATH and export it
        export PATH="${PATH}:$i"
    done
}

BASHRC
