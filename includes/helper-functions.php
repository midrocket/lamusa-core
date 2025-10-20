<?php
/**
 * Funciones helper para el plugin La Musa Core
 */

// Prevenir acceso directo
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Obtener el menú semanal activo para un restaurante
 *
 * @param int $restaurant_id ID del restaurante
 * @param string $date Fecha específica (Y-m-d), por defecto hoy
 * @return WP_Post|false Post del menú semanal o false si no existe
 */
function lamusa_get_active_weekly_menu($restaurant_id, $date = null) {
    if (!$date) {
        $date = current_time('Y-m-d');
    }
    
    $menus = get_posts(array(
        'post_type' => 'weekly_menu',
        'post_status' => 'publish',
        'posts_per_page' => 1,
        'orderby' => 'meta_value',
        'order' => 'DESC',
        'meta_key' => 'start_date',
        'meta_query' => array(
            'relation' => 'AND',
            array(
                'key' => 'restaurant',
                'value' => $restaurant_id,
                'compare' => '='
            ),
            array(
                'key' => 'menu_active',
                'value' => '1',
                'compare' => '='
            ),
            array(
                'key' => 'start_date',
                'value' => $date,
                'compare' => '<='
            ),
            array(
                'key' => 'end_date',
                'value' => $date,
                'compare' => '>='
            )
        )
    ));
    
    return !empty($menus) ? $menus[0] : false;
}

/**
 * Obtener el menú del día específico
 *
 * @param int $menu_id ID del menú semanal
 * @param string $day_name Nombre del día (lunes, martes, etc.) o fecha Y-m-d
 * @return array|false Datos del día o false si no existe
 */
function lamusa_get_menu_day($menu_id, $day_name = null) {
    if (!$day_name) {
        $day_name = strtolower(date_i18n('l'));
        // Convertir nombres en inglés a español
        $day_translations = array(
            'monday' => 'lunes',
            'tuesday' => 'martes',
            'wednesday' => 'miercoles',
            'thursday' => 'jueves',
            'friday' => 'viernes',
            'saturday' => 'sabado',
            'sunday' => 'domingo'
        );
        $day_name = $day_translations[$day_name] ?? $day_name;
    }
    
    $menu_days = get_post_meta($menu_id, 'menu_days', true);
    
    if (!$menu_days) {
        return false;
    }
    
    foreach ($menu_days as $day) {
        // Buscar por nombre del día
        if ($day['day_name'] === $day_name) {
            return $day;
        }
        
        // Buscar por fecha específica si se proporciona una fecha
        if (strpos($day_name, '-') !== false && !empty($day['day_date'])) {
            if ($day['day_date'] === $day_name) {
                return $day;
            }
        }
    }
    
    return false;
}

/**
 * Obtener todos los restaurantes
 *
 * @param array $args Argumentos adicionales para get_posts
 * @return array Array de posts de restaurantes
 */
function lamusa_get_restaurants($args = array()) {
    $default_args = array(
        'post_type' => 'restaurant',
        'post_status' => 'publish',
        'posts_per_page' => -1,
        'orderby' => 'title',
        'order' => 'ASC'
    );
    
    $args = wp_parse_args($args, $default_args);
    
    return get_posts($args);
}

/**
 * Obtener restaurantes destacados
 *
 * @return array Array de posts de restaurantes destacados
 */
function lamusa_get_featured_restaurants() {
    return lamusa_get_restaurants(array(
        'meta_query' => array(
            array(
                'key' => 'featured',
                'value' => '1',
                'compare' => '='
            )
        )
    ));
}

/**
 * Verificar si un restaurante tiene menús semanales
 *
 * @param int $restaurant_id ID del restaurante
 * @return bool
 */
