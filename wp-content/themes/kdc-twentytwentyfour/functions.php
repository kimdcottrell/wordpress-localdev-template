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
	function () {
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
	}
);
