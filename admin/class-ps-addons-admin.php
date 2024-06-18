<?php

/**
 * Registers admin menus and pages.
 * @link       https://podoshop.com.br
 * @since      1.0.0
 * @package    PS_Addons
 * @subpackage PS_Addons/admin
 * @author     Ian Mendes <ianlucamendes02@gmail.com>
 */
class PS_Addons_Admin {

	/**
	 * Plugin slug.
	 * @since 1.0.0
	 * @var string $plugin_name The ID of this plugin.
	 */
	private string $plugin_name;

	/**
	 * Plugin version.
	 * @since 1.0.0
	 */
	private string $version;

	/**
	 * Admin page names.
	 * @since 1.0.0
	 */
	private array $views;

	public function __construct($plugin_name, $version) {
		$this->plugin_name = $plugin_name;
		$this->version = $version;
		$this->views = [
			['slug' => 'dashboard', 'title' => 'Dashboard'],
			['slug' => 'discounts', 'title' => 'Descontos']
		];
	}

	/**
	 * Registers admin css.
	 * @since 1.0.0
	 */
	public function enqueue_styles() {
		wp_enqueue_style($this->plugin_name, plugin_dir_url(__FILE__) . 'css/ps-addons-admin.css', [], $this->version, 'all');
	}

	/**
	 * Registers admin js.
	 * @since 1.0.0
	 */
	public function enqueue_scripts() {
		wp_enqueue_script($this->plugin_name, plugin_dir_url(__FILE__) . 'js/ps-addons-admin.js', ['jquery'], $this->version, true);
		wp_localize_script($this->plugin_name, 'ps_addons_ajax', ['ajax_url' => admin_url('admin-ajax.php')]);
	}

	public function import_admin_views() {
		foreach ($this->views as $view) {
			require_once plugin_dir_path(__DIR__) . 'admin/views/' . $view['slug'] . '.php';
		}
	}

	/**
	 * Registers admin menus and pages.
	 * @since 1.0.0
	 */
	public function register_admin_menus() {
		add_menu_page('', 'Podoshop', 'manage_options', 'ps-admin', 'ps_addons_admin_dashboard', PSIcon(), 7);

		foreach ($this->views as $view) {
			add_submenu_page('ps-admin', $view['title'], $view['title'], 'manage_options', 'ps-' . $view['slug'], 'ps_addons_admin_' . $view['slug']);
		}

		remove_submenu_page('ps-admin', 'ps-admin');
	}
}