function lamusa_restaurant_has_weekly_menus($restaurant_id) {
    $show_weekly_menu = get_post_meta($restaurant_id, 'show_weekly_menu', true);
    
    if (!$show_weekly_menu) {
        return false;
    }
    
    $menus = get_posts(array(
        'post_type' => 'weekly_menu',
        'post_status' => 'publish',
        'posts_per_page' => 1,
        'meta_query' => array(
            'relation' => 'AND',
            array(
                'key' => 'restaurant',
                'value' => $restaurant_id,
                'compare' => '='
            ),
            array(
                'key' => 'menu_active',
                'value' => '1',
                'compare' => '='
            )
        )
    ));
    
    return !empty($menus);
}

/**
 * Formatear alérgenos para mostrar
 *
 * @param array $allergens Array de términos de alérgenos
 * @param string $type Tipo: 'contains' o 'traces'
 * @return string HTML formateado de alérgenos
 */
function lamusa_format_allergens($allergens, $type = 'contains') {
    if (empty($allergens)) {
        return '';
    }
    
    $output = '';
    $prefix = $type === 'traces' ? 'Puede contener trazas de: ' : 'Contiene: ';
    $class = $type === 'traces' ? 'allergen-traces' : 'allergen-contains';
    
    $allergen_items = array();
    
    foreach ($allergens as $allergen) {
        $icon = get_term_meta($allergen->term_id, 'allergen_icon', true);
        $color = get_term_meta($allergen->term_id, 'allergen_color', true);
        
        $item = '<span class="allergen-item ' . $class . '" style="color: ' . esc_attr($color) . ';">';
        
        if ($icon) {
            $item .= '<i class="' . esc_attr($icon) . '"></i> ';
        }
        
        $item .= esc_html($allergen->name) . '</span>';
        
        $allergen_items[] = $item;
    }
    
    $output = '<div class="allergen-list ' . $class . '">';
    $output .= '<span class="allergen-prefix">' . $prefix . '</span>';
    $output .= implode(', ', $allergen_items);
    $output .= '</div>';
    
    return $output;
}

/**
 * Obtener alérgenos de un plato específico
 *
 * @param array $dish Datos del plato
 * @return array Array con 'contains' y 'traces'
 */
function lamusa_get_dish_allergens($dish) {
    $contains = array();
    $traces = array();
    
    // Los alérgenos ahora se guardan como IDs, necesitamos convertirlos a objetos
    if (!empty($dish['allergens_contains'])) {
        foreach ($dish['allergens_contains'] as $allergen_id) {
            $allergen = get_term($allergen_id, 'allergen');
            if ($allergen && !is_wp_error($allergen)) {
                $contains[] = $allergen;
            }
        }
    }
    
    if (!empty($dish['allergens_traces'])) {
        foreach ($dish['allergens_traces'] as $allergen_id) {
            $allergen = get_term($allergen_id, 'allergen');
            if ($allergen && !is_wp_error($allergen)) {
                $traces[] = $allergen;
            }
        }
    }
    
    return array(
        'contains' => $contains,
        'traces' => $traces
    );
}

/**
 * Generar URL de página específica del restaurante
 *
 * @param int $restaurant_id ID del restaurante
 * @param string $page_type Tipo de página: 'ficha', 'reserva', 'menu', 'alergenos'
 * @return string URL de la página
 */
function lamusa_get_restaurant_page_url($restaurant_id, $page_type = 'ficha') {
    $restaurant = get_post($restaurant_id);
    
    if (!$restaurant) {
        return '';
    }
    
    $base_slug = $restaurant->post_name;
    $page_urls = array(
        'ficha' => home_url('/restaurante/' . $base_slug . '/'),
        'reserva' => home_url('/restaurante/' . $base_slug . '/reserva/'),
        'menu' => home_url('/restaurante/' . $base_slug . '/menu-del-dia/'),
        'alergenos' => home_url('/restaurante/' . $base_slug . '/alergenos/')
    );
    
    return $page_urls[$page_type] ?? $page_urls['ficha'];
}

