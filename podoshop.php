<?php

/**
 * Plugin bootstrap file.
 *
 * @link              https://podoshop.com.br
 * @since             1.0.0
 * @package           Podoshop
 *
 * @wordpress-plugin
 * Plugin Name:       Podoshop Addons
 * Plugin URI:        https://podoshop.com.br
 * Description:       Complementos para a loja virtual Podoshop®.
 * Version:           1.0.0
 * Author:            Ian Mendes
 * Author URI:        https://podoshop.com.br/
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 */

// Abort execution if accessed directly.
defined('WPINC') || die;

if ( ! defined( 'PS_PLUGIN_FILE' ) ) {
	define( 'PS_PLUGIN_FILE', __FILE__ );
}

include_once __DIR__ . '/includes/class-ps-customer.php';

// Store current logged in user in the global scope.
$GLOBALS['ps-customer'] = PS_Customer::instance();

// Include the main plugin class
include_once __DIR__ . '/includes/class-podoshop.php';

/** 
 * Returns to the main instance of the plugin.
 * @since 1.0.0
 * @return \Podoshop
 */
function PS() {
	return Podoshop::instance();
}

// Store main plugin instance in global scope
$GLOBALS['podoshop'] = PS();
