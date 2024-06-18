<?php

/**
 * Core plugin class.
 * @link       https://podoshop.com.br
 * @since      1.0.0
 * @package    PS_Addons
 * @subpackage PS_Addons/includes
 * @author     Ian Mendes <ianlucamendes02@gmail.com>
 */
class PS_Addons {

	/**
	 * Loads dependencies and registers hooks.
	 * @since 1.0.0
	 */
	protected PS_Addons_Loader $loader;

	/**
	 * Plugin slug.
	 * @since 1.0.0
	 */
	protected string $plugin_name;

	/**
	 * Current plugin version.
	 * @since 1.0.0
	 */
	protected string $version;

	public function __construct() {
		if (defined('PS_ADDONS_VERSION')) {
			$this->version = PS_ADDONS_VERSION;
		} else {
			$this->version = '1.0.0';
		}
		$this->plugin_name = 'ps-addons';
		$this->load_dependencies();
		$this->define_hooks();
	}

	/**
	 * Loads plugin dependencies.
	 * @since 1.0.0
	 */
	private function load_dependencies() {

		require_once plugin_dir_path(__DIR__) . 'admin/class-ps-addons-admin.php';
		require_once plugin_dir_path(__DIR__) . 'includes/ps-addons-functions.php';
		require_once plugin_dir_path(__DIR__) . 'includes/class-ps-addons-ajax.php';
		require_once plugin_dir_path(__DIR__) . 'includes/class-ps-addons-loader.php';
		require_once plugin_dir_path(__DIR__) . 'includes/class-ps-addons-discount.php';
		require_once plugin_dir_path(__DIR__) . 'includes/class-ps-addons-customer.php';
		require_once plugin_dir_path(__DIR__) . 'includes/class-ps-addons-discount-manager.php';

		$this->loader = new PS_Addons_Loader();
	}

	/**
	 * Registers admin hooks.
	 * @since 1.0.0
	 */
	private function define_hooks() {
		
		$ps_admin = new PS_Addons_Admin($this->get_plugin_name(), $this->get_version());
		$ps_ajax = 'PS_Addons_Ajax';
		$ps_discount_manager = 'PS_Addons_Discount_Manager';

		$this->loader->add_action('admin_enqueue_scripts', $ps_admin, 'enqueue_styles');
		$this->loader->add_action('admin_enqueue_scripts', $ps_admin, 'enqueue_scripts');
		$this->loader->add_action('admin_menu', $ps_admin, 'register_admin_menus');
		$this->loader->add_action('admin_menu', $ps_admin, 'import_admin_views');
		$this->loader->add_action('wp_ajax_get_products', $ps_ajax, 'get_products');
		$this->loader->add_action('wp_ajax_product_lookup', $ps_ajax, 'product_lookup');
		$this->loader->add_action('wp_ajax_update_discounts', $ps_ajax, 'update_discounts');
		$this->loader->add_action('wp_ajax_delete_discount', $ps_ajax, 'delete_discount');
		$this->loader->add_action('woocommerce_cart_calculate_fees', $ps_discount_manager, 'apply_discount');
		$this->loader->add_filter( 'woocommerce_product_data_store_cpt_get_products_query', $ps_ajax, 'product_like_name', 10, 2 );
		do_action('user_register', ['Ps_Customer', 'verify_membership']);
	}

	/**
	 * Runs the loader to execute all of the queued hooks.
	 * @since 1.0.0
	 */
	public function run() {
		$this->loader->run();
	}

	/**
	 * Retrieves plugin slug.
	 * @since 1.0.0
	 */
	public function get_plugin_name() {
		return $this->plugin_name;
	}

	/**
	 * Retrieves current loader object.
	 * @since 1.0.0
	 */
	public function get_loader() {
		return $this->loader;
	}

	/**
	 * Retrieve plugin version number.
	 * @since 1.0.0
	 */
	public function get_version() {
		return $this->version;
	}
}