/**
 * Validar si hay solapamiento de fechas en menús semanales
 *
 * @param int $restaurant_id ID del restaurante
 * @param string $start_date Fecha de inicio (Y-m-d)
 * @param string $end_date Fecha de fin (Y-m-d)
 * @param int $exclude_menu_id ID del menú a excluir de la validación
 * @return array|false Array con información del conflicto o false si no hay conflicto
 */
function lamusa_validate_menu_date_overlap($restaurant_id, $start_date, $end_date, $exclude_menu_id = 0) {
    $conflicting_menus = get_posts(array(
        'post_type' => 'weekly_menu',
        'post_status' => 'publish',
        'posts_per_page' => -1,
        'exclude' => array($exclude_menu_id),
        'meta_query' => array(
            'relation' => 'AND',
            array(
                'key' => 'restaurant',
                'value' => $restaurant_id,
                'compare' => '='
            ),
            array(
                'key' => 'menu_active',
                'value' => '1',
                'compare' => '='
            ),
            array(
                'relation' => 'OR',
                // Nuevo inicio dentro del rango existente
                array(
                    'relation' => 'AND',
                    array(
                        'key' => 'start_date',
                        'value' => $start_date,
                        'compare' => '<='
                    ),
                    array(
                        'key' => 'end_date',
                        'value' => $start_date,
                        'compare' => '>='
                    )
                ),
                // Nuevo fin dentro del rango existente
                array(
                    'relation' => 'AND',
                    array(
                        'key' => 'start_date',
                        'value' => $end_date,
                        'compare' => '<='
                    ),
                    array(
                        'key' => 'end_date',
                        'value' => $end_date,
                        'compare' => '>='
                    )
                ),
                // Nuevo rango engloba al existente
                array(
                    'relation' => 'AND',
                    array(
                        'key' => 'start_date',
                        'value' => $start_date,
                        'compare' => '>='
                    ),
                    array(
                        'key' => 'end_date',
                        'value' => $end_date,
                        'compare' => '<='
                    )
                )
            )
        )
    ));
    
    if (!empty($conflicting_menus)) {
        $conflict = $conflicting_menus[0];
        return array(
            'menu_id' => $conflict->ID,
            'menu_title' => $conflict->post_title,
            'start_date' => get_post_meta($conflict->ID, 'start_date', true),
            'end_date' => get_post_meta($conflict->ID, 'end_date', true)
        );
    }
    
    return false;
}

/**
 * Obtener el siguiente menú semanal para un restaurante
 *
 * @param int $restaurant_id ID del restaurante
 * @param string $date Fecha de referencia (Y-m-d), por defecto hoy
 * @return WP_Post|false Post del siguiente menú o false si no existe
 */
function lamusa_get_next_weekly_menu($restaurant_id, $date = null) {
    if (!$date) {
        $date = current_time('Y-m-d');
    }
    
    $menus = get_posts(array(
        'post_type' => 'weekly_menu',
        'post_status' => 'publish',
        'posts_per_page' => 1,
        'orderby' => 'meta_value',
        'order' => 'ASC',
        'meta_key' => 'start_date',
        'meta_query' => array(
            'relation' => 'AND',
            array(
                'key' => 'restaurant',
                'value' => $restaurant_id,
                'compare' => '='
            ),
            array(
                'key' => 'menu_active',
                'value' => '1',
                'compare' => '='
            ),
            array(
                'key' => 'start_date',
                'value' => $date,
                'compare' => '>'
            )
        )
    ));
    
    return !empty($menus) ? $menus[0] : false;
}

/**
 * Obtener todos los menús semanales de un restaurante
 *
 * @param int $restaurant_id ID del restaurante
 * @param bool $active_only Solo menús activos
 * @return array Array de posts de menús semanales
 */
