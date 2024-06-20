<?php

/**
 * Ajax controller for admin requests.
 * @package    Podoshop
 * @subpackage Podoshop/Classes/admin
 * @link       https://podoshop.com.br
 * @since      1.0.0
 * @author     Ian Mendes <ianlucamendes02@gmail.com>
 */

class PS_Ajax {

    private $ajax_hooks;

    public function __construct() {
        $this->ajax_hooks = [];
    }

    /**
     * Adds a new `acton` in the `wp_ajax` format. 
     * Action name should be the same as the target method.
     * @since 1.0.0
     */
    public function add($callback, $priority = 10, $accepted_args = 1) {
        array_push($this->ajax_hooks, [
            'hook'          => 'wp_ajax_' . $callback, 
            'component'     => __CLASS__, 
            'callback'      => $callback, 
            'priority'      => $priority, 
            'accepted_args' => $accepted_args
        ]);
    }

    /**
     * Hooks in ajax handlers.
     * @since 1.0.0
     */
    public function init() {
        foreach ($this->ajax_hooks as $hook) {
			add_action($hook['hook'], [$hook['component'], $hook['callback']], $hook['priority'], $hook['accepted_args']);
		}
    }

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
        if (isset($_POST['data'])) {
            $data = $_POST['data'];
            $table_name = $wpdb->prefix . 'member_discounts';
            $name = $data['name'];
            $query_names = $wpdb->query("SELECT * FROM $table_name WHERE name = '$name'");
            if ($query_names != 0) {
                echo json_encode(['error' => 'name exists.']);
            } else {
                $wpdb->insert($table_name, [
                    'name' => $data["name"],
                    'type' => $data["type"],
                    'value' => $data["value"],
                    'included_products' => $data["included_products"],
                ]);
            }
        }
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
