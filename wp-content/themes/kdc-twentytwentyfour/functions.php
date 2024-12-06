<?php
/**
 * KDC Twenty Twenty-Four functions and definitions
 *
 * @package KDC Twenty Twenty-Four
 * @since Twenty Twenty-Four 1.0
 */

define( 'TEXT_DOMAIN', 'kdc-ttfour' );

add_action(
	'wp_enqueue_scripts',
	function (): void {
		wp_enqueue_style(
			TEXT_DOMAIN . '-style',
			get_stylesheet_uri(),
			ver: '0.0.1'
		);

		// add in the parent stylesheet.
		wp_enqueue_style(
			TEXT_DOMAIN . '-parent-style',
			get_parent_theme_file_uri( 'style.css' ),
			ver: '0.0.1'
		);

		// add in some sample javascript .
		// check out these guides for more info:
		// https://developer.wordpress.org/block-editor/getting-started/devenv/get-started-with-wp-scripts/ .
		// https://developer.wordpress.org/block-editor/reference-guides/packages/packages-scripts/#start .
		$asset_file = include get_stylesheet_directory() . '/build/index.asset.php';
		wp_enqueue_script(
			TEXT_DOMAIN . '-sample-script',
			get_stylesheet_directory_uri() . '/build/index.js',
			deps: $asset_file['dependencies'],
			ver: $asset_file['version'],
			in_footer: true,
		);
	}
);
