<?php

/**
 * Cleanup routines to run on plugin uninstall.
 * @link       https://podoshop.com.br
 * @since      1.0.0
 * @package    Ps_Addons
 */

// If uninstall not called from WordPress, then exit.
defined('WP_UNINSTALL_PLUGIN') || exit;

function delete_tables() {
	global $wpdb;
	$table_name = $wpdb->prefix . "member_discounts";
	$sql = "DROP TABLE IF EXISTS $table_name";
	$wpdb->query($sql);
}

register_uninstall_hook(__FILE__, 'delete_tables');
