<?php
/*
 * Plugin Name:     Hello World
 * Description:     A really barebones implementation of a plugin.
 * Version:         0.0.1
 * Author:          kimdcottrell
 * Author URI:      https://example.com
 * Text Domain:     kdc-hello-world
 */

add_action( 'admin_menu', 'kdc_hello_world_add_admin_menu' );

function kdc_hello_world_add_admin_menu()
{
      add_menu_page(
        'Hello World Page', // Title of the page
        'Hello World Plugin', // Text to show on the menu link
        'manage_options', // Capability requirement to see the link
        plugin_dir_path( __FILE__ ) . 'includes/index.php' // The 'slug' - file to display when clicking the link
    );
}
