<?php
/**
 * Fired during plugin deactivation.
 * @since      1.0.0
 * @package    PS_Addons
 * @subpackage PS_Addons/includes
 * @author     Ian Mendes <ianlucamendes02@gmail.com>
 */
class PS_Addons_Deactivator {
	public static function deactivate() {
		remove_menu_page('ps-addons-admin');
	}
}
