<?php
/**
 * Admin settings page.
 * @since 1.0.0
 */
class PS_Admin_Settings extends PS_Admin_Page {
    public function __construct( $slug ) {
        $this->format_slug( $slug );
        $this->parent_slug = 'ps_admin';
        $this->page_title  = 'Configurações';
        $this->menu_title  = 'Configurações';
    }
}