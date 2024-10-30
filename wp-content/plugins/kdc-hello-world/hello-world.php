<?php
/*
 * Plugin Name:     Hello World
 * Description:     A really barebones implementation of a plugin.
 * Version:         0.0.1
 * Author:          kimdcottrell
 * Author URI:      https://example.com
 * Text Domain:     kdc-hello-world
 */

add_action( 'admin_menu', function() {
    add_menu_page(
        'Hello World Page', // Title of the page
        'Hello World Plugin', // Text to show on the menu link
        'manage_options', // Capability requirement to see the link
        plugin_dir_path( __FILE__ ) . 'includes/index.php' // The 'slug' - file to display when clicking the link
    );
});

add_action('init', function() {
	register_post_type('landing_page', [
		'label' => __('Landing page', 'txtdomain'),
		'public' => true,
		'menu_position' => 5,
		'menu_icon' => 'dashicons-groups', # https://developer.wordpress.org/resource/dashicons/#editor-paste-text
		'supports' => ['title', 'editor', 'thumbnail', 'author', 'revisions', 'comments'],
		'show_in_rest' => true, # turns on block editor support
		'rewrite' => ['slug' => 'landing-page'],
		'taxonomies' => ['landing_page_audience'], # declare the relationship part 1/3
		'labels' => [
			'singular_name' => __('Landing Page', 'txtdomain'),
            'add_new' => __('Add New Landing Page', 'txtdomain'),
			'add_new_item' => __('Add New Landing Page', 'txtdomain'),
			'new_item' => __('New Landing Page', 'txtdomain'),
			'view_item' => __('View Landing Page', 'txtdomain'),
			'not_found' => __('No Landing Pages Found', 'txtdomain'),
			'not_found_in_trash' => __('No Landing Pages Found in Trash', 'txtdomain'),
			'all_items' => __('All Landing Pages', 'txtdomain'),
			'insert_into_item' => __('Insert Into Landing Page', 'txtdomain')
		],		
	]);
 
	register_taxonomy('landing_page_audience', ['landing_page'], [ # declare the relationship part 2/3
		'label' => __('Audience', 'txtdomain'),
		'hierarchical' => true,
		'rewrite' => ['slug' => 'landing-page-audience'],
		'show_admin_column' => true,
		'show_in_rest' => true, # turns on block editor support
		'labels' => [
			'singular_name' => __('Audience', 'txtdomain'),
			'all_items' => __('All Audience', 'txtdomain'),
			'edit_item' => __('Edit Audience', 'txtdomain'),
			'view_item' => __('View Audience', 'txtdomain'),
			'update_item' => __('Update Audience', 'txtdomain'),
			'add_new_item' => __('Add New Audience', 'txtdomain'),
			'new_item_name' => __('New Audience Name', 'txtdomain'),
			'search_items' => __('Search Audiences', 'txtdomain'),
			'parent_item' => __('Parent Audience', 'txtdomain'),
			'parent_item_colon' => __('Parent Audience:', 'txtdomain'),
			'not_found' => __('No Audiences found', 'txtdomain'),
		]
	]);
	register_taxonomy_for_object_type('landing_page_audience', 'landing_page'); # declare the relationship part 3/3
    # yes, you really should declare that three times. https://developer.wordpress.org/reference/functions/register_taxonomy/#additional-parameter-information
 
});

