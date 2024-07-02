<?php
/**
 * Admin dashboard.
 * @since 1.0.0
 */
class PS_Admin_Dashboard extends PS_Admin_Page {
    public function __construct( $slug ) {
        $this->format_slug($slug);
        $this->page_title = 'Dashboard';
        $this->menu_title = 'Dashboard';
        $this->parent_slug = 'ps_admin';
    }
}
