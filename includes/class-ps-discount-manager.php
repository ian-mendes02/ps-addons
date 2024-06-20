<?php

/**
 * Methods for managing member discounts.
 * @since      1.0.0
 * @package    Podoshop
 * @subpackage Podoshop/Classes
 * @author     Ian Mendes <ianlucamendes02@gmail.com>
 */
class PS_Discount_Manager {

    /**
     * Gets discount data from database.
     * @since 1.0.0
     */
    public static function get_discounts() {
        global $wpdb;
        $table_name = $wpdb->prefix . 'member_discounts';
        $results = $wpdb->get_results("SELECT * FROM $table_name", ARRAY_A);
        $discounts = [];
        foreach ($results as $result) {
            array_push($discounts, new PS_Discount($result));
        }
        return $discounts;
    }

    /**
     * Applies member discounts to applicable products.
     * @param WC_Cart $cart passed from `woocommerce_cart_calculate_fees` hook.
     * @since 1.0.0
     */
    public static function apply_discount($cart) {
        if (ps_is_member()) {
            foreach ($cart->get_cart() as $cart_item) {
                $product_id = $cart_item['product_id'];
                if (in_array($product_id, $discounted_products)) {
                    $price = $cart_item['data']->get_price();
                    $discount = $price - (($discount_percentage / 100) * $price);
                    $cart->add_fee('Desconto Palmilhando®', -$discount);
                }
            }
        }
    }
}
