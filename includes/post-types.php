<?php
/**
 * Registro de Custom Post Types
 */

// Prevenir acceso directo
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Registrar todos los post types del plugin
 */
function lamusa_register_post_types() {
    lamusa_register_restaurant_post_type();
    lamusa_register_weekly_menu_post_type();
}

/**
 * Registrar post type para Restaurantes
 */
function lamusa_register_restaurant_post_type() {
    $labels = array(
        'name'                  => _x('Restaurantes', 'Post type general name', 'lamusa-core'),
        'singular_name'         => _x('Restaurante', 'Post type singular name', 'lamusa-core'),
        'menu_name'             => _x('Restaurantes', 'Admin Menu text', 'lamusa-core'),
        'name_admin_bar'        => _x('Restaurante', 'Add New on Toolbar', 'lamusa-core'),
        'add_new'               => __('Añadir Nuevo', 'lamusa-core'),
        'add_new_item'          => __('Nuevo Restaurante', 'lamusa-core'),
        'new_item'              => __('Añadir Restaurante', 'lamusa-core'),
        'edit_item'             => __('Editar Restaurante', 'lamusa-core'),
        'view_item'             => __('Ver Restaurante', 'lamusa-core'),
        'all_items'             => __('Restaurantes', 'lamusa-core'),
        'search_items'          => __('Buscar Restaurantes', 'lamusa-core'),
        'parent_item_colon'     => __('Restaurante Padre:', 'lamusa-core'),
        'not_found'             => __('No se encontraron restaurantes.', 'lamusa-core'),
        'not_found_in_trash'    => __('No se encontraron restaurantes en la papelera.', 'lamusa-core'),
        'featured_image'        => _x('Imagen Destacada', 'Overrides the "Featured Image" phrase', 'lamusa-core'),
        'set_featured_image'    => _x('Establecer imagen destacada', 'Overrides the "Set featured image" phrase', 'lamusa-core'),
        'remove_featured_image' => _x('Quitar imagen destacada', 'Overrides the "Remove featured image" phrase', 'lamusa-core'),
        'use_featured_image'    => _x('Usar como imagen destacada', 'Overrides the "Use as featured image" phrase', 'lamusa-core'),
        'archives'              => _x('Archivos de Restaurantes', 'The post type archive label', 'lamusa-core'),
        'insert_into_item'      => _x('Insertar en restaurante', 'Overrides the "Insert into post" phrase', 'lamusa-core'),
        'uploaded_to_this_item' => _x('Subido a este restaurante', 'Overrides the "Uploaded to this post" phrase', 'lamusa-core'),
        'filter_items_list'     => _x('Filtrar lista de restaurantes', 'Screen reader text for the filter links', 'lamusa-core'),
        'items_list_navigation' => _x('Navegación de lista de restaurantes', 'Screen reader text for the pagination', 'lamusa-core'),
        'items_list'            => _x('Lista de restaurantes', 'Screen reader text for the items list', 'lamusa-core'),
    );

    $args = array(
        'labels'             => $labels,
        'public'             => true,
        'publicly_queryable' => true,
        'show_ui'            => true,
        'show_in_menu'       => true,
        'menu_position'      => 20,
        'menu_icon'          => 'dashicons-store',
        'query_var'          => true,
        'rewrite'            => array('slug' => 'restaurante'),
        'capability_type'    => 'post',
        'has_archive'        => true,
        'hierarchical'       => false,
        'supports'           => array('title', 'thumbnail', 'custom-fields'),
        'show_in_rest'       => true, // Para Gutenberg y REST API
        'rest_base'          => 'restaurants',
        'rest_controller_class' => 'WP_REST_Posts_Controller',
        'description'        => __('Post type para gestionar los restaurantes del grupo La Musa', 'lamusa-core'),
    );

    register_post_type('restaurant', $args);
}

/**
 * Registrar post type para Menús Semanales
 */
