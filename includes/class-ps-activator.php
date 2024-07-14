<?php

/**
 * Fired during plugin activation.
 * @package    Podoshop
 * @subpackage Podoshop/Classes
 *
 * @since      1.0.0
 */
class PS_Activator {
    /**
     * Plugin activation routine.
     * @since 1.0.0
     */
    public static function activate() {
        self::create_tables();
        self::add_options();
        self::create_roles();
    }

    private static function create_tables() {
        global $wpdb;
        $table_name = $wpdb->prefix . 'member_discounts';
        $collate    = $wpdb->get_charset_collate();
        $sql        =
            "CREATE TABLE IF NOT EXISTS $table_name (
                id int(4) unsigned NOT NULL auto_increment,
                name varchar(32) NOT NULL,
                type varchar(8) NOT NULL DEFAULT 'fixed',
                value decimal(6,3) NOT NULL DEFAULT 0.0,
                included_products text NOT NULL DEFAULT '{}',
                author varchar(32) NOT NULL,
                last_modified datetime NOT NULL DEFAULT NOW(),
                last_edited_by varchar(32) NOT NULL,
                created_on datetime NOT NULL DEFAULT NOW(),
                expires_on datetime NULL DEFAULT NULL,
                is_active tinyint(1) NOT NULL,
                priority int(3) unsigned NOT NULL DEFAULT 0,
                PRIMARY KEY  (id),
                KEY name (name)
            ) $collate;";
        require_once ABSPATH . 'wp-admin/includes/upgrade.php';
        dbDelta( $sql );
    }

    private static function add_options() {
        add_option( 'ps_member_role', '', '', 'yes' );
    }

    private static function create_roles() {
        $base_role = get_role( get_option( 'default_role', 'customer' ) );
        $base_caps = $base_role->capabilities;
        add_role( 'ps_member', 'Assinante Podoshop', $base_caps );
        update_option( 'ps_member_role', 'ps_member' );
    }
}
