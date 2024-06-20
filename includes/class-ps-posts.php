<?php

/**
 * Registers post types and taxonomies.
 * @since 1.0.0
 */
class PS_Posts {

    public static function init() {
        add_action('init', [__CLASS__, 'register_post_types']);
        add_action('init', [__CLASS__, 'register_taxonomies']);
    }

    private static function register_post_types() {
        register_post_type('ps_discount', [
            'labels'             => [
                'name'                  => 'Descontos',
                'singular_name'         => 'Desconto',
                'menu_name'             => 'Descontos',
                'name_admin_bar'        => 'Descontos',
                'add_new'               => 'Criar novo',
                'add_new_item'          => 'Criar novo desconto',
                'new_item'              => 'Novo desconto',
                'edit_item'             => 'Editar desconto',
                'view_item'             => 'Ver desconto',
                'all_items'             => 'Todos os descontos',
                'search_items'          => 'Pesquisar descontos',
                'not_found'             => 'Nenhum desconto cadastrado.',
                'not_found_in_trash'    => 'Nenhum desconto na lixeira.',
                'filter_items_list'     => 'Filtrar descontos',
                'items_list_navigation' => 'Navegar pelos descontos',
                'items_list'            => 'Lista de descontos',
            ],
            'description'        => 'Desconto para assinantes do Palmilhando.',
            'public'             => true,
            'publicly_queryable' => true,
            'show_ui'            => true,
            'show_in_menu'       => true,
            'query_var'          => true,
            'rewrite'            => ['slug' => 'ps-discount'],
            'capability_type'    => 'post',
            'has_archive'        => true,
            'hierarchical'       => false,
            'menu_position'      => 20,
            'supports'           => ['title', 'editor', 'author'],
            'taxonomies'         => ['type', 'included-products', 'value'],
            'show_in_rest'       => true
        ]);
    }

    private static function register_taxonomies() {
        register_taxonomy('discount_type', 'ps_discount', [
            'labels' => [
                'name' => 'Tipo de desconto',
                'singular_name' => 'Tipo de desconto',
                'edit_item' => 'Editar tipo',
                'update_item' => 'Atualizar tipo',
                'menu_name' => 'Tipo de desconto',
            ],
            'rewrite' => [
                'slug' => 'ps',
                'with_front' => false,
            ],
            'hierarchical' => false,
            'show_in_quick_edit' => true,
        ]);
    }
}

PS_Posts::init();