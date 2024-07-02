<?php

/**
 * Registers admin menus and pages.
 * @package    Podoshop
 * @subpackage Podoshop/Admin
 *
 * @author     Ian Mendes <ianlucamendes02@gmail.com>
 *
 * @link       https://podoshop.com.br
 * @since      1.0.0
 */
class PS_Admin {
    /**
     * Refers to an instance of the class itself.
     * @var PS_Admin
     * @since 1.0.0
     */
    protected static $instance = null;

    /**
     * Admin page components.
     * Each component will include its
     * respective page on instantiation.
     * @var array
     * @since 1.0.0
     */
    protected $page_components;

    /**
     * Instantiated component classes
     * @var array
     * @since 1.0.0
     */
    protected $components;

    /**
     * Registers static admin assets.
     * @since 1.0.0
     */
    public function enqueue_assets() {        
        // Admin css
        wp_enqueue_style( 'ps-admin', PS_ASSETS_FOLDER . 'css/admin.css', [], PS_VERSION, 'all' );
    }

    /**
     * Ensures only one instance of this class is loaded.
     * @since 1.0.0
     *
     * @return PS_Admin
     */
    public static function instance() {
        if ( null === self::$instance ) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    private function __construct() {
        $this->components      = [];
        $this->page_components = [
            'PS_Admin_Dashboard'       => 'dashboard',
            'PS_Admin_Settings'        => 'settings',
            'PS_Admin_Discounts'       => 'discounts',
        ];
        $this->include_admin_components();
        $this->init_admin_hooks();
    }

    /**
     * Includes admin pages.
     * @since 1.0.0
     */
    private function include_admin_components() {
        include_once PS_ABSPATH . 'includes/admin/abstract-class-ps-admin-page.php';
        foreach ( $this->page_components as $component => $slug ) {
            include_once PS_ABSPATH . "includes/admin/class-ps-admin-$slug.php";
            $ref = new ReflectionClass( $component );

            $this->components[] = $ref->newInstanceArgs( [$slug] );
        }
    }

    /**
     * Registers admin menus and pages.
     * @since 1.0.0
     */
    public function register_admin_menus() {
        $shop_icon = 'data:image/svg+xml;base64,' . base64_encode( "<svg width='20' height='20' xmlns='http://www.w3.org/2000/svg' viewBox='0 0 16 16'><path fill='rgba(240,246,252,.6)' d='M14.8,8c0,3.7-3,6.8-6.8,6.8c-0.4,0-0.8,0-1.1-0.1v-3.5c0.4,0.1,0.7,0.2,1.1,0.2c1.9,0,3.4-1.5,3.4-3.4S9.9,4.6,8,4.6S4.6,6.1,4.6,8v6.8H1.2V8c0-3.7,3-6.8,6.8-6.8C11.7,1.2,14.8,4.3,14.8,8'/></svg>" );

        add_menu_page( '', 'Podoshop', 'manage_options', 'ps_admin', 'ps_admin_dashboard', $shop_icon, 7 );

        foreach ( $this->components as $component ) {
            add_submenu_page(
                $component->parent_slug,
                $component->page_title,
                $component->menu_title,
                $component->capability,
                $component->menu_slug,
                [$component, 'get_template'],
                $component->position
            );
        }

        remove_submenu_page( 'ps_admin', 'ps_admin' );
    }

    /**
     * Hooks in admin methods.
     * @since 1.0.0
     */
    private function init_admin_hooks() {
        add_action( 'admin_enqueue_scripts', [$this, 'enqueue_assets'] );
        add_action( 'admin_menu', [$this, 'register_admin_menus'] );
    }
}
