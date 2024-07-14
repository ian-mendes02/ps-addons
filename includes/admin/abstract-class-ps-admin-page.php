<?php

/**
 * Template class for admin page components.
 * @since 1.0.0
 */
abstract class PS_Admin_Page {
    protected $template;
    public $parent_slug = 'admin.php';
    public $page_title;
    public $menu_title;
    public $capability = 'manage_options';
    public $menu_slug;
    public $position = null;

    protected function format_slug($slug) {
        $this->template  = $slug;
        $this->menu_slug = 'ps_admin_' . str_replace( '-', '_', $slug );
    }

    protected function get_template_name() {
        return PS_ABSPATH . 'includes/admin/pages/html-ps-admin-' . $this->template . '.php';
    }

    public function get_template() {
        include_once $this->get_template_name();
    }
}