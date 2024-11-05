# A Localdev: WordPress 6, PHP 8, MySQL/MariaDB, Composer, WP CLI

Why does this exist?

I couldn't find a docker-compose.yml-based localdev that had these technologies correctly working together. 

This setup - one where you're forced to edit `wp-content` as a monolith - is the one a lot of WordPress-specific hosts prefer, as they want to manage WordPress for you. I wanted a localdev that would better mirror that setup.

## How to get started

### Setup localdev proxy 

Install the Traefik reverse proxy: https://kimdcottrell.com/posts/5-steps-to-achieving-https-and-domain-names-for-docker-local-development-envs/ It is what enables you to visit the resulting application as something like https://test.local.dev in your browser.

### Setup this application

```
# Ensure that you're running localdev-proxy
cd ~/path/to/localdev-proxy
docker compose up -d
docker ps -a # you should see the containers up and running

# Setup your desired url in /etc/hosts
sudo vim /etc/hosts
# 127.0.0.1 test.local.dev
# these site names should end in .local.dev or whatever you set your certs to support

# Clone it into a folder of your choice
git clone git@github.com:kimdcottrell/wordpress-localdev-template.git ./your-folder-here

# Copy the .env.sample into a real .env
cp .env.sample .env
# make sure the same entry in /etc/hosts is set as the value for SERVER_NAME 
# Take a minute to review what is in the .env and create your own salts

# Finally, run a command in the Makefile
make init
```

You should now be able to visit something like https://test.local.dev in your browser and see a WordPress instance using the TwentyTwentyFour theme.

#### If you mean to turn this into a project

Remove this line in your `.gitignore`:

```
# we don't need composer.lock for this since this is a template
composer.lock
```

Uncomment this line in your `docker-compose.yml`:

```
# - ./composer.lock:/var/www/composer.lock # uncomment this when this is no longer a template
```

Grep for all instances of `kdc-` and change them to whatever you want. That is just an identifier to help stop your custom plugins and themes from being ignored in git - the identifier can be whatever.

Review the files under `tools/local-env/git-hooks/*` and see if you want to replace the files of the same name in `.git/hooks/*`. They contain things like an automatic linter on pre-commit and such.

## How to use it

Everything is editable. This projects expects you to run WordPress as a single-site, not a multisite. It expects that the resulting project will be a monolith in terms of code architecture. 

You can expand on the Dockerfiles in `./images.` The are intended to be multi-stage, with future staging or production stages if you need. `dev` is only supposed to be used for local development, and never in a remote, publically-accessible env.

The containers are super lightweight right now. I figured it's easier to start with tooling separated from the container than the other way around. 

The containers that should continue running after `docker compose up` are `webserver`, `php`, and `db`.

This means that you can access them as they are running as something like:

```
# for an interactive container
docker compose exec php bash

# for a one liner that returns you back to the host machine
# btw, this is how you'd run any tooling executable you install through composer
docker compose exec php bash -c "../vendor/bin/phpcbf --standard=WordPress wp-content/*/kdc-*" 
```

The containers that should stop after `docker compose up` are `composer` and `cli`. All that `docker compose up` is doing in this situation is building them so they exist in the docker cache when you run them later.

This structure means that workflows that use `composer` or `wp cli` should run the commands as something like:

```
# --rm will clean up the container after the command runs
docker compose run --rm cli wp theme list
docker compose run --rm composer help
```

The tooling containers are mounted in a way that the results of their commands are picked up by the others. 

If use of the tooling container is too verbose for your liking, consider creating an alias in your `~/.bashrc` or adding to the list of commands in the `Makefile`.

## Explanations of development choices

### wp-config.php is missing

The `wordpress` docker image will create one for you as long as you pass in the configuration items as env vars. This does **not** apply to _all_ env vars. 

Read more here:

- https://hub.docker.com/_/wordpress
- https://github.com/docker-library/wordpress/blob/aa3c30f8c0a6a5ba0e1b26f73be802dfc8f18e4f/latest/php8.3/fpm/wp-config-docker.php

### Manging plugins, mu-plugins, and themes though composer

If you're a layperson with a single WP env, and that env is production, having WP manage the updates to your themes and plugins is a good idea. Once you have multiple team members managing a site and multple envs, that changes drastically. 

You can install normal composer packages from https://packagist.org and WP-specific ones from https://wpackagist.org/

### Core WordPress as a volume

You will see that WordPress itself is missing from your filesystem. That is done on purpose - it is held in a volume for persistence instead. WordPress is not supposed to be directly modified, but rather, files under `wp-content` are to be edited. Few webhosts allow you to edit WordPress core files, and this localdev mirrors that functionality. 

### Composer vendor as a volume

Just like WordPress as a volume, `./vendor` is never expected to be editable on your machine. So I've made it a volume. 

### nginx.conf.template mysteriously accepts env vars 

That's the magic of its docker image.

> Using environment variables in nginx configuration (new in 1.19)
>
> Out-of-the-box, nginx doesn't support environment variables inside most configuration blocks. But this image has a function, which will extract environment variables before nginx starts.
>
> By default, this function reads template files in /etc/nginx/templates/*.template and outputs the result of executing envsubst to /etc/nginx/conf.d.

- https://hub.docker.com/_/nginx/

### MySQL/MariaDB persistence

`mysql-init.sql` will fire ONLY once, and that is after the volume is built during `docker compose up`. This allows you to persist the database despite `docker compose down` or `docker compose restart` until you want to fully destroy it via something like `docker compose down --volumes`. 

> Initializing a fresh instance
> When a container is started for the first time, a new database with the specified name will be created and initialized with the provided configuration variables. Furthermore, it will execute files with extensions .sh, .sql and .sql.gz that are found in /docker-entrypoint-initdb.d. Files will be executed in alphabetical order. You can easily populate your mysql services by mounting a SQL dump into that directory⁠ and provide custom images⁠ with contributed data. SQL files will be imported by default to the database specified by the MYSQL_DATABASE variable.

- https://hub.docker.com/_/mysql/ (this behavior is identical for MariaDB's image as well)

### Why not run git in a container?

Docker containers require you to mount local directories into your containers. Most implementations of `git` require ssh. That means that in order to get `git` to work within a container, you have to mount your `~/.ssh` folder, or at least specific files within it.

Containers run as usually alpine or debian, not OSX or Linux. `~/.ssh/config` may have syntax that specifically only works in your operating system.

Docker containers get really weird about permissions depending on the implementation - e.g. Docker Engine vs Docker Desktop on Linux. SSH is also really restricted in the permissions department, requiring `600` and `644` for the private keys and public keys respectively. 

This all means that you have to do at least the following:

- Have Docker run at the same level as the user who can access the files within an `~/.ssh` dir
- Mount in specifically the private and public keys needed by git
- Write a specific ssh config for your container
- Write a specific authorized_keys for your container
- Set the mounted files to read-only, lest you destroy your private/public keys accidentally 

I have found that trying to manage all the above across a team only means that something _will_ go wrong, either thanks to a miconfiguration as someone tries to correct for issues that arise, or because Docker Desktop glitches. I have seen people delete their ssh configs and authorized_keys accidentally. I have seen keys turn into blank files.  

**I do not recommend mounting any part of your `~/.ssh` folder into a container** because of such things. 
