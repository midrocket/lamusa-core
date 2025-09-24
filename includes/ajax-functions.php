<?php
/**
 * Funciones AJAX para el navegador de menús
 */

// Prevenir acceso directo
if (!defined('ABSPATH')) {
    exit;
}

/**
 * AJAX: Obtener datos del restaurante y menú
 */
add_action('wp_ajax_lamusa_get_restaurant_menu_data', 'lamusa_ajax_get_restaurant_menu_data');
add_action('wp_ajax_nopriv_lamusa_get_restaurant_menu_data', 'lamusa_ajax_get_restaurant_menu_data');
function lamusa_ajax_get_restaurant_menu_data() {
    // Verificar nonce
    if (!wp_verify_nonce($_POST['nonce'], 'lamusa_navigator_nonce')) {
        wp_send_json_error('Nonce inválido');
        return;
    }
    
    $restaurant_id = intval($_POST['restaurant_id']);
    
    if (!$restaurant_id) {
        wp_send_json_error('ID de restaurante inválido');
        return;
    }
    
    $restaurant = get_post($restaurant_id);
    if (!$restaurant || $restaurant->post_type !== 'restaurant') {
        wp_send_json_error('Restaurante no encontrado');
        return;
    }
    
    // Obtener datos del restaurante
    $restaurant_data = array(
        'id' => $restaurant->ID,
        'title' => get_field('restaurant_title', $restaurant->ID) ?: $restaurant->post_title,
        'title_secondary' => get_field('restaurant_title_secondary', $restaurant->ID),
        'subtitle' => get_field('restaurant_subtitle', $restaurant->ID),
        'subtitle_secondary' => get_field('restaurant_subtitle_secondary', $restaurant->ID)
    );
    
    // Obtener menú semanal activo
    $menu = null;
    $menu_data = null;
    
    if (function_exists('lamusa_get_active_weekly_menu')) {
        $menu = lamusa_get_active_weekly_menu($restaurant_id);
    }
    
    if ($menu) {
        $start_date = get_field('start_date', $menu->ID);
        $end_date = get_field('end_date', $menu->ID);
        
        $menu_data = array(
            'id' => $menu->ID,
            'title' => $menu->post_title,
            'price' => get_field('menu_price', $menu->ID),
            'introduction' => get_field('menu_introduction', $menu->ID),
            'dates' => $start_date && $end_date ? 
                date('d/m', strtotime($start_date)) . ' - ' . date('d/m', strtotime($end_date)) : null,
            'start_date' => $start_date,
            'end_date' => $end_date
        );
    }
    
    // Obtener días de la semana
    $days_data = lamusa_get_menu_days_data($menu);
    
    wp_send_json_success(array(
        'restaurant' => $restaurant_data,
        'menu' => $menu_data,
        'days' => $days_data
    ));
}

/**
 * AJAX: Obtener contenido del menú de un día específico
 */
