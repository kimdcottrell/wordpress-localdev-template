<?php
# wrapping this in a class only to avoid the spam of `function kdc_ffour_` on every function declaration
class KDC_Functions {
    const TEXT_DOMAIN   = 'kdc-ttfour';
    const TEXT_DOMAIN_U = 'kdc_ttfour';

    public function __construct() {
        // Do nothing.
    }

    public function initialize(){
        add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_styles' ) );
    }

    public function enqueue_styles() {
        wp_enqueue_style( 
            self::TEXT_DOMAIN . '-style', 
            get_stylesheet_uri()
        );
    
        // add in the parent stylesheet
        wp_enqueue_style( 
            self::TEXT_DOMAIN . '-parent-style', 
            get_parent_theme_file_uri( 'style.css' )
        );
    }
}

$Functions = new KDC_Functions();
$Functions->initialize();


