<?php

/**
 * Plugin bootstrap file.
 *
 * @link              https://podoshop.com.br
 * @since             1.0.0
 * @package           Ps_Addons
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
 * Text Domain:       ps-addons
 */

// Abort execution if accessed directly.
defined('WPINC') || die;

/**
 * Current plugin version.
 */
define('PS_ADDONS_VERSION', '1.0.0');

/**
 * Runs on activation.
 */
function activate_ps_addons() {
	require_once plugin_dir_path(__FILE__) . 'includes/class-ps-addons-activator.php';
	Ps_Addons_Activator::activate();
}

/**
 * Runs on deactivation.
 */
function deactivate_ps_addons() {
	require_once plugin_dir_path(__FILE__) . 'includes/class-ps-addons-deactivator.php';
	Ps_Addons_Deactivator::deactivate();
}

register_activation_hook(__FILE__, 'activate_ps_addons');

register_deactivation_hook(__FILE__, 'deactivate_ps_addons');

/**
 * Core plugin class.
 */
require plugin_dir_path(__FILE__) . 'includes/class-ps-addons.php';

/**
 * Jumpstart plugin execution.
 * @since 1.0.0
 */
function run_ps_addons() {
	$plugin = new Ps_Addons();
	$plugin->run();
}

// Plugin execution begins here
run_ps_addons();
