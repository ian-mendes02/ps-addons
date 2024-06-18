<?php

/**
 * Ajax controller for admin requests.
 * @package    PS_Addons
 * @subpackage PS_Addons/admin
 * @link       https://podoshop.com.br
 * @since      1.0.0
 * @author     Ian Mendes <ianlucamendes02@gmail.com>
 */

class PS_Addons_Ajax {

    /**
     * Registers loose product title comparison.
     * @since 1.0.0
     */
    public static function product_like_name($query, $query_vars) {
        if (isset($query_vars['like_name']) && !empty($query_vars['like_name'])) {
            $query['s'] = esc_attr($query_vars['like_name']);
        }
        return $query;
    }

    /**
     * Updates discount data in database.
     * @since 1.0.0
     */
    public static function update_discounts() {
        global $wpdb;
        $table_name = $wpdb->prefix . 'member_discounts';
        $data = $_POST['data'];
        $wpdb->insert($table_name, [
            'name' => $data["name"],
            'type' => $data["type"],
            'value' => $data["value"],
            'included_products' => $data["included_products"],
        ]);
    }

    /**
     * Lists WooCommerce products.
     * @since 1.0.0
     */
    public static function get_products($query = '', $limit = -1) {
        $args = ["limit" => $limit, "like_name" => $query];
        $products = wc_get_products($args) ?? [];
        $posts = [];
        foreach ($products as $product) {
            array_push($posts, (object) [
                "id" => $product->get_id(),
                "name" => $product->get_name(),
                "slug" => $product->get_slug(),
                "sku" => $product->get_sku(),
                "img_url" => esc_url(wp_get_attachment_image_url($product->get_image_id(), 'full')),
            ]);
        }
        return $posts;
    }

    /**
     * Handles ajax product lookup.
     * @since 1.0.0
     */
    public static function product_lookup() {
        echo json_encode(self::get_products($_POST["query"], $_POST["limit"]));
    }

    /**
     * Deletes discount data in database.
     * @since 1.0.0
     */
    public static function delete_discount() {
        global $wpdb;
        if (isset($_POST["discount_id"])) {
            $discount_id = $_POST["discount_id"];
            $table_name = $wpdb->prefix . 'member_discounts';
            $res = $wpdb->query("DELETE FROM $table_name WHERE id = $discount_id");
            echo $res;
        }
    }
}
