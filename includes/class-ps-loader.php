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
     * An array of scripts to be enqueued.
     * @var array $scripts
     * @since 1.0.0
     */
    private $scripts;

    /**
     * An array of stylesheets to be enqueued.
     * @var array $styles
     * @since 1.0.0
     */
    private $styles;

    /**
     * Refers to an instance of the class itself.
     * @var PS_Loader
     * @since 1.0.0
     */
    protected static $instance = null;

    /**
     * Ensures only one instance of this class is loaded.
     * @since 1.0.0
     *
     * @return PS_Loader
     */
    public static function instance() {
        if ( null === self::$instance ) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    private function __construct() {
        $this->scripts = [];
        $this->styles  = [];
        $this->enqueue_script( 'ps-components' );
        $this->init();
    }

    /**
     * Adds a new script to the `$scripts` array to be included on load.
     * @since 1.0.0
     *
     * @param string           $slug    Basename of script, will be used as script slug.
     * @param array            $deps    List of script dependencies, uses jQuery as default.
     * @param string|bool|null $version Current file version, defaults to plugin version.
     * @param array            $args    Parameters to be passed to the script.
     */
    public function enqueue_script( $slug, $deps = ['jquery'] ) {
        array_push( $this->scripts, [
            'handle'  => str_replace( '-', '_', $slug ),
            'src'     => PS_ABSPATH . 'assets/js/' . $slug . '.js',
            'version' => PS_VERSION,
            'deps'    => $deps,
        ] );
    }

    /**
     * Adds a new stylesheet to the `$styles` array to be included on load.
     * @since 1.0.0
     *
     * @param string           $slug    Basename of css file, will be used as stylesheet slug.
     * @param array            $deps    List of stylesheet dependencies.
     * @param string|bool|null $version Current file version, defaults to plugin version.
     * @param string|array     $media   Media type targeted by the stylesheet.
     */
    public function enqueue_style( $slug, $deps = [] ) {
        array_push( $this->styles, [
            'handle'  => $slug,
            'src'     => PS_ABSPATH . 'assets/css/' . $slug . '.css',
            'version' => PS_VERSION,
            'deps'    => $deps,
            'media'   => 'screen',
        ] );
    }

    /**
     * Enqueues collected styles.
     * @since 1.0.0
     */
    public function load_styles() {
        foreach ( $this->styles as $style ) {
            wp_enqueue_style( $style['handle'], $style['src'], $style['deps'], $style['media'] );
        }
    }

    /**
     * Enqueues collected scripts.
     * @since 1.0.0
     */
    public function load_scripts() {
        foreach ( $this->scripts as $script ) {
            wp_enqueue_script( $script['handle'], $script['src'], $script['deps'], $script['version'], true );
        }
    }

    /**
     * Registers queued hooks.
     * @since 1.0.0
     */
    public function init() {
        add_action( 'wp_enqueue_scripts', [$this, 'load_scripts'] );
        add_action( 'wp_enqueue_styles', [$this, 'load_styles'] );
    }
}
