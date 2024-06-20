<?php

/*
Plugin Name:  SupportHost Admin Table
Description: It displays a table with custom data
Author: SupportHost
Author URI: https://supporthost.com/
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html
Text Domain: supporthost-admin-table
Version: 1.0
*/

// Loading WP_List_Table class file
// We need to load it as it's not automatically loaded by WordPress
if (!class_exists('WP_List_Table')) {
    require_once(ABSPATH . 'wp-admin/includes/class-wp-list-table.php');
}

// Extending class
class Supporthost_List_Table extends WP_List_Table {
    // Here we will add our code
}

// Adding menu
function my_add_menu_items() {
    add_menu_page('SupportHost List Table', 'SupportHost List Table', 'activate_plugins', 'supporthost_list_table', 'supporthost_list_init');
}
add_action('admin_menu', 'my_add_menu_items');

// Plugin menu callback function
function supporthost_list_init() {
    // Creating an instance
    $table = new Supporthost_List_Table();

    echo '<div class="wrap"><h2>SupportHost List Table</h2>';
    // Prepare table
    $table->prepare_items();
    // Display table
    $table->display();
    echo '</div>';
}
