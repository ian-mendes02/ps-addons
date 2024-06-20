<?php

/**
 * Fired during plugin activation.
 * @since      1.0.0
 * @package    Podoshop
 * @subpackage Podoshop/Classes
 * @author     Ian Mendes <ianlucamendes02@gmail.com>
 */
class PS_Activator {

	/**
	 * Main activation function.
	 * @since 1.0.0
	 */
	public static function activate() {
		self::create_terms();
	}

	private static function create_terms() {

		$taxonomies = [

		];

		foreach ( $taxonomies as $taxonomy => $terms ) {
			foreach ( $terms as $term ) {
				if ( ! get_term_by( 'name', $term, $taxonomy ) ) {
					wp_insert_term( $term, $taxonomy );
				}
			}
		}
	}
}
