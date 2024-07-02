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
     * Applies member discounts to applicable products.
     * @since 1.0.0
     *
     * @param WC_Cart $cart passed from `woocommerce_cart_calculate_fees` hook.
     */
    public static function apply_discount( $cart ) {
        if ( ps_customer()->is_member() ) {
            foreach ( $cart->get_cart() as $cart_item ) {
                $product_id = $cart_item['product_id'];
                if ( in_array( $product_id, $discounted_products ) ) {
                    $price    = $cart_item['data']->get_price();
                    $discount = $price - ( ( $discount_percentage / 100 ) * $price );
                    $cart->add_fee( 'Desconto Palmilhando®', -$discount );
                }
            }
        }
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
            $discount->name = $discount->name . ' (cópia)';
            $discount->id   = null;
            $submit         = self::submit_discount( (array) $discount );
            PS_Ajax::respond( ['status' => $submit ? 'ok' : 'error'] );
        }
    }

    /**
     * Gets discount data from database.
     * @since 1.0.0
     */
    public static function get_discounts() {
        global $wpdb;
        $table_name = $wpdb->prefix . 'member_discounts';
        $results    = $wpdb->get_results( "SELECT * FROM $table_name", ARRAY_A );
        $discounts  = [];
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
    public static function get_single_discount( $discount_id = null ) {
        global $wpdb;
        if ( ! is_null( $discount_id ) && $discount_id != '' ) {
            $table_name = $wpdb->prefix . 'member_discounts';
            $query      = $wpdb->get_row( "SELECT * FROM $table_name WHERE id = $discount_id", ARRAY_A );
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
                PS_Ajax::respond( ['status' => 'error', 'message' => 'name exists'] );
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
}

// Hooks in methods once file is included
add_action( 'woocommerce_cart_calculate_fees', ['PS_Discount_Manager', 'apply_discount'] );
