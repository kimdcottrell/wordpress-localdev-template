<?php
define('TEXT_DOMAIN', 'kdc-ttfour');

add_action( 'wp_enqueue_scripts', function() {
    wp_enqueue_style( 
        TEXT_DOMAIN . '-style', 
        get_stylesheet_uri()
    );

    // add in the parent stylesheet
    wp_enqueue_style( 
        TEXT_DOMAIN . '-parent-style', 
        get_parent_theme_file_uri( 'style.css' )
    );
});


