<?php
/**
 * Fired during plugin deactivation.
 * @since      1.0.0
 * @package    Podoshop
 * @subpackage Podoshop/Classes
 * @author     Ian Mendes <ianlucamendes02@gmail.com>
 */
class PS_Deactivator {
	public static function deactivate() {
		remove_menu_page('ps-admin');
	}
}
