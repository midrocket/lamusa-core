<?php
/**
 * Funciones del área de administración
 */

// Prevenir acceso directo
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Eliminar metaboxes innecesarios de menús semanales
 */
add_action('add_meta_boxes', 'lamusa_remove_unnecessary_metaboxes');
function lamusa_remove_unnecessary_metaboxes() {
    // Eliminar metabox de imagen destacada
    remove_meta_box('postimagediv', 'weekly_menu', 'side');
    
    // Eliminar metabox de alérgenos (taxonomía)
    remove_meta_box('tagsdiv-allergen', 'weekly_menu', 'side');
    remove_meta_box('allergen_div', 'weekly_menu', 'side');
}

/**
 * Eliminar enlace permanente de menús semanales
 */
add_filter('get_sample_permalink_html', 'lamusa_remove_permalink_from_menus', 10, 2);
function lamusa_remove_permalink_from_menus($return, $post_id) {
    $post = get_post($post_id);
    if ($post && $post->post_type === 'weekly_menu') {
        return ''; // No mostrar enlace permanente
    }
    return $return;
}

/**
 * Eliminar botón de previsualizar menú
 */
add_action('admin_head-post.php', 'lamusa_remove_preview_button');
add_action('admin_head-post-new.php', 'lamusa_remove_preview_button');
function lamusa_remove_preview_button() {
    global $post_type;
    
    if ($post_type === 'weekly_menu') {
        ?>
        <style>
        #preview-action,
        .lamusa-preview-menu {
            display: none !important;
        }
        </style>
        
        <script>
        jQuery(document).ready(function($) {
            // Eliminar cualquier botón de previsualizar que pueda existir
            $('.lamusa-preview-menu').remove();
            $('#preview-action').remove();
        });
        </script>
        <?php
    }
}

/**
 * Añadir metabox de información en el editor de menús semanales
 * DESACTIVADO - Causa bucles infinitos
 */
// add_action('add_meta_boxes', 'lamusa_add_menu_info_metabox');
function lamusa_add_menu_info_metabox() {
    add_meta_box(
        'lamusa_menu_info',
        __('Información del Menú', 'lamusa-core'),
        'lamusa_menu_info_metabox_callback',
        'weekly_menu',
        'side',
        'high'
    );
}

/**
 * Callback para el metabox de información del menú
 */
