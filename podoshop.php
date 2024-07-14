<?php

/**
 * Plugin bootstrap file.
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
 * @package           Podoshop
 *
 * @link              https://podoshop.com.br
 * @since             1.0.0
 */

// Abort execution if accessed directly.
defined( 'WPINC' ) || die;

if ( ! defined( 'PS_PLUGIN_FILE' ) ) {
    define( 'PS_PLUGIN_FILE', __FILE__ );
}

if ( ! defined( 'PS_ROOT' ) ) {
    define( 'PS_ROOT', plugin_dir_path(__FILE__) );
}

function ps_activate() {
    include_once __DIR__ . '/includes/class-ps-activator.php';
    PS_Activator::activate();
}

function ps_deactivate() {
    include_once __DIR__ . '/includes/class-ps-deactivator.php';
    PS_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'ps_activate' );

register_deactivation_hook( __FILE__, 'ps_deactivate' );


// Include the main plugin class
require __DIR__ . '/includes/class-podoshop.php';

/**
 * Returns to the main instance of the plugin.
 * @since 1.0.0
 *
 * @return \Podoshop
 */
function PS() {
    return Podoshop::instance();
}

// Store main plugin instance in global scope
$GLOBALS['podoshop'] = PS();
