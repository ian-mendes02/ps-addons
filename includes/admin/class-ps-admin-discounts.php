<?php
/**
 * Admin discounts page.
 * @since 1.0.0
 */
class PS_Admin_Discounts extends PS_Admin_Page {
    public function __construct( $slug ) {
        $this->format_slug( $slug );
        $this->parent_slug = 'ps_admin';
        $this->page_title  = 'Descontos';
        $this->menu_title  = 'Descontos';
        if ( ps_var($_GET['page'], $this->menu_slug) ) {
            add_action( 'admin_enqueue_scripts', [$this, 'enqueue_assets'] );
        }
    }
    public function enqueue_assets() {
        wp_enqueue_script( 'ps-inline-edit', PS_ASSETS_FOLDER . 'js/ps-inline-edit.js', ['jquery'], PS_VERSION, true );

        wp_localize_script( 'ps-inline-edit', 'ps_ajax', [
            'ajax_url'      => admin_url( 'admin-ajax.php' ),
            'discount_data' => PS_Discount_Manager::get_discounts()
        ] );
    }
}