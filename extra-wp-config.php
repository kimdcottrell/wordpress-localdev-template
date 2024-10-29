<?php
###
# So you can do this in docker-compose.yml:
#
# php:
#   image: wordpress:php${LOCAL_PHP_VERSION-8.3}-fpm
#   env_file: .env
#
# environment:
#   WORDPRESS_CONFIG_EXTRA: |
#     define( 'SCRIPT_DEBUG', true );
#     define( 'WP_DEBUG_LOG', true );
#
#
# But maybe you have a lot of configurations, or you want to use envvars in the configs.
# That's why this exists.
###

define( 'SCRIPT_DEBUG', getenv_docker('EXTRA_SCRIPT_DEBUG', true) );
define( 'WP_DEBUG_LOG', getenv_docker('EXTRA_WP_DEBUG_LOG', true) );
define( 'WP_DEBUG_DISPLAY', getenv_docker('EXTRA_WP_DEBUG_DISPLAY', true) );
define( 'WP_HOME', 'https://' . getenv_docker('SERVER_NAME', 'test.local.dev') );
define( 'WP_SITEURL', 'https://' . getenv_docker('SERVER_NAME', 'test.local.dev') );

# we are managing the WordPress install, and plugins. Auto-updates need to be turned off
define( 'FS_METHOD', 'direct' );

# Turn this on so scheduled events like WP_Recovery_Mode::clean_expired_keys() don't fail and throw errors to Tools > Site Health
define( 'DISABLE_WP_CRON', getenv_docker('EXTRA_WP_DEBUG_DISABLE_WP_CRON', false) );