function lamusa_register_weekly_menu_post_type() {
    $labels = array(
        'name'                  => _x('Menús Semanales', 'Post type general name', 'lamusa-core'),
        'singular_name'         => _x('Menú Semanal', 'Post type singular name', 'lamusa-core'),
        'menu_name'             => _x('Menús Semanales', 'Admin Menu text', 'lamusa-core'),
        'name_admin_bar'        => _x('Menú Semanal', 'Add New on Toolbar', 'lamusa-core'),
        'add_new'               => __('Añadir Nuevo', 'lamusa-core'),
        'add_new_item'          => __('Añadir Nuevo Menú Semanal', 'lamusa-core'),
        'new_item'              => __('Nuevo Menú Semanal', 'lamusa-core'),
        'edit_item'             => __('Editar Menú Semanal', 'lamusa-core'),
        'view_item'             => __('Ver Menú Semanal', 'lamusa-core'),
        'all_items'             => __('Todos los Menús', 'lamusa-core'),
        'search_items'          => __('Buscar Menús', 'lamusa-core'),
        'parent_item_colon'     => __('Menú Padre:', 'lamusa-core'),
        'not_found'             => __('No se encontraron menús.', 'lamusa-core'),
        'not_found_in_trash'    => __('No se encontraron menús en la papelera.', 'lamusa-core'),
        'featured_image'        => _x('Imagen del Menú', 'Overrides the "Featured Image" phrase', 'lamusa-core'),
        'set_featured_image'    => _x('Establecer imagen del menú', 'Overrides the "Set featured image" phrase', 'lamusa-core'),
        'remove_featured_image' => _x('Quitar imagen del menú', 'Overrides the "Remove featured image" phrase', 'lamusa-core'),
        'use_featured_image'    => _x('Usar como imagen del menú', 'Overrides the "Use as featured image" phrase', 'lamusa-core'),
        'archives'              => _x('Archivos de Menús', 'The post type archive label', 'lamusa-core'),
        'insert_into_item'      => _x('Insertar en menú', 'Overrides the "Insert into post" phrase', 'lamusa-core'),
        'uploaded_to_this_item' => _x('Subido a este menú', 'Overrides the "Uploaded to this post" phrase', 'lamusa-core'),
        'filter_items_list'     => _x('Filtrar lista de menús', 'Screen reader text for the filter links', 'lamusa-core'),
        'items_list_navigation' => _x('Navegación de lista de menús', 'Screen reader text for the pagination', 'lamusa-core'),
        'items_list'            => _x('Lista de menús', 'Screen reader text for the items list', 'lamusa-core'),
    );

    $args = array(
        'labels'             => $labels,
        'public'             => true,
        'publicly_queryable' => true,
        'show_ui'            => true,
        'show_in_menu'       => 'edit.php?post_type=restaurant', // Submenu bajo restaurantes
        'query_var'          => true,
        'rewrite'            => array('slug' => 'menu-semanal'),
        'capability_type'    => 'post',
        'has_archive'        => false, // No necesitamos archivo público
        'hierarchical'       => false,
        'supports'           => array('title', 'thumbnail', 'custom-fields'),
        'show_in_rest'       => true,
        'rest_base'          => 'weekly-menus',
        'rest_controller_class' => 'WP_REST_Posts_Controller',
        'description'        => __('Post type para gestionar los menús semanales de los restaurantes', 'lamusa-core'),
    );

    register_post_type('weekly_menu', $args);
}

/**
 * Personalizar las columnas del admin para restaurantes
 */
add_filter('manage_restaurant_posts_columns', 'lamusa_restaurant_columns');
function lamusa_restaurant_columns($columns) {
    $new_columns = array();
    $new_columns['cb'] = $columns['cb'];
    $new_columns['title'] = __('Gestión Interna', 'lamusa-core');
    $new_columns['restaurant_title'] = __('Título Principal', 'lamusa-core');
    $new_columns['restaurant_title_secondary'] = __('Título Secundario', 'lamusa-core');
    $new_columns['restaurant_subtitle'] = __('Subtítulo Principal', 'lamusa-core');
    $new_columns['restaurant_subtitle_secondary'] = __('Subtítulo Secundario', 'lamusa-core');
    $new_columns['restaurant_address'] = __('Dirección', 'lamusa-core');
    $new_columns['date'] = $columns['date'];
    
    return $new_columns;
}