function lamusa_get_restaurant_menus($restaurant_id, $active_only = true) {
    $meta_query = array(
        array(
            'key' => 'restaurant',
            'value' => $restaurant_id,
            'compare' => '='
        )
    );
    
    if ($active_only) {
        $meta_query[] = array(
            'key' => 'menu_active',
            'value' => '1',
            'compare' => '='
        );
    }
    
    return get_posts(array(
        'post_type' => 'weekly_menu',
        'post_status' => 'publish',
        'posts_per_page' => -1,
        'orderby' => 'meta_value',
        'order' => 'DESC',
        'meta_key' => 'start_date',
        'meta_query' => $meta_query
    ));
}

/**
 * Generar resumen de alérgenos para un menú completo
 *
 * @param int $menu_id ID del menú semanal
 * @return array Array con todos los alérgenos encontrados organizados
 */
function lamusa_get_menu_allergens_summary($menu_id) {
    $allergen_groups = get_post_meta($menu_id, 'allergen_groups', true);
    $summary = array(
        'contains' => array(),
        'traces' => array()
    );
    
    if (!$allergen_groups) {
        return $summary;
    }
    
    foreach ($allergen_groups as $group) {
        if (!empty($group['dishes'])) {
            foreach ($group['dishes'] as $dish) {
                // Alérgenos que contiene
                if (!empty($dish['allergens_contains'])) {
                    foreach ($dish['allergens_contains'] as $allergen) {
                        $summary['contains'][$allergen->term_id] = $allergen;
                    }
                }
                
                // Trazas de alérgenos
                if (!empty($dish['allergens_traces'])) {
                    foreach ($dish['allergens_traces'] as $allergen) {
                        $summary['traces'][$allergen->term_id] = $allergen;
                    }
                }
            }
        }
    }
    
    // Convertir a arrays simples y ordenar
    $summary['contains'] = array_values($summary['contains']);
    $summary['traces'] = array_values($summary['traces']);
    
    // Ordenar por orden personalizado
    usort($summary['contains'], function($a, $b) {
        $order_a = get_term_meta($a->term_id, 'allergen_order', true) ?: 999;
        $order_b = get_term_meta($b->term_id, 'allergen_order', true) ?: 999;
        return $order_a - $order_b;
    });
    
    usort($summary['traces'], function($a, $b) {
        $order_a = get_term_meta($a->term_id, 'allergen_order', true) ?: 999;
        $order_b = get_term_meta($b->term_id, 'allergen_order', true) ?: 999;
        return $order_a - $order_b;
    });
    
    return $summary;
}

/**
 * Helper para debug - mostrar información del menú actual
 *
 * @param int $restaurant_id ID del restaurante
 */
function lamusa_debug_menu_info($restaurant_id) {
    if (!current_user_can('manage_options')) {
        return;
    }
    
    $menu = lamusa_get_active_weekly_menu($restaurant_id);
    
    echo '<div style="background: #f1f1f1; padding: 15px; margin: 15px 0; border-left: 4px solid #0073aa;">';
    echo '<h4>Debug: Información del Menú</h4>';
    
    if ($menu) {
        echo '<p><strong>Menú encontrado:</strong> ' . $menu->post_title . '</p>';
        echo '<p><strong>ID:</strong> ' . $menu->ID . '</p>';
        echo '<p><strong>Fechas:</strong> ' . get_post_meta($menu->ID, 'start_date', true) . ' - ' . get_post_meta($menu->ID, 'end_date', true) . '</p>';
        
        $today = current_time('Y-m-d');
        $day_info = lamusa_get_menu_day($menu->ID);
        
        echo '<p><strong>Fecha actual:</strong> ' . $today . '</p>';
        echo '<p><strong>Menú del día:</strong> ' . ($day_info ? 'Encontrado' : 'No encontrado') . '</p>';
        
        if ($day_info) {
            echo '<p><strong>Día:</strong> ' . $day_info['day_name'] . '</p>';
            echo '<p><strong>Activo:</strong> ' . ($day_info['day_active'] ? 'Sí' : 'No') . '</p>';
        }
    } else {
        echo '<p><strong>No se encontró menú activo para este restaurante</strong></p>';
    }
    
    echo '</div>';
}
