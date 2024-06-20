<?php

/**
 * Registers admin menus and pages.
 * @link       https://podoshop.com.br
 * @since      1.0.0
 * @package    Podoshop
 * @subpackage Podoshop/Admin
 * @author     Ian Mendes <ianlucamendes02@gmail.com>
 */
class PS_Admin {

	protected $views;

	public function __construct() {
		$this->views = [
			['slug' => 'dashboard', 'title' => 'Dashboard'],
			['slug' => 'discounts', 'title' => 'Descontos']
		];
	}

	/**
	 * Registers static admin assets.
	 * @since 1.0.0
	 */
	public function enqueue_assets() {
		// Admin js and ajax
		wp_enqueue_script('ps-admin-js', PS_PLUGIN_URL . 'includes/admin/js/ps-admin.js', ['jquery'], PS_VERSION, true);
		wp_localize_script('ps-admin-js', 'ps_ajax', ['ajax_url' => admin_url('admin-ajax.php')]);
		// Admin css
		wp_enqueue_style('ps-admin-css', PS_PLUGIN_URL  . 'includes/admin/css/ps-admin.css', [], PS_VERSION, 'all');
	}

	protected function import_admin_views() {
		foreach ($this->views as $view) {
			include_once PS_ABSPATH . 'includes/admin/views/' . $view['slug'] . '.php';
		}
	}

	/**
	 * Registers admin menus and pages.
	 * @since 1.0.0
	 */
	public function register_admin_menus() {

		$this->import_admin_views();

		add_menu_page('', 'Podoshop', 'manage_options', 'ps-admin', 'ps_admin_dashboard', ps_store_icon(), 7);

		foreach ($this->views as $view) {
			add_submenu_page('ps-admin', $view['title'], $view['title'], 'manage_options', 'ps-' . $view['slug'], 'ps_admin_' . $view['slug']);
		}

		remove_submenu_page('ps-admin', 'ps-admin');
	}
}
