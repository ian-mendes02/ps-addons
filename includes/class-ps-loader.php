<?php

/**
 * Loads in static assets.
 * @package    Podoshop
 * @subpackage Podoshop/Classes
 *
 * @author     Ian Mendes <ianlucamendes02@gmail.com>
 */
class PS_Loader {
    /**
     * Enqueue public css.
     * @since 1.0.0
     */
    public static function load_styles() {
    }

    /**
     * Enqueue public js.
     * @since 1.0.0
     */
    public static function load_scripts() {
        if ( is_shop() ) {
            wp_enqueue_script( 'ps-public', PS_ASSETS_FOLDER . 'js/ps-public.js', ['jquery'], PS_VERSION, true );
        }
    }
}
