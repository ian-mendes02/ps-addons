<?php

/**
 * Core plugin class.
 * @link       https://podoshop.com.br
 * @since      1.0.0
 * @package    Podoshop
 * @subpackage Podoshop/Classes
 * @author     Ian Mendes <ianlucamendes02@gmail.com>
 */
class Podoshop {

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
	 * Returns a new instance of the class itself.
	 * @since 1.0.0
	 */
	public static function instance() {
		if (is_null(self::$instance)) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	public function __construct() {
		$this->define_constants();
		$this->load_dependencies();
		$this->define_hooks();
	}

	/**
	 * Defines plugin constants.
	 * @since 1.0.0
	 */
	private function define_constants() {
		$this->define('PS_ABSPATH', plugin_dir_path(PS_PLUGIN_FILE));
		$this->define('PS_PLUGIN_URL', plugin_dir_url(PS_PLUGIN_FILE));
		$this->define('PS_PLUGIN_BASENAME', plugin_basename(PS_PLUGIN_FILE));
		$this->define('PS_VERSION', $this->version);
		$this->define('PS_SLUG', $this->plugin_slug);
	}

	/**
	 * Loads plugin dependencies.
	 * @since 1.0.0
	 */
	private function load_dependencies() {
		include_once PS_ABSPATH . 'includes/ps-functions.php';
		include_once PS_ABSPATH . 'includes/class-ps-ajax.php';
		include_once PS_ABSPATH . 'includes/class-ps-loader.php';
		include_once PS_ABSPATH . 'includes/class-ps-discount.php';
		include_once PS_ABSPATH . 'includes/class-ps-activator.php';
		include_once PS_ABSPATH . 'includes/admin/class-ps-admin.php';
		include_once PS_ABSPATH . 'includes/class-ps-deactivator.php';
		include_once PS_ABSPATH . 'includes/class-ps-discount-manager.php';
	}

	/**
	 * Initializes plugin hooks.
	 * @since 1.0.0
	 */
	private function init_hooks() {

		register_activation_hook(__FILE__, ['PS_Activator', 'activate']);
		register_deactivation_hook(__FILE__, ['Ps_Deactivator', 'deactivate']);

		$ps_ajax = new PS_Ajax();
		$ps_admin = new PS_Admin();
		$ps_loader = new PS_Loader();

		add_action('woocommerce_cart_calculate_fees', ['PS_Discount_Manager', 'apply_discount']);
		add_filter('woocommerce_product_data_store_cpt_get_products_query', [$ps_ajax, 'product_like_name'], 10, 2);
		add_action('admin_enqueue_scripts', [$ps_admin, 'enqueue_assets']);
		add_action('admin_menu', [$ps_admin, 'register_admin_menus']);
		
		$ps_loader->enqueue_script('ps-components');
		$ps_loader->init();

        $ps_ajax->add('get_products');
		$ps_ajax->add('product_lookup');
		$ps_ajax->add('update_discounts');
		$ps_ajax->add('delete_discount');
		$ps_ajax->init();
	}

	/**
	 * Defines a constant if not already set.
	 * @param string      $name  Constant name.
	 * @param string|bool $value Constant value.
	 */
	private function define($name, $value) {
		if (!defined($name)) {
			define($name, $value);
		}
	}
}