add_action('wp_ajax_lamusa_get_day_menu_content', 'lamusa_ajax_get_day_menu_content');
add_action('wp_ajax_nopriv_lamusa_get_day_menu_content', 'lamusa_ajax_get_day_menu_content');
function lamusa_ajax_get_day_menu_content() {
    // Verificar nonce
    if (!wp_verify_nonce($_POST['nonce'], 'lamusa_navigator_nonce')) {
        wp_send_json_error('Nonce inválido');
        return;
    }
    
    $restaurant_id = intval($_POST['restaurant_id']);
    $day_name = sanitize_text_field($_POST['day_name']);
    
    if (!$restaurant_id || !$day_name) {
        wp_send_json_error('Parámetros inválidos');
        return;
    }
    
    // Obtener menú activo
    if (!function_exists('lamusa_get_active_weekly_menu')) {
        wp_send_json_error('Función no disponible');
        return;
    }
    
    $menu = lamusa_get_active_weekly_menu($restaurant_id);
    if (!$menu) {
        wp_send_json_error('No hay menú activo para este restaurante');
        return;
    }
    
    // Obtener contenido del día
    $day_content = '';
    if (function_exists('lamusa_get_menu_day')) {
        $day_menu = lamusa_get_menu_day($menu->ID, $day_name);
        
        if ($day_menu && $day_menu['day_active']) {
            if (!empty($day_menu['special_note'])) {
                $day_content = '<div class="menu-special-note">' . esc_html($day_menu['special_note']) . '</div>';
            } else if (!empty($day_menu['menu_content'])) {
                $day_content = '<div class="menu-day-content">' . wp_kses_post($day_menu['menu_content']) . '</div>';
            }
        } else {
            $day_content = '<div class="menu-not-available"><p>No hay menú disponible para ' . ucfirst($day_name) . '</p></div>';
        }
    }
    
    // Obtener información de alérgenos para este menú
    $allergens_html = '';
    if (function_exists('lamusa_get_menu_allergens_summary')) {
        $allergen_summary = lamusa_get_menu_allergens_summary($menu->ID);
        
        if (!empty($allergen_summary['contains']) || !empty($allergen_summary['traces'])) {
            $allergens_html = '<div class="menu-allergens-section">';
            $allergens_html .= '<div class="allergens-badge">ALÉRGENOS</div>';
            $allergens_html .= '<div class="allergens-note">';
            
            if (!empty($allergen_summary['contains'])) {
                $allergens_html .= '<p>Contiene alérgenos ●</p>';
            }
            
            if (!empty($allergen_summary['traces'])) {
                $allergens_html .= '<p>Puede contener trazas T</p>';
            }
            
            $allergens_html .= '</div>';
            $allergens_html .= '</div>';
        }
    }
    
    $full_content = $day_content . $allergens_html;
    
    wp_send_json_success($full_content);
}

/**
 * Helper: Obtener datos estructurados de los días del menú
 */
function lamusa_get_menu_days_data($menu) {
    $days_data = array();
    
    // Días de la semana en orden
    $week_days = array(
        'lunes' => 'LUNES',
        'martes' => 'MARTES', 
        'miercoles' => 'MIÉRCOLES',
        'jueves' => 'JUEVES',
        'viernes' => 'VIERNES',
        'sabado' => 'SÁBADO',
        'domingo' => 'DOMINGO'
    );
    
    if ($menu) {
        $menu_days = get_field('menu_days', $menu->ID);
        
        // Crear array indexado por nombre de día
        $days_by_name = array();
        if ($menu_days) {
            foreach ($menu_days as $day) {
                $days_by_name[$day['day_name']] = $day;
            }
        }
        
        // Generar datos para todos los días de la semana
        foreach ($week_days as $day_key => $day_display) {
            $day_info = $days_by_name[$day_key] ?? null;
            
            $days_data[] = array(
                'name' => $day_key,
                'display_name' => $day_display,
                'active' => $day_info ? $day_info['day_active'] : false,
                'date' => $day_info && !empty($day_info['day_date']) ? 
                    date('d/m', strtotime($day_info['day_date'])) : null,
                'has_content' => $day_info && !empty($day_info['menu_content']),
                'special_note' => $day_info ? $day_info['special_note'] : null
            );
        }
    } else {
        // Si no hay menú, todos los días inactivos
        foreach ($week_days as $day_key => $day_display) {
            $days_data[] = array(
                'name' => $day_key,
                'display_name' => $day_display,
                'active' => false,
                'date' => null,
                'has_content' => false,
                'special_note' => null
            );
        }
    }
    
    return $days_data;
}

/**
 * Localizar scripts para AJAX
 */
add_action('wp_enqueue_scripts', 'lamusa_localize_navigator_scripts');
function lamusa_localize_navigator_scripts() {
    wp_localize_script('lamusa-menu-navigator', 'lamusaNavigator', array(
        'ajaxurl' => admin_url('admin-ajax.php'),
        'nonce' => wp_create_nonce('lamusa_navigator_nonce'),
        'strings' => array(
            'loading' => __('Cargando...', 'lamusa-core'),
            'error' => __('Error al cargar los datos', 'lamusa-core'),
            'no_menu' => __('No hay menú disponible', 'lamusa-core'),
            'no_content' => __('Sin contenido para este día', 'lamusa-core')
        )
    ));
}