function lamusa_menu_info_metabox_callback($post) {
    // Solo mostrar información si el post ya existe (no en auto-draft)
    if (!$post || $post->post_status === 'auto-draft') {
        echo '<div class="lamusa-menu-info">';
        echo '<p><em>' . __('Guarda el menú para ver la información', 'lamusa-core') . '</em></p>';
        echo '</div>';
        return;
    }
    
    // Usar get_post_meta directamente para evitar que ACF cargue campos innecesarios
    $restaurant_id = get_post_meta($post->ID, 'restaurant', true);
    $restaurant = $restaurant_id ? get_post($restaurant_id) : null;
    $start_date = get_post_meta($post->ID, 'start_date', true);
    $end_date = get_post_meta($post->ID, 'end_date', true);
    $menu_active = get_post_meta($post->ID, 'menu_active', true);
    
    echo '<div class="lamusa-menu-info">';
    
    if ($restaurant) {
        echo '<p><strong>' . __('Restaurante:', 'lamusa-core') . '</strong><br>';
        echo '<a href="' . get_edit_post_link($restaurant->ID) . '">' . esc_html($restaurant->post_title) . '</a></p>';
    }
    
    if ($start_date && $end_date) {
        echo '<p><strong>' . __('Período:', 'lamusa-core') . '</strong><br>';
        echo date('d/m/Y', strtotime($start_date)) . ' - ' . date('d/m/Y', strtotime($end_date)) . '</p>';
        
        // Mostrar estado del menú
        $today = current_time('Y-m-d');
        if ($today < $start_date) {
            echo '<p class="menu-status menu-future"><span class="dashicons dashicons-clock"></span> ' . __('Futuro', 'lamusa-core') . '</p>';
        } elseif ($today > $end_date) {
            echo '<p class="menu-status menu-expired"><span class="dashicons dashicons-dismiss"></span> ' . __('Expirado', 'lamusa-core') . '</p>';
        } else {
            echo '<p class="menu-status menu-active"><span class="dashicons dashicons-yes"></span> ' . __('Activo', 'lamusa-core') . '</p>';
        }
    }
    
    if ($menu_active) {
        echo '<p class="menu-published"><span class="dashicons dashicons-visibility"></span> ' . __('Publicado', 'lamusa-core') . '</p>';
    } else {
        echo '<p class="menu-draft"><span class="dashicons dashicons-hidden"></span> ' . __('Borrador', 'lamusa-core') . '</p>';
    }
    
    // Verificar conflictos de fechas solo si tenemos todos los datos
    if ($restaurant && $start_date && $end_date && function_exists('lamusa_validate_menu_date_overlap')) {
        try {
            $conflict = lamusa_validate_menu_date_overlap($restaurant->ID, $start_date, $end_date, $post->ID);
            if ($conflict) {
                echo '<div class="notice notice-warning inline">';
                echo '<p><strong>' . __('¡Atención!', 'lamusa-core') . '</strong><br>';
                echo sprintf(
                    __('Este menú se solapa con "%s" (%s - %s)', 'lamusa-core'),
                    $conflict['menu_title'],
                    date('d/m/Y', strtotime($conflict['start_date'])),
                    date('d/m/Y', strtotime($conflict['end_date']))
                );
                echo '</p></div>';
            }
        } catch (Exception $e) {
            // Silenciar errores en el metabox para evitar problemas con AJAX
            error_log('Error en metabox de menú: ' . $e->getMessage());
        }
    }
    
    echo '</div>';
    
    // CSS para el metabox
    ?>
    <style>
    .lamusa-menu-info .menu-status {
        padding: 5px 10px;
        border-radius: 3px;
        margin: 10px 0;
    }
    .menu-active { background: #d4edda; color: #155724; }
    .menu-future { background: #cce5ff; color: #004085; }
    .menu-expired { background: #f8d7da; color: #721c24; }
    .menu-published { color: #155724; }
    .menu-draft { color: #721c24; }
    </style>
    <?php
}

/**
 * Añadir enlaces rápidos en la barra de administración
 */
add_action('admin_bar_menu', 'lamusa_add_admin_bar_links', 100);
function lamusa_add_admin_bar_links($wp_admin_bar) {
    if (!current_user_can('edit_posts')) {
        return;
    }
    
    $wp_admin_bar->add_node(array(
        'id' => 'lamusa-core',
        'title' => __('La Musa', 'lamusa-core'),
        'href' => admin_url('edit.php?post_type=restaurant'),
    ));
    
    $wp_admin_bar->add_node(array(
        'parent' => 'lamusa-core',
        'id' => 'lamusa-restaurants',
        'title' => __('Restaurantes', 'lamusa-core'),
        'href' => admin_url('edit.php?post_type=restaurant'),
    ));
    
    $wp_admin_bar->add_node(array(
        'parent' => 'lamusa-core',
        'id' => 'lamusa-menus',
        'title' => __('Menús Semanales', 'lamusa-core'),
        'href' => admin_url('edit.php?post_type=weekly_menu'),
    ));
    
    $wp_admin_bar->add_node(array(
        'parent' => 'lamusa-core',
        'id' => 'lamusa-allergens',
        'title' => __('Alérgenos', 'lamusa-core'),
        'href' => admin_url('edit-tags.php?taxonomy=allergen&post_type=weekly_menu'),
    ));
}

/**
 * Añadir widgets del dashboard personalizados
 */
add_action('wp_dashboard_setup', 'lamusa_add_dashboard_widgets');
function lamusa_add_dashboard_widgets() {
    if (current_user_can('edit_posts')) {
        wp_add_dashboard_widget(
            'lamusa_menu_overview',
            __('Resumen de Menús La Musa', 'lamusa-core'),
            'lamusa_dashboard_menu_overview'
        );
    }
}

/**
 * Callback para el widget del dashboard
 */
function lamusa_dashboard_menu_overview() {
    // Verificar que las funciones existan antes de usarlas
    if (!function_exists('lamusa_get_restaurants')) {
        echo '<p>' . __('Funciones del plugin no disponibles', 'lamusa-core') . '</p>';
        return;
    }
    
    try {
        $restaurants = lamusa_get_restaurants();
        $today = current_time('Y-m-d');
        
        echo '<div class="lamusa-dashboard-overview">';
        
        foreach ($restaurants as $restaurant) {
            $active_menu = function_exists('lamusa_get_active_weekly_menu') ? lamusa_get_active_weekly_menu($restaurant->ID) : null;
            $next_menu = function_exists('lamusa_get_next_weekly_menu') ? lamusa_get_next_weekly_menu($restaurant->ID) : null;
            
            echo '<div class="restaurant-overview">';
            echo '<h4>' . esc_html($restaurant->post_title) . '</h4>';
            
            if ($active_menu) {
                $end_date = get_post_meta($active_menu->ID, 'end_date', true);
                $days_left = $end_date ? max(0, (strtotime($end_date) - strtotime($today)) / (60*60*24)) : 0;
                
                echo '<p class="current-menu">';
                echo '<span class="dashicons dashicons-yes" style="color: green;"></span> ';
                echo sprintf(__('Menú activo: %s', 'lamusa-core'), $active_menu->post_title);
                echo '<br><small>' . sprintf(__('Expira en %d días', 'lamusa-core'), $days_left) . '</small>';
                echo '</p>';
            } else {
                echo '<p class="no-menu">';
                echo '<span class="dashicons dashicons-warning" style="color: orange;"></span> ';
                echo __('Sin menú activo', 'lamusa-core');
                echo '</p>';
            }
            
            if ($next_menu) {
                $start_date = get_post_meta($next_menu->ID, 'start_date', true);
                $days_until = $start_date ? max(0, (strtotime($start_date) - strtotime($today)) / (60*60*24)) : 0;
                
                echo '<p class="next-menu">';
                echo '<span class="dashicons dashicons-clock"></span> ';
                echo sprintf(__('Próximo: %s', 'lamusa-core'), $next_menu->post_title);
                echo '<br><small>' . sprintf(__('Comienza en %d días', 'lamusa-core'), $days_until) . '</small>';
                echo '</p>';
            }
            
            echo '<p class="actions">';
            echo '<a href="' . admin_url('edit.php?post_type=weekly_menu&restaurant=' . $restaurant->ID) . '" class="button button-small">' . __('Ver menús', 'lamusa-core') . '</a> ';
            echo '<a href="' . admin_url('post-new.php?post_type=weekly_menu&restaurant=' . $restaurant->ID) . '" class="button button-primary button-small">' . __('Nuevo menú', 'lamusa-core') . '</a>';
            echo '</p>';
            
            echo '</div>';
            
            if ($restaurant !== end($restaurants)) {
                echo '<hr>';
            }
        }
        
        echo '</div>';
        
        // CSS para el widget
        ?>
        <style>
        .lamusa-dashboard-overview .restaurant-overview {
            margin-bottom: 15px;
        }
        .lamusa-dashboard-overview h4 {
            margin-bottom: 8px;
            color: #1d2327;
        }
        .lamusa-dashboard-overview p {
            margin: 5px 0;
        }
        .lamusa-dashboard-overview .actions {
            margin-top: 10px;
        }
        .lamusa-dashboard-overview .dashicons {
            vertical-align: middle;
        }
        </style>
        <?php
        
    } catch (Exception $e) {
        echo '<p>' . __('Error al cargar la información del dashboard', 'lamusa-core') . '</p>';
        error_log('Error en dashboard widget: ' . $e->getMessage());
    }
}

/**
 * Añadir filtros personalizados en la lista de menús semanales
 */
add_action('restrict_manage_posts', 'lamusa_add_menu_filters');
function lamusa_add_menu_filters() {
    global $typenow;
    
    if ($typenow === 'weekly_menu') {
        // Filtro por restaurante
        $restaurants = lamusa_get_restaurants();
        $selected_restaurant = $_GET['restaurant'] ?? '';
        
        echo '<select name="restaurant">';
        echo '<option value="">' . __('Todos los restaurantes', 'lamusa-core') . '</option>';
        foreach ($restaurants as $restaurant) {
            $selected = selected($selected_restaurant, $restaurant->ID, false);
            echo '<option value="' . $restaurant->ID . '"' . $selected . '>' . esc_html($restaurant->post_title) . '</option>';
        }
        echo '</select>';
        
        // Filtro por estado del menú
        $selected_status = $_GET['menu_status'] ?? '';
        echo '<select name="menu_status">';
        echo '<option value="">' . __('Todos los estados', 'lamusa-core') . '</option>';
        echo '<option value="active"' . selected($selected_status, 'active', false) . '>' . __('Activos', 'lamusa-core') . '</option>';
        echo '<option value="future"' . selected($selected_status, 'future', false) . '>' . __('Futuros', 'lamusa-core') . '</option>';
        echo '<option value="expired"' . selected($selected_status, 'expired', false) . '>' . __('Expirados', 'lamusa-core') . '</option>';
        echo '</select>';
    }
}

/**
 * Aplicar filtros personalizados
 */
add_filter('parse_query', 'lamusa_apply_menu_filters');
function lamusa_apply_menu_filters($query) {
    global $pagenow, $typenow;
    
    if ($pagenow === 'edit.php' && $typenow === 'weekly_menu' && $query->is_admin && $query->is_main_query()) {
        $meta_query = array();
        
        // Filtro por restaurante
        if (!empty($_GET['restaurant'])) {
            $meta_query[] = array(
                'key' => 'restaurant',
                'value' => intval($_GET['restaurant']),
                'compare' => '='
            );
        }
        
        // Filtro por estado del menú
        if (!empty($_GET['menu_status'])) {
            $today = current_time('Y-m-d');
            
            switch ($_GET['menu_status']) {
                case 'active':
                    $meta_query[] = array(
                        'key' => 'start_date',
                        'value' => $today,
                        'compare' => '<='
                    );
                    $meta_query[] = array(
                        'key' => 'end_date',
                        'value' => $today,
                        'compare' => '>='
                    );
                    break;
                    
                case 'future':
                    $meta_query[] = array(
                        'key' => 'start_date',
                        'value' => $today,
                        'compare' => '>'
                    );
                    break;
                    
                case 'expired':
                    $meta_query[] = array(
                        'key' => 'end_date',
                        'value' => $today,
                        'compare' => '<'
                    );
                    break;
            }
        }
        
        if (!empty($meta_query)) {
            $meta_query['relation'] = 'AND';
            $query->set('meta_query', $meta_query);
        }
    }
}

/**
 * Añadir enlaces de acción rápida en la lista de restaurantes
 */
add_filter('post_row_actions', 'lamusa_add_restaurant_quick_actions', 10, 2);
function lamusa_add_restaurant_quick_actions($actions, $post) {
    if ($post->post_type === 'restaurant') {
        $actions['view_menus'] = sprintf(
            '<a href="%s">%s</a>',
            admin_url('edit.php?post_type=weekly_menu&restaurant=' . $post->ID),
            __('Ver menús', 'lamusa-core')
        );
        
        $actions['new_menu'] = sprintf(
            '<a href="%s">%s</a>',
            admin_url('post-new.php?post_type=weekly_menu&restaurant=' . $post->ID),
            __('Nuevo menú', 'lamusa-core')
        );
    }
    
    return $actions;
}

/**
 * Pre-rellenar el campo restaurante en nuevos menús
 */
add_action('acf/load_value/name=restaurant', 'lamusa_preselect_restaurant_in_menu', 10, 3);
function lamusa_preselect_restaurant_in_menu($value, $post_id, $field) {
    // Solo en el admin y para nuevos posts, evitar en AJAX
    if (is_admin() && !wp_doing_ajax() && empty($value) && !empty($_GET['restaurant'])) {
        $restaurant_id = intval($_GET['restaurant']);
        if ($restaurant_id > 0 && get_post_type($restaurant_id) === 'restaurant') {
            return $restaurant_id;
        }
    }
    
    return $value;
}

/**
 * Añadir notificaciones para menús que expiran pronto
 */
add_action('admin_notices', 'lamusa_menu_expiry_notices');
function lamusa_menu_expiry_notices() {
    $screen = get_current_screen();
    
    // Solo mostrar en páginas relevantes
    if (!in_array($screen->base, ['dashboard', 'edit']) || !current_user_can('edit_posts')) {
        return;
    }
    
    $today = current_time('Y-m-d');
    $warning_date = date('Y-m-d', strtotime('+3 days'));
    
    $expiring_menus = get_posts(array(
        'post_type' => 'weekly_menu',
        'post_status' => 'publish',
        'posts_per_page' => -1,
        'meta_query' => array(
            'relation' => 'AND',
            array(
                'key' => 'menu_active',
                'value' => '1',
                'compare' => '='
            ),
            array(
                'key' => 'end_date',
                'value' => $today,
                'compare' => '>='
            ),
            array(
                'key' => 'end_date',
                'value' => $warning_date,
                'compare' => '<='
            )
        )
    ));
    
    if (!empty($expiring_menus)) {
        echo '<div class="notice notice-warning is-dismissible">';
        echo '<p><strong>' . __('Menús que expiran pronto:', 'lamusa-core') . '</strong></p>';
        echo '<ul>';
        
        foreach ($expiring_menus as $menu) {
            $restaurant_id = get_post_meta($menu->ID, 'restaurant', true);
            $restaurant = $restaurant_id ? get_post($restaurant_id) : null;
            $end_date = get_post_meta($menu->ID, 'end_date', true);
            $days_left = max(0, (strtotime($end_date) - strtotime($today)) / (60*60*24));
            
            echo '<li>';
            echo sprintf(
                __('%s (%s) - expira en %d días', 'lamusa-core'),
                $menu->post_title,
                $restaurant ? $restaurant->post_title : __('Sin restaurante', 'lamusa-core'),
                $days_left
            );
            echo ' <a href="' . get_edit_post_link($menu->ID) . '">' . __('Editar', 'lamusa-core') . '</a>';
            echo '</li>';
        }
        
        echo '</ul>';
        echo '</div>';
    }
}
