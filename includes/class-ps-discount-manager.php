<?php

/**
 * Methods for managing member discounts.
 * @package    Podoshop
 * @subpackage Podoshop/Classes
 *
 * @author     Ian Mendes <ianlucamendes02@gmail.com>
 *
 * @since      1.0.0
 */
class PS_Discount_Manager {
    /**
     * An instance of this class.
     * @since 1.0.0
     */
    protected static $instance;

    /**
     * Is AWL Plugin currently installed and active.
     * @since 1.0.0
     */
    private $is_awl_active;

    /**
     * Effective discount objects.
     * @var array
     * @since 1.0.0
     */
    public $discounts;

    /**
     * Ensures a single intance of this class is loaded.
     * @since 1.0.0
     */
    public static function instance() {
        if ( is_null( self::$instance ) ) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    private function __construct() {
        $this->is_awl_active = class_exists( 'AWL_Main' );
        $this->discounts     = [];
        $this->get_effective_discounts();
        $this->init_hooks();
    }

    /**
     * Removes discount from database.
     * @since 1.0.0
     */
    public static function delete_discount() {
        global $wpdb;
        if ( isset( $_POST["id"] ) ) {
            $id         = $_POST["id"];
            $table_name = $wpdb->prefix . 'member_discounts';
            $res        = $wpdb->query( "DELETE FROM $table_name WHERE id = $id" );
            PS_Ajax::respond( $res );
        }
    }

    /**
     * Duplicates an existing discount.
     * @since 1.0.0
     */
    public static function duplicate_discount() {
        if ( isset( $_POST['id'] ) ) {
            $discount       = self::get_single_discount( $_POST['id'] );
            $discount->name = 'Cópia de ' . $discount->name;
            $discount->id   = null;
            $submit         = self::submit_discount( (array) $discount );
            PS_Ajax::respond( ['status' => $submit ? 'ok' : 'error'] );
        }
    }

    /**
     * Gets discount data from database.
     * @since 1.0.0
     */
    public static function get_discounts( $active_only = false, $order_by = 'created_on', $order = 'ASC' ) {
        global $wpdb;
        $table_name = $wpdb->prefix . 'member_discounts';

        $query = "SELECT * FROM $table_name";
        $active_only && $query .= " WHERE is_active = 1";
        $query .= " ORDER BY $order_by $order";

        $results   = $wpdb->get_results( $query, ARRAY_A );
        $discounts = [];
        foreach ( $results as $result ) array_push( $discounts, new PS_Discount( $result ) );
        return $discounts;
    }

    /**
     * Gets a discount object from database by id.
     * If `$id` is not provided, returns a blank discount object.
     * @since 1.0.0
     *
     * @param int $id
     */
    public static function get_single_discount( $id = null ) {
        global $wpdb;
        if ( ! is_null( $id ) && $id != '' ) {
            $table_name = $wpdb->prefix . 'member_discounts';
            $query      = $wpdb->get_row( "SELECT * FROM $table_name WHERE id = $id", ARRAY_A );
            return new PS_Discount( $query );
        }
        return new PS_Discount();
    }

    /**
     * Creates or updates discount data in database.
     * @since 1.0.0
     *
     * @param array $data
     */
    public static function submit_discount( $data = [] ) {
        global $wpdb;
        $wp_user   = wp_get_current_user()->display_name;
        $is_post   = isset( $_POST['data'] );
        $post_data = $is_post ? $_POST['data'] : $data;
        if ( isset( $post_data ) ) {
            date_default_timezone_set( 'America/Sao_Paulo' );

            $discount;
            $discount_duration = null;

            $discount = self::get_single_discount( (int) $post_data['id'] ?? null );

            if ( isset( $post_data['schedule'] ) ) {
                $discount_duration = $post_data['schedule'];
            }

            $table_name  = $wpdb->prefix . 'member_discounts';
            $name        = $post_data['name'];
            $query_names = $wpdb->query( "SELECT * FROM $table_name WHERE name = '$name'" );

            $query = null;

            $date = date( 'Y-m-d H:i:s' );
            if ( $discount->name != $post_data['name'] && $query_names != 0 ) {
                PS_Ajax::respond( ['status' => 'error', 'message' => 'Um item com esse nome já existe.'] );
                return;
            } else {
                $included_products = '[]';
                if ( isset( $post_data['included_products'] ) ) {
                    $included_products = json_encode( $post_data['included_products'] );
                }
                if ( ! isset( $discount->id ) || $discount->id == '' ) {
                    $query = $wpdb->insert( $table_name, [
                        'name'              => $post_data['name'],
                        'type'              => $post_data['type'],
                        'author'            => $wp_user,
                        'created_on'        => $date,
                        'last_modified'     => $date,
                        'last_edited_by'    => $wp_user,
                        'expires_on'        => $discount_duration,
                        'value'             => (float) $post_data['value'],
                        'is_active'         => $post_data['is_active'],
                        'included_products' => $included_products,
                        'priority'          => $post_data['priority'],
                    ] );
                } else {
                    $query = $wpdb->update( $table_name, [
                        'name'              => $post_data['name'],
                        'type'              => $post_data['type'],
                        'last_modified'     => $date,
                        'last_edited_by'    => $wp_user,
                        'expires_on'        => $discount_duration,
                        'value'             => (float) $post_data['value'],
                        'is_active'         => $post_data['is_active'],
                        'included_products' => $included_products,
                        'priority'          => $post_data['priority'],
                    ], ['id' => $discount->id] );
                }
            }
            if ( $is_post ) PS_Ajax::respond( $query );
            else return $query;
        }
    }

    /**
     * Updates discount 'is_active' field.
     * @since 1.0.0
     */
    public static function update_discount_status() {
        global $wpdb;
        if ( isset( $_POST ) ) {
            $is_active  = (int) $_POST['is_active'];
            $id         = (int) $_POST['id'];
            $table_name = $wpdb->prefix . 'member_discounts';
            $query      = $wpdb->update( $table_name, ['is_active' => $is_active], ['id' => $id] );
            PS_Ajax::respond( $query );
        }
    }

    /**
     * Get effective discount data.
     * Effective discounts are marked as active and within expiration date.
     * Discounts with the same priority value will be overwritten by the most recent.
     * @since 1.0.0
     */
    public function get_effective_discounts() {
        foreach ( self::get_discounts( true, 'priority' ) as $discount ) {
            if ( strtotime( $discount->expires_on ) < time() ) {
                foreach ( $discount->included_products as $prod ) {
                    $this->discounts[$prod['id']] = [
                        'type'  => $discount->type,
                        'value' => $discount->value,
                    ];
                }
            }
        }
    }

    /**
     * Applies valid discounts to elegible products in cart.
     * @since 1.0.0
     *
     * @param WC_Cart $cart passed from `woocommerce_cart_calculate_fees` hook.
     */
    public function apply_discounts_to_cart( $cart ) {
        $total = 0;
        // Calculate total discount value
        foreach ( $cart->get_cart() as $cart_item => $values ) {
            $id = $values['product_id'];
            if ( isset( $this->discounts[$id] ) ) {
                $discount = $this->discounts[$id]['value'];
                $price    = $values['data']->get_price();
                if ( $this->discounts[$id]['type'] == 'percent' ) {
                    $discount = $price - ( ( $discount / 100 ) * $price );
                }
                $total -= $discount;
            }
        }

        // Apply discount to cart if current user is member
        // Print potential savings otherwise
        if ( $total < 0 ) {
            if ( PS_Customer::instance()->is_member() ) {
                $cart->add_fee( 'Desconto Palmilhando®', $total );
            } else {
                add_action( 'woocommerce_proceed_to_checkout', function () use ( $total ) {
                    $d = ps_format_value( str_replace( "-", "", (string) round( $total, 2 ) ) );
                    ps_push_subscribe( $d );
                } );
            }
        }
    }

    /**
     * Adds labels to store loop on elegible products.
     * @since 1.0.0
     */
    public function print_discounts_to_store_loop() {
        global $product;
        if ( PS_Customer::instance()->is_member() && isset( $this->discounts[$product->get_id()] ) ) {
            print( '<span class="ps-discount"></span>' );
        }
    }

    /**
     * Hooks in methods.
     * @since 1.0.0
     */
    public function init_hooks() {
        add_action( 'woocommerce_cart_calculate_fees', [$this, 'apply_discounts_to_cart'] );
        add_action( 'woocommerce_before_shop_loop_item', [$this, 'print_discounts_to_store_loop'] );
    }

}
