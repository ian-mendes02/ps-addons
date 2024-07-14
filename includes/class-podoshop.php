<?php

/**
 * Core plugin class.
 * @package    Podoshop
 * @subpackage Podoshop/Classes
 *
 * @author     Ian Mendes <ianlucamendes02@gmail.com>
 *
 * @link       https://podoshop.com.br
 * @since      1.0.0
 */
final class Podoshop {
    /**
     * Plugin slug.
     * @since 1.0.0
     */
    private $plugin_slug = 'ps';

    /**
     * Current plugin version.
     * @since 1.0.0
     */
    private $version = '1.0.0';

    /**
     * An instance of the class itself.
     * @since 1.0.0
     */
    private static $instance = null;

    /**
     * Admin functionality.
     * @var PS_Admin
     * @since 1.0.0
     */
    public $admin;

    /**
     * Controller for discount operations.
     * @var PS_Discount_Manager
     * @since 1.0.0
     */
    public $discount_manager;

    /**
     * Ensures a single instance of the plugin class is loaded.
     * @since 1.0.0
     */
    public static function instance() {
        if ( is_null( self::$instance ) ) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    private function __construct() {
        $this->define_constants();
        $this->include();
        $this->init_hooks();
    }

    /**
     * Defines plugin constants.
     * @since 1.0.0
     */
    private function define_constants() {
        $this->define( 'PS_ABSPATH', plugin_dir_path( PS_PLUGIN_FILE ) );
        $this->define( 'PS_PLUGIN_URL', plugin_dir_url( PS_PLUGIN_FILE ) );
        $this->define( 'PS_PLUGIN_BASENAME', plugin_basename( PS_PLUGIN_FILE ) );
        $this->define( 'PS_ASSETS_FOLDER', PS_PLUGIN_URL . 'includes/assets/' );
        $this->define( 'PS_VERSION', $this->version );
        $this->define( 'PS_SLUG', $this->plugin_slug );
    }

    /**
     * Includes core plugin files.
     * @since 1.0.0
     */
    private function include() {
        include_once PS_ABSPATH . 'includes/ps-functions.php';
        include_once PS_ABSPATH . 'includes/class-ps-ajax.php';
        include_once PS_ABSPATH . 'includes/class-ps-loader.php';
        include_once PS_ABSPATH . 'includes/ps-user-functions.php';
        include_once PS_ABSPATH . 'includes/class-ps-discount.php';
        include_once PS_ABSPATH . 'includes/class-ps-customer.php';
        include_once PS_ABSPATH . 'includes/admin/class-ps-admin.php';
        include_once PS_ABSPATH . 'includes/class-ps-discount-manager.php';
    }

    /**
     * Initializes plugin hooks.
     * @since 1.0.0
     */
    private function init_hooks() {

        add_action( 'wp_enqueue_scripts', ['PS_Loader', 'load_scripts'] );
        add_action( 'wp_enqueue_scripts', ['PS_Loader', 'load_styles'] );

        // These classes set up hooks on instantiation
        $this->admin = PS_Admin::instance();
        $this->discount_manager = PS_Discount_Manager::instance();
        PS_Ajax::instance();
    }

    /**
     * Defines a constant if not already set.
     * @param string      $name  Constant name.
     * @param string|bool $value Constant value.
     */
    private function define( $name, $value ) {
        if ( ! defined( $name ) ) {
            define( $name, $value );
        }
    }

}
