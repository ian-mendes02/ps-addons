<?php

/**
 * Ajax controller for admin requests.
 * @package    Podoshop
 * @subpackage Podoshop/Classes/admin
 *
 * @author     Ian Mendes <ianlucamendes02@gmail.com>
 *
 * @link       https://podoshop.com.br
 * @since      1.0.0
 */

class PS_Ajax {
    /**
     * Refers to an instance of the class itself.
     * @var PS_Ajax
     */
    protected static $instance = null;

    /**
     * Array of hook slugs to be registered.
     * @var array $ajax_hooks
     * @since 1.0.0
     */
    private $ajax_hooks;

    /**
     * Lists WooCommerce products.
     * @since 1.0.0
     */
    public static function get_products( $query = '', $limit = -1 ) {
        $args     = ["limit" => $limit, "like_name" => $query];
        $products = wc_get_products( $args ) ?? [];
        $posts    = [];
        foreach ( $products as $product ) {
            array_push( $posts, [
                "slug" => $product->get_slug(),
                "id"   => $product->get_id(),
                "name" => $product->get_name(),
            ] );
        }
        return $posts;
    }

    /**
     * Ensures only one instance of this class is loaded.
     * @since 1.0.0
     *
     * @return PS_Ajax
     */
    public static function instance() {
        if ( null === self::$instance ) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * Registers loose product title comparison.
     * @since 1.0.0
     */
    public static function product_like_name( $query, $query_vars ) {
        if ( isset( $query_vars['like_name'] ) && ! empty( $query_vars['like_name'] ) ) {
            $query['s'] = esc_attr( $query_vars['like_name'] );
        }
        return $query;
    }

    /**
     * Handles ajax product lookup.
     * @since 1.0.0
     */
    public static function product_lookup() {
        self::respond( self::get_products( $_POST["query"], $_POST["limit"] ) );
    }

    /**
     * Echo a json-encoded response to an ajax request.
     * @since 1.0.0
     */
    public static function respond( $data ) {
        echo json_encode( $data );
    }

    private function __construct() {
        $this->ajax_hooks = [
            $this->add_hook( 'get_products' ),
            $this->add_hook( 'product_lookup' ),
            $this->add_hook( 'admin_redirect' ),
            $this->add_hook( 'delete_discount', 'PS_Discount_Manager' ),
            $this->add_hook( 'submit_discount', 'PS_Discount_Manager' ),
            $this->add_hook( 'duplicate_discount', 'PS_Discount_Manager' ),
            $this->add_hook( 'update_discount_status', 'PS_Discount_Manager' ),
            $this->add_hook( 'get_single_discount', 'PS_Discount_Manager' ),
            $this->add_hook( 'get_quick_edit_form', 'PS_Admin_Discounts' ),
        ];
        $this->init();
    }

    /**
     * Adds a new `acton` in the `wp_ajax` format.
     * Action name should be the same as the target method.
     * @since 1.0.0
     */
    private function add_hook( $callback, $component = __CLASS__, $priority = 10, $accepted_args = 1 ) {
        return [
            'hook'          => 'wp_ajax_' . $callback,
            'component'     => $component,
            'callback'      => $callback,
            'priority'      => $priority,
            'accepted_args' => $accepted_args,
        ];
    }

    /**
     * Hooks in ajax handlers.
     * @since 1.0.0
     */
    private function init() {
        // Adds loose product name comparison to WooCommerce product query
        add_filter( 'woocommerce_product_data_store_cpt_get_products_query', [$this, 'product_like_name'], 10, 2 );

        foreach ( $this->ajax_hooks as $hook ) {
            add_action( $hook['hook'], [$hook['component'], $hook['callback']], $hook['priority'], $hook['accepted_args'] );
        }

        $this->ajax_hooks = null;
    }
}
