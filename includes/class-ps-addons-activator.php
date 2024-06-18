<?php

/**
 * Fired during plugin activation.
 * @since      1.0.0
 * @package    PS_Addons
 * @subpackage PS_Addons/includes
 * @author     Ian Mendes <ianlucamendes02@gmail.com>
 */
class PS_Addons_Activator {

	/**
	 * Main activation function.
	 * @since 1.0.0
	 */
	public static function activate() {
		self::create_discounts_table();
	}

	/**
	 * Create discount data table.
	 * @since 1.0.0
	 */
	public static function create_discounts_table() {
		global $wpdb;
		$collate = '';
		if ($wpdb->has_cap('collation')) {
			$collate = $wpdb->get_charset_collate();
		}
		$sql = "
			CREATE TABLE IF NOT EXISTS {$wpdb->prefix}member_discounts (
				id int(4) unsigned NOT NULL AUTO_INCREMENT,
				included_products text NOT NULL DEFAULT '[]',
				name varchar(32) NOT NULL DEFAULT '',
				type varchar(8) NULL DEFAULT NULL,
				value decimal(6,3) NOT NULL DEFAULT 0.0,
				PRIMARY KEY  (id),
				UNIQUE KEY name (name)
			) $collate;
		";
		require_once ABSPATH . 'wp-admin/includes/upgrade.php';
		dbDelta($sql);
	}
}
