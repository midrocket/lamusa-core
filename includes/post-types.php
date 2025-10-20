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
    $new_columns['menu_dates'] = __('Período', 'lamusa-core');
    $new_columns['menu_status'] = __('Estado', 'lamusa-core');
    $new_columns['menu_active'] = __('Activo', 'lamusa-core');
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
                $is_active_for_restaurant = lamusa_is_menu_active_for_restaurant($post_id);
                $badge = '';
                if ($is_active_for_restaurant) {
                    $badge = ' <span style="background:#10b981;color:white;padding:2px 8px;border-radius:10px;font-size:10px;font-weight:bold;margin-left:5px;">ACTIVO</span>';
                }
                echo '<a href="' . get_edit_post_link($restaurant->ID) . '">' . esc_html($restaurant->post_title) . '</a>' . $badge;
            } else {
                echo '—';
            }
            break;
        case 'menu_dates':
            $start_date = get_field('start_date', $post_id);
            $end_date = get_field('end_date', $post_id);
            if ($start_date && $end_date) {
                echo '<strong>' . date('d/m/Y', strtotime($start_date)) . '</strong> — <strong>' . date('d/m/Y', strtotime($end_date)) . '</strong>';
                
                // Calcular duración
                $days = (strtotime($end_date) - strtotime($start_date)) / (60 * 60 * 24) + 1;
                echo '<br><small style="color:#666;">(' . $days . ' días)</small>';
            } else {
                echo '—';
            }
            break;
        case 'menu_status':
            $start_date = get_field('start_date', $post_id);
            $end_date = get_field('end_date', $post_id);
            $today = current_time('Y-m-d');
            
            if ($start_date && $end_date) {
                if ($today < $start_date) {
                    echo '<span style="color:#3b82f6;font-weight:600;">● Futuro</span>';
                    $days_until = (strtotime($start_date) - strtotime($today)) / (60 * 60 * 24);
                    echo '<br><small style="color:#666;">Comienza en ' . ceil($days_until) . ' días</small>';
                } elseif ($today > $end_date) {
                    echo '<span style="color:#ef4444;font-weight:600;">● Expirado</span>';
                    $days_ago = (strtotime($today) - strtotime($end_date)) / (60 * 60 * 24);
                    echo '<br><small style="color:#666;">Hace ' . ceil($days_ago) . ' días</small>';
                } else {
                    echo '<span style="color:#10b981;font-weight:600;">● En Curso</span>';
                    $days_left = (strtotime($end_date) - strtotime($today)) / (60 * 60 * 24);
                    echo '<br><small style="color:#666;">Quedan ' . ceil($days_left) . ' días</small>';
                }
            } else {
                echo '—';
            }
            break;
        case 'menu_active':
            $is_active = get_field('menu_active', $post_id);
            if ($is_active == 1 || $is_active === '1' || $is_active === true) {
                echo '<span style="color:#10b981;font-size:20px;">✓</span>';
            } else {
                echo '<span style="color:#ef4444;font-size:20px;">✗</span>';
            }
            break;
    }
}

/**
 * Verificar si un menú es el activo para su restaurante
 */
function lamusa_is_menu_active_for_restaurant($menu_id) {
    $restaurant = get_field('restaurant', $menu_id);
    if (!$restaurant) {
        return false;
    }
    
    $is_active = get_field('menu_active', $menu_id);
    if (!($is_active == 1 || $is_active === '1' || $is_active === true)) {
        return false;
    }
    
    $start_date = get_field('start_date', $menu_id);
    $end_date = get_field('end_date', $menu_id);
    $today = current_time('Y-m-d');
    
    // Verificar si está en el rango de fechas
    if ($start_date && $end_date && $today >= $start_date && $today <= $end_date) {
        return true;
    }
    
    return false;
}

/**
 * Hacer las columnas ordenables
 */
add_filter('manage_edit-weekly_menu_sortable_columns', 'lamusa_weekly_menu_sortable_columns');
function lamusa_weekly_menu_sortable_columns($columns) {
    $columns['menu_dates'] = 'start_date';
    $columns['menu_restaurant'] = 'restaurant';
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

    if ('start_date' === $query->get('orderby')) {
        $query->set('meta_key', 'start_date');
        $query->set('orderby', 'meta_value');
    }

    if ('restaurant' === $query->get('orderby')) {
        $query->set('meta_key', 'restaurant');
        $query->set('orderby', 'meta_value');
    }
}

/**
 * Añadir estilos personalizados a la lista de menús
 */
add_action('admin_head', 'lamusa_weekly_menu_admin_styles');
function lamusa_weekly_menu_admin_styles() {
    global $post_type;
    if ($post_type === 'weekly_menu') {
        ?>
        <style>
        .wp-list-table .column-menu_restaurant {
            width: 20%;
        }
        .wp-list-table .column-menu_dates {
            width: 20%;
        }
        .wp-list-table .column-menu_status {
            width: 15%;
        }
        .wp-list-table .column-menu_active {
            width: 8%;
            text-align: center;
        }
        .wp-list-table .column-menu_active span {
            display: inline-block;
        }
        
        /* Resaltar fila del menú activo */
        .wp-list-table tr.menu-active-row {
            background: #f0fdf4 !important;
        }
        .wp-list-table tr.menu-expired-row {
            background: #fef2f2 !important;
            opacity: 0.7;
        }
        .wp-list-table tr.menu-future-row {
            background: #eff6ff !important;
        }
        </style>
        <?php
    }
}

/**
 * Añadir clases CSS a las filas según el estado
 */
add_filter('post_class', 'lamusa_weekly_menu_row_class', 10, 3);
function lamusa_weekly_menu_row_class($classes, $class, $post_id) {
    if (get_post_type($post_id) === 'weekly_menu') {
        if (lamusa_is_menu_active_for_restaurant($post_id)) {
            $classes[] = 'menu-active-row';
        } else {
            $start_date = get_field('start_date', $post_id);
            $end_date = get_field('end_date', $post_id);
            $today = current_time('Y-m-d');
            
            if ($start_date && $end_date) {
                if ($today < $start_date) {
                    $classes[] = 'menu-future-row';
                } elseif ($today > $end_date) {
                    $classes[] = 'menu-expired-row';
                }
            }
        }
    }
    return $classes;
}