/**
 * Rellenar las columnas personalizadas para restaurantes
 */
add_action('manage_restaurant_posts_custom_column', 'lamusa_restaurant_column_content', 10, 2);
function lamusa_restaurant_column_content($column, $post_id) {
    switch ($column) {
        case 'restaurant_title':
            $title = get_field('restaurant_title', $post_id);
            echo $title ? '<strong>' . esc_html($title) . '</strong>' : '—';
            break;
        case 'restaurant_title_secondary':
            $title_secondary = get_field('restaurant_title_secondary', $post_id);
            echo $title_secondary ? '<strong style="color: #666;">' . esc_html($title_secondary) . '</strong>' : '—';
            break;
        case 'restaurant_subtitle':
            $subtitle = get_field('restaurant_subtitle', $post_id);
            echo $subtitle ? '<em>' . esc_html($subtitle) . '</em>' : '—';
            break;
        case 'restaurant_subtitle_secondary':
            $subtitle_secondary = get_field('restaurant_subtitle_secondary', $post_id);
            echo $subtitle_secondary ? '<em style="color: #999; text-transform: uppercase; font-size: 11px;">' . esc_html($subtitle_secondary) . '</em>' : '—';
            break;
        case 'restaurant_address':
            $address = get_field('address', $post_id);
            echo $address ? esc_html($address) : '—';
            break;
        case 'restaurant_phone':
            $phone = get_field('phone', $post_id);
            echo $phone ? esc_html($phone) : '—';
            break;
    }
}

/**
 * Personalizar las columnas del admin para menús semanales
 */
add_filter('manage_weekly_menu_posts_columns', 'lamusa_weekly_menu_columns');
function lamusa_weekly_menu_columns($columns) {
    $new_columns = array();
    $new_columns['cb'] = $columns['cb'];
    $new_columns['title'] = $columns['title'];
    $new_columns['menu_restaurant'] = __('Restaurante', 'lamusa-core');
    $new_columns['menu_start_date'] = __('Fecha Inicio', 'lamusa-core');
    $new_columns['menu_end_date'] = __('Fecha Fin', 'lamusa-core');
    $new_columns['date'] = $columns['date'];
    
    return $new_columns;
}

/**
 * Rellenar las columnas personalizadas para menús semanales
 */
add_action('manage_weekly_menu_posts_custom_column', 'lamusa_weekly_menu_column_content', 10, 2);
function lamusa_weekly_menu_column_content($column, $post_id) {
    switch ($column) {
        case 'menu_restaurant':
            $restaurant = get_field('restaurant', $post_id);
            if ($restaurant) {
                echo '<a href="' . get_edit_post_link($restaurant->ID) . '">' . esc_html($restaurant->post_title) . '</a>';
            } else {
                echo '—';
            }
            break;
        case 'menu_start_date':
            $start_date = get_field('start_date', $post_id);
            echo $start_date ? date('d/m/Y', strtotime($start_date)) : '—';
            break;
        case 'menu_end_date':
            $end_date = get_field('end_date', $post_id);
            echo $end_date ? date('d/m/Y', strtotime($end_date)) : '—';
            break;
    }
}

/**
 * Hacer las columnas ordenables
 */
add_filter('manage_edit-weekly_menu_sortable_columns', 'lamusa_weekly_menu_sortable_columns');
function lamusa_weekly_menu_sortable_columns($columns) {
    $columns['menu_start_date'] = 'menu_start_date';
    $columns['menu_end_date'] = 'menu_end_date';
    return $columns;
}

/**
 * Manejar la ordenación personalizada
 */
add_action('pre_get_posts', 'lamusa_weekly_menu_orderby');
function lamusa_weekly_menu_orderby($query) {
    if (!is_admin() || !$query->is_main_query()) {
        return;
    }

    if ('menu_start_date' === $query->get('orderby')) {
        $query->set('meta_key', 'start_date');
        $query->set('orderby', 'meta_value');
    }

    if ('menu_end_date' === $query->get('orderby')) {
        $query->set('meta_key', 'end_date');
        $query->set('orderby', 'meta_value');
    }
}
