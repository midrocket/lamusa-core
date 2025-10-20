<?php
/**
 * Shortcodes para La Musa Core
 */

// Prevenir acceso directo
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Registrar todos los shortcodes
 */
function lamusa_register_shortcodes() {
    add_shortcode('lamusa_menu_navigator', 'lamusa_menu_navigator_shortcode');
    add_shortcode('lamusa_restaurant_menu', 'lamusa_restaurant_menu_shortcode');
    add_shortcode('lamusa_restaurant_allergens', 'lamusa_restaurant_allergens_shortcode');
}
add_action('init', 'lamusa_register_shortcodes');

/**
 * Añadir clases al body según la vista
 */
add_action('wp_head', 'lamusa_add_body_classes');
function lamusa_add_body_classes() {
    $url_params = lamusa_parse_url_params();
    
    if (!empty($url_params['restaurant'])) {
        $body_class = '';
        
        if ($url_params['view'] === 'allergens') {
            $body_class = 'menu-view-allergens';
        } else {
            $body_class = 'menu-view-days';
        }
        
        if ($body_class) {
            ?>
            <script>
            document.addEventListener('DOMContentLoaded', function() {
                document.body.classList.add('<?php echo esc_js($body_class); ?>');
            });
            </script>
            <?php
        }
    }
}

/**
 * Shortcode principal del navegador de menús
 * [lamusa_menu_navigator]
 */
function lamusa_menu_navigator_shortcode($atts) {
    $atts = shortcode_atts(array(
        'restaurant' => '',
        'view' => 'menu', // 'menu' o 'allergens'
        'day' => '',
        'show_navigation' => 'true',
        'show_all_days' => 'false',
        'style' => 'default'
    ), $atts, 'lamusa_menu_navigator');
    
    // Detectar parámetros de URL
    $url_params = lamusa_parse_url_params();
    
    // Los parámetros de URL tienen prioridad sobre los atributos del shortcode
    $restaurant_slug = $url_params['restaurant'] ?: $atts['restaurant'];
    $view_type = $url_params['view'] ?: $atts['view'];
    $day_name = $url_params['day'] ?: $atts['day'];
    
    // Obtener ID del restaurante por slug
    $restaurant_id = null;
    if ($restaurant_slug) {
        $restaurant_id = lamusa_get_restaurant_by_slug($restaurant_slug);
    }
    
    // Si no hay restaurante específico, usar el primero disponible
    if (!$restaurant_id) {
        $restaurants = get_posts(array(
            'post_type' => 'restaurant',
            'post_status' => 'publish',
            'posts_per_page' => 1,
            'orderby' => 'title',
            'order' => 'ASC'
        ));
        
        if (!empty($restaurants)) {
            $restaurant_id = $restaurants[0]->ID;
        }
    }
    
    if (!$restaurant_id) {
        return '<div class="lamusa-error">No hay restaurantes disponibles.</div>';
    }
    
    // Generar contenido según el tipo de vista
    ob_start();
    
    echo '<div class="lamusa-shortcode-container" data-restaurant="' . esc_attr($restaurant_id) . '" data-view="' . esc_attr($view_type) . '">';
    
    if ($view_type === 'allergens') {
        lamusa_render_allergens_view($restaurant_id, $atts);
    } else {
        lamusa_render_menu_view($restaurant_id, $day_name, $atts);
    }
    
    echo '</div>';
    
    // Añadir botones de navegación del restaurante
    lamusa_render_restaurant_action_buttons($restaurant_id);
    
    // Añadir navegación si está habilitada
    if ($atts['show_navigation'] === 'true') {
        lamusa_render_restaurant_navigation($restaurant_id, $view_type);
    }
    
    return ob_get_clean();
}

/**
 * Shortcode específico para menú de restaurante
 * [lamusa_restaurant_menu restaurant="limbo-tribunal" day="lunes"]
 */
function lamusa_restaurant_menu_shortcode($atts) {
    $atts = shortcode_atts(array(
        'restaurant' => '',
        'day' => '',
        'show_header' => 'true',
        'show_navigation' => 'true'
    ), $atts, 'lamusa_restaurant_menu');
    
    $atts['view'] = 'menu';
    return lamusa_menu_navigator_shortcode($atts);
}

/**
 * Shortcode específico para alérgenos de restaurante
 * [lamusa_restaurant_allergens restaurant="limbo-tribunal"]
 */
function lamusa_restaurant_allergens_shortcode($atts) {
    $atts = shortcode_atts(array(
        'restaurant' => '',
        'show_header' => 'true',
        'show_navigation' => 'true'
    ), $atts, 'lamusa_restaurant_allergens');
    
    $atts['view'] = 'allergens';
    return lamusa_menu_navigator_shortcode($atts);
}

/**
 * Parsear parámetros de URL
 */
function lamusa_parse_url_params() {
    $params = array(
        'restaurant' => get_query_var('lamusa_restaurant', ''),
        'view' => get_query_var('lamusa_view', 'menu'),
        'day' => get_query_var('lamusa_day', '')
    );
    
    // Fallback al método manual si no hay query vars
    if (empty($params['restaurant'])) {
        global $wp;
        $request_parts = explode('/', trim($wp->request, '/'));
        
        $page_slug = get_query_var('pagename') ?: get_query_var('name');
        
        if ($page_slug) {
            $page_index = array_search($page_slug, $request_parts);
            
            if ($page_index !== false) {
                if (isset($request_parts[$page_index + 1])) {
                    $params['restaurant'] = $request_parts[$page_index + 1];
                }
                
                if (isset($request_parts[$page_index + 2])) {
                    $third_param = $request_parts[$page_index + 2];
                    if ($third_param === 'alergenos') {
                        $params['view'] = 'allergens';
                    } else {
                        $params['day'] = $third_param;
                    }
                }
            }
        }
    }
    
    return $params;
}

/**
 * Obtener ID de restaurante por slug
 */
function lamusa_get_restaurant_by_slug($slug) {
    // Primero intentar por slug del post
    $restaurant = get_page_by_path($slug, OBJECT, 'restaurant');
    
    if ($restaurant) {
        return $restaurant->ID;
    }
    
    // Si no se encuentra, buscar por título del restaurante
    $restaurants = get_posts(array(
        'post_type' => 'restaurant',
        'post_status' => 'publish',
        'posts_per_page' => -1
    ));
    
    foreach ($restaurants as $restaurant) {
        $restaurant_title = get_field('restaurant_title', $restaurant->ID);
        $restaurant_title_secondary = get_field('restaurant_title_secondary', $restaurant->ID);
        
        // Crear posibles slugs
        $possible_slugs = array(
            sanitize_title($restaurant->post_title),
            sanitize_title($restaurant_title),
            sanitize_title($restaurant_title . '-' . $restaurant_title_secondary),
            sanitize_title($restaurant_title . ' ' . $restaurant_title_secondary)
        );
        
        if (in_array($slug, $possible_slugs)) {
            return $restaurant->ID;
        }
    }
    
    return null;
}

/**
 * Renderizar vista de menú con estructura de 3 secciones
 */
function lamusa_render_menu_view($restaurant_id, $day_name = '', $atts = array()) {
    // SECCIÓN 1: Barra superior con todos los restaurantes
    lamusa_render_restaurants_bar($restaurant_id);
    
    // Obtener menú activo del restaurante seleccionado
    $menu = lamusa_get_restaurant_active_menu($restaurant_id);
    
    if (!$menu) {
        // Mostrar mensaje de no hay menú
        echo '<div class="lamusa-no-menu">No hay menú semanal disponible para este restaurante.</div>';
        
        // Renderizar barra de restaurantes en mobile aunque no haya menú
        echo '<div class="lamusa-mobile-layout">';
        lamusa_render_mobile_restaurants_bottom($restaurant_id);
        echo '</div>';
        
        return;
    }
    
    // Determinar día seleccionado con lógica automática
    $selected_day = lamusa_determine_selected_day($menu, $day_name);
    
    // SECCIÓN 2 & 3: Renderizar según dispositivo
    // Desktop: Submenú horizontal + contenido separado
    // Mobile: Días verticales con contenido inline
    lamusa_render_menu_sections($restaurant_id, $menu, $selected_day, $atts);
}

/**
 * Renderizar vista de alérgenos
 */
function lamusa_render_allergens_view($restaurant_id, $atts = array()) {
    $restaurant = get_post($restaurant_id);
    if (!$restaurant) {
        echo '<div class="lamusa-error">Restaurante no encontrado.</div>';
        return;
    }
    
    // SECCIÓN 1: Barra superior con todos los restaurantes (siempre se muestra)
    lamusa_render_restaurants_bar($restaurant_id);
    
    // Obtener menú activo usando la misma función que la vista de días
    $menu = lamusa_get_restaurant_active_menu($restaurant_id);
    
    if (!$menu) {
        // Mostrar mensaje de no hay menú
        echo '<div class="lamusa-no-menu">No hay información de alérgenos disponible para este restaurante.</div>';
        
        // Renderizar barra de restaurantes en mobile aunque no haya menú
        echo '<div class="lamusa-mobile-layout">';
        lamusa_render_mobile_restaurants_bottom($restaurant_id);
        echo '</div>';
        
        return;
    }
    
    // SECCIÓN 2: Submenú de días (igual que en menú pero ninguno activo)
    lamusa_render_days_submenu_for_allergens($menu, $atts);
    
    // SECCIÓN 3: Cabecera específica para alérgenos + tabla
    lamusa_render_allergens_header($restaurant);
    lamusa_render_allergens_table($menu);
    
    // Barra de restaurantes en mobile
    echo '<div class="lamusa-mobile-layout">';
    lamusa_render_mobile_restaurants_bottom($restaurant_id);
    echo '</div>';
}

/**
 * Renderizar secciones de menú (desktop y mobile)
 */
function lamusa_render_menu_sections($restaurant_id, $menu, $selected_day, $atts) {
    $restaurant = get_post($restaurant_id);
    
    // DESKTOP: Layout horizontal tradicional
    echo '<div class="lamusa-desktop-layout">';
    
    // Submenú de días horizontal
    lamusa_render_days_submenu($menu, $selected_day, $atts);
    
    // Contenido del menú separado
    lamusa_render_menu_content_section($restaurant_id, $menu, $selected_day);
    
    echo '</div>'; // lamusa-desktop-layout
    
    // MOBILE: Layout vertical con contenido inline
    echo '<div class="lamusa-mobile-layout">';
    
    // Días verticales con contenido inline
    lamusa_render_mobile_days_with_content($restaurant_id, $menu, $selected_day, $atts);
    
    // Barra de restaurantes adicional abajo
    lamusa_render_mobile_restaurants_bottom($restaurant_id);
    
    echo '</div>'; // lamusa-mobile-layout
}

/**
 * SECCIÓN 1: Renderizar barra superior con todos los restaurantes
 */
function lamusa_render_restaurants_bar($current_restaurant_id) {
    $restaurants = get_posts(array(
        'post_type' => 'restaurant',
        'post_status' => 'publish',
        'posts_per_page' => -1,
        'orderby' => 'title',
        'order' => 'ASC'
    ));
    
    if (empty($restaurants)) {
        return;
    }
    
    echo '<div class="lamusa-restaurants-bar">';
    
    foreach ($restaurants as $restaurant) {
        $restaurant_title = get_field('restaurant_title', $restaurant->ID) ?: $restaurant->post_title;
        $restaurant_title_secondary = get_field('restaurant_title_secondary', $restaurant->ID);
        
        // Crear display name
        $display_name = '<span class="restaurant-title-primary">'.strtoupper($restaurant_title).'</span>';
        if ($restaurant_title_secondary) {
            $display_name .= ' <span class="restaurant-title-secondary">' . $restaurant_title_secondary . '</span>';
        }
        
        // Determinar si está activo
        $is_active = ($restaurant->ID == $current_restaurant_id);
        $item_class = 'restaurant-bar-item' . ($is_active ? ' active' : '');
        
        // Generar URL del restaurante
        $restaurant_slug = lamusa_get_restaurant_slug($restaurant);
        $restaurant_url = lamusa_build_menu_url($restaurant_slug);
        
        echo '<a href="' . esc_url($restaurant_url) . '" class="' . esc_attr($item_class) . '">';
        echo $display_name; // No escapar aquí porque incluye HTML para italic
        echo '</a>';
    }
    
    echo '</div>';
}

/**
 * SECCIÓN 2: Renderizar submenú de días disponibles
 */
function lamusa_render_days_submenu($menu, $selected_day, $atts = array()) {
    $show_all_days = ($atts['show_all_days'] ?? 'false') === 'true';
    
    $menu_days = get_field('menu_days', $menu->ID);
    if (!$menu_days) {
        return;
    }
    
    // Días de la semana
    $week_days = array(
        'lunes' => 'LUNES',
        'martes' => 'MARTES', 
        'miercoles' => 'MIÉRCOLES',
        'jueves' => 'JUEVES',
        'viernes' => 'VIERNES',
        'sabado' => 'SÁBADO',
        'domingo' => 'DOMINGO'
    );
    
    // Organizar días por nombre
    $days_by_name = array();
    foreach ($menu_days as $day) {
        $days_by_name[$day['day_name']] = $day;
    }
    
    echo '<div class="lamusa-days-submenu">';
    
    foreach ($week_days as $day_key => $day_display) {
        $day_info = $days_by_name[$day_key] ?? null;
        $is_active = $day_info; // Si existe el día, está activo
        
        // Si no se muestran todos los días y este día no está activo, saltar
        if (!$show_all_days && !$is_active) {
            continue;
        }
        
        $day_class = 'day-submenu-item';
        $day_class .= $is_active ? ' has-menu' : ' no-menu';
        $day_class .= ($day_key === $selected_day) ? ' active' : '';
        
        // Generar URL del día
        $url_params = lamusa_parse_url_params();
        $restaurant_slug = $url_params['restaurant'];
        $day_url = lamusa_build_menu_url($restaurant_slug, $day_key);
        
        echo '<a href="' . esc_url($day_url) . '" class="' . esc_attr($day_class) . '" data-day="' . esc_attr($day_key) . '">';
        echo '<span class="day-name">' . esc_html($day_display) . '</span>';
        echo '</a>';
    }
    
    echo '</div>';
}

/**
 * SECCIÓN 3: Renderizar contenido del menú con cabecera del restaurante
 */
function lamusa_render_menu_content_section($restaurant_id, $menu, $selected_day) {
    $restaurant = get_post($restaurant_id);
    
    echo '<div class="lamusa-menu-content-section">';
    
    // Cabecera del restaurante y menú
    lamusa_render_restaurant_menu_header($restaurant, $menu);
    
    // Contenido del día seleccionado
    lamusa_render_selected_day_content($menu, $selected_day);
    
    echo '</div>';
}

/**
 * Obtener menú activo de un restaurante
 */
function lamusa_get_restaurant_active_menu($restaurant_id) {
    $today = current_time('Y-m-d');
    
    // Primero intentar obtener menú activo dentro del rango de fechas
    $menu_posts = get_posts(array(
        'post_type' => 'weekly_menu',
        'post_status' => 'publish',
        'posts_per_page' => -1,
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
                'key' => 'start_date',
                'value' => $today,
                'compare' => '<='
            ),
            array(
                'key' => 'end_date',
                'value' => $today,
                'compare' => '>='
            )
        )
    ));
    
    // Filtrar solo los que tienen menu_active = 1
    if (!empty($menu_posts)) {
        foreach ($menu_posts as $menu) {
            $is_active = get_post_meta($menu->ID, 'menu_active', true);
            // Verificar si es 1, "1", true o cualquier valor truthy
            if ($is_active == 1 || $is_active === '1' || $is_active === true) {
                return $menu;
            }
        }
    }
    
    // Fallback: buscar el menú más reciente activo sin filtro de fecha
    $all_menus = get_posts(array(
        'post_type' => 'weekly_menu',
        'post_status' => 'publish',
        'posts_per_page' => -1,
        'orderby' => 'date',
        'order' => 'DESC',
        'meta_query' => array(
            array(
                'key' => 'restaurant',
                'value' => $restaurant_id,
                'compare' => '='
            )
        )
    ));
    
    if (!empty($all_menus)) {
        foreach ($all_menus as $menu) {
            $is_active = get_post_meta($menu->ID, 'menu_active', true);
            if ($is_active == 1 || $is_active === '1' || $is_active === true) {
                return $menu;
            }
        }
    }
    
    return null;
}

/**
 * Determinar día seleccionado con lógica automática
 */
function lamusa_determine_selected_day($menu, $requested_day = '') {
    $menu_days = get_field('menu_days', $menu->ID);
    if (!$menu_days) {
        return 'lunes'; // Fallback
    }
    
    // Organizar días por nombre
    $days_by_name = array();
    $active_days = array();
    
    foreach ($menu_days as $day) {
        $days_by_name[$day['day_name']] = $day;
        // Si existe el día, se considera activo (ya no hay campo day_active)
        $active_days[] = $day['day_name'];
    }
    
    // Si se especifica un día y existe, usarlo
    if ($requested_day && isset($days_by_name[$requested_day])) {
        return $requested_day;
    }
    
    // Si no, intentar usar el día actual
    $today = strtolower(date_i18n('l'));
    $day_translations = array(
        'monday' => 'lunes',
        'tuesday' => 'martes',
        'wednesday' => 'miercoles',
        'thursday' => 'jueves',
        'friday' => 'viernes',
        'saturday' => 'sabado',
        'sunday' => 'domingo'
    );
    $today_spanish = $day_translations[$today] ?? 'lunes';
    
    // Si hoy está disponible, usarlo
    if (in_array($today_spanish, $active_days)) {
        return $today_spanish;
    }
    
    // Si no, usar el primer día activo
    return !empty($active_days) ? $active_days[0] : 'lunes';
}

/**
 * Obtener slug del restaurante
 */
function lamusa_get_restaurant_slug($restaurant) {
    $restaurant_title = get_field('restaurant_title', $restaurant->ID) ?: $restaurant->post_title;
    $restaurant_title_secondary = get_field('restaurant_title_secondary', $restaurant->ID);
    
    $slug = sanitize_title($restaurant_title);
    if ($restaurant_title_secondary) {
        $slug .= '-' . sanitize_title($restaurant_title_secondary);
    }
    
    return $slug;
}

/**
 * Renderizar cabecera del restaurante y menú (dentro de la sección 3)
 */
function lamusa_render_restaurant_menu_header($restaurant, $menu) {
    $restaurant_title = get_field('restaurant_title', $restaurant->ID) ?: $restaurant->post_title;
    $restaurant_title_secondary = get_field('restaurant_title_secondary', $restaurant->ID);
    $restaurant_subtitle = get_field('restaurant_subtitle', $restaurant->ID);
    $restaurant_subtitle_secondary = get_field('restaurant_subtitle_secondary', $restaurant->ID);
    
    $menu_price = get_field('menu_price', $menu->ID);
    $start_date = get_field('start_date', $menu->ID);
    $end_date = get_field('end_date', $menu->ID);
    $menu_introduction = get_field('menu_introduction', $menu->ID);
    
    echo '<div class="lamusa-content-header">';
    echo '<div class="content-header-layout">';
    
    // Lado izquierdo: Títulos del restaurante
    echo '<div class="content-header-left">';
    echo '<div class="content-restaurant-title">';
    echo '<span class="content-restaurant-name">' . esc_html($restaurant_title) . '</span>';
    if ($restaurant_title_secondary) {
        echo '<span class="content-restaurant-name-secondary">' . esc_html($restaurant_title_secondary) . '</span>';
    }
    echo '</div>';
    
    // if ($restaurant_subtitle) {
    //     echo '<div class="content-restaurant-subtitle">' . esc_html($restaurant_subtitle) . '</div>';
    // }
    // if ($restaurant_subtitle_secondary) {
    //     echo '<div class="content-restaurant-subtitle-secondary">' . esc_html($restaurant_subtitle_secondary) . '</div>';
    // }
    echo '</div>';
    
    // Lado derecho: Precio
    if ($menu_price) {
        echo '<div class="content-header-right">';
        echo '<div class="content-menu-price">' . esc_html($menu_price) . '</div>';
        echo '</div>';
    }
    
    echo '</div>'; // content-header-layout
    
    // Fila inferior: Fechas y descripción
    echo '<div class="content-header-bottom">';
    
    // Fechas
    if ($start_date && $end_date) {
        echo '<div class="content-menu-dates">';
        echo '<span class="date-range">' . date('d/m', strtotime($start_date)) . '</span>';
        // echo '<span class="date-separator"></span>';
        echo '<span class="date-range">' . date('d/m', strtotime($end_date)) . '</span>';
        echo '</div>';
    }
    
    // Descripción
    if ($menu_introduction) {
        echo '<div class="content-menu-description">' . wp_kses_post($menu_introduction) . '</div>';
    }
    
    // Botón de alérgenos - usar slug del restaurante actual
    $restaurant_slug = lamusa_get_restaurant_slug($restaurant);
    $allergens_url = lamusa_build_menu_url($restaurant_slug, '', 'allergens');
    
    echo '<div class="content-allergens-button">';
    echo '<a href="' . esc_url($allergens_url) . '" class="allergens-btn">ALÉRGENOS</a>';
    echo '</div>';
    
    echo '</div>'; // content-header-bottom
    echo '</div>'; // lamusa-content-header
}

/**
 * Renderizar contenido del día seleccionado
 */
function lamusa_render_selected_day_content($menu, $selected_day) {
    $menu_days = get_field('menu_days', $menu->ID);
    $day_content = null;
    
    if ($menu_days) {
        foreach ($menu_days as $day) {
            if ($day['day_name'] === $selected_day) {
                $day_content = $day;
                break;
            }
        }
    }
    
    echo '<div class="lamusa-day-menu-content" data-day="' . esc_attr($selected_day) . '">';
    
    if ($day_content) {
        if (!empty($day_content['special_note'])) {
            echo '<div class="menu-special-note">' . esc_html($day_content['special_note']) . '</div>';
        } else if (!empty($day_content['menu_content'])) {
            echo '<div class="menu-day-content">' . wp_kses_post($day_content['menu_content']) . '</div>';
        }
    } else {
        echo '<div class="menu-not-available">';
        echo '<h3>No hay menú disponible</h3>';
        echo '<p>No tenemos menú para ' . ucfirst($selected_day) . '.</p>';
        echo '</div>';
    }
    
    echo '</div>';
}

/**
 * Generar URL base para menús
 */
function lamusa_build_menu_url($restaurant_slug, $day = '', $view = 'menu') {
    global $wp;
    $current_page = get_query_var('pagename') ?: get_query_var('name');
    
    if (!$current_page) {
        // Fallback: usar la página actual
        $current_page = get_post_field('post_name', get_the_ID());
    }
    
    $url = home_url('/' . $current_page . '/' . $restaurant_slug);
    
    if ($view === 'allergens') {
        $url .= '/alergenos';
    } elseif ($day) {
        $url .= '/' . $day;
    }
    
    return $url;
}

/**
 * Generar URL para día específico
 */
function lamusa_get_day_url($day_name) {
    $url_params = lamusa_parse_url_params();
    $restaurant_slug = $url_params['restaurant'];
    
    if (!$restaurant_slug) {
        return '#';
    }
    
    return lamusa_build_menu_url($restaurant_slug, $day_name);
}

/**
 * Generar URL para alérgenos
 */
function lamusa_get_allergens_url() {
    $url_params = lamusa_parse_url_params();
    $restaurant_slug = $url_params['restaurant'];
    
    if (!$restaurant_slug) {
        return '#';
    }
    
    return lamusa_build_menu_url($restaurant_slug, '', 'allergens');
}

/**
 * Generar URL para restaurante
 */
function lamusa_get_restaurant_url($restaurant, $view = 'menu') {
    $restaurant_slug = lamusa_get_restaurant_slug($restaurant);
    return lamusa_build_menu_url($restaurant_slug, '', $view);
}

/**
 * SECCIÓN 2: Renderizar submenú de días para página de alérgenos (ninguno activo)
 */
function lamusa_render_days_submenu_for_allergens($menu, $atts = array()) {
    $menu_days = get_field('menu_days', $menu->ID);
    if (!$menu_days) {
        return;
    }
    
    // Días de la semana
    $week_days = array(
        'lunes' => 'LUNES',
        'martes' => 'MARTES', 
        'miercoles' => 'MIÉRCOLES',
        'jueves' => 'JUEVES',
        'viernes' => 'VIERNES',
        'sabado' => 'SÁBADO',
        'domingo' => 'DOMINGO'
    );
    
    // Organizar días por nombre
    $days_by_name = array();
    foreach ($menu_days as $day) {
        $days_by_name[$day['day_name']] = $day;
    }
    
    echo '<div class="lamusa-days-submenu allergens-page">';
    
    foreach ($week_days as $day_key => $day_display) {
        $day_info = $days_by_name[$day_key] ?? null;
        $is_available = $day_info; // Si existe el día, está disponible
        
        // Solo mostrar días que tienen menú
        if (!$is_available) {
            continue;
        }
        
        // Para alérgenos, ningún día está "activo" pero todos son clickeables para volver al menú
        $day_class = 'day-submenu-item has-menu';
        
        // Generar URL del día (volver al menú)
        $url_params = lamusa_parse_url_params();
        $restaurant_slug = $url_params['restaurant'];
        $day_url = lamusa_build_menu_url($restaurant_slug, $day_key);
        
        echo '<a href="' . esc_url($day_url) . '" class="' . esc_attr($day_class) . '" data-day="' . esc_attr($day_key) . '">';
        echo '<span class="day-name">' . esc_html($day_display) . '</span>';
        echo '</a>';
    }
    
    echo '</div>';
}

/**
 * Renderizar días verticales con contenido inline para mobile
 */
function lamusa_render_mobile_days_with_content($restaurant_id, $menu, $selected_day, $atts) {
    $show_all_days = ($atts['show_all_days'] ?? 'false') === 'true';
    $restaurant = get_post($restaurant_id);
    
    $menu_days = get_field('menu_days', $menu->ID);
    if (!$menu_days) {
        return;
    }
    
    // Días de la semana
    $week_days = array(
        'lunes' => 'LUNES',
        'martes' => 'MARTES', 
        'miercoles' => 'MIÉRCOLES',
        'jueves' => 'JUEVES',
        'viernes' => 'VIERNES',
        'sabado' => 'SÁBADO',
        'domingo' => 'DOMINGO'
    );
    
    // Organizar días por nombre
    $days_by_name = array();
    foreach ($menu_days as $day) {
        $days_by_name[$day['day_name']] = $day;
    }
    
    echo '<div class="lamusa-mobile-days-container">';
    
    foreach ($week_days as $day_key => $day_display) {
        $day_info = $days_by_name[$day_key] ?? null;
        $is_active = $day_info; // Si existe el día, está activo
        
        // Si no se muestran todos los días y este día no está activo, saltar
        if (!$show_all_days && !$is_active) {
            continue;
        }
        
        $day_class = 'mobile-day-item';
        $day_class .= $is_active ? ' has-menu' : ' no-menu';
        $day_class .= ($day_key === $selected_day) ? ' active' : '';
        
        // Generar URL del día
        $url_params = lamusa_parse_url_params();
        $restaurant_slug = $url_params['restaurant'];
        $day_url = lamusa_build_menu_url($restaurant_slug, $day_key);
        
        echo '<div class="' . esc_attr($day_class) . '" data-day="' . esc_attr($day_key) . '">';
        
        // Cabecera del día (siempre visible)
        echo '<a href="' . esc_url($day_url) . '" class="mobile-day-header" data-day-toggle="' . esc_attr($day_key) . '">';
        echo '<span class="mobile-day-name">' . esc_html($day_display) . '</span>';
        // echo '<span class="mobile-day-arrow">▼</span>';
        echo '</a>';
        
        // Contenido del día (renderizar siempre, mostrar/ocultar con CSS)
        if ($is_active) {
            $content_class = 'mobile-day-content';
            $content_class .= ($day_key === $selected_day) ? ' active' : '';
            
            echo '<div class="' . esc_attr($content_class) . '" data-day-content="' . esc_attr($day_key) . '">';
            
            // Cabecera del restaurante
            lamusa_render_mobile_restaurant_header($restaurant, $menu);
            
            // Contenido del día específico
            lamusa_render_mobile_day_content($menu, $day_key);
            
            echo '</div>'; // mobile-day-content
        }
        
        echo '</div>'; // mobile-day-item
    }
    
    echo '</div>'; // lamusa-mobile-days-container
}

/**
 * Renderizar cabecera del restaurante para mobile
 */
function lamusa_render_mobile_restaurant_header($restaurant, $menu) {
    $restaurant_title = get_field('restaurant_title', $restaurant->ID) ?: $restaurant->post_title;
    $restaurant_title_secondary = get_field('restaurant_title_secondary', $restaurant->ID);
    
    $menu_price = get_field('menu_price', $menu->ID);
    $start_date = get_field('start_date', $menu->ID);
    $end_date = get_field('end_date', $menu->ID);
    $menu_introduction = get_field('menu_introduction', $menu->ID);
    
    echo '<div class="mobile-restaurant-header">';
    
    // Precio y fechas en la parte superior
    echo '<div class="mobile-menu-info-top">';
    if ($menu_price) {
        echo '<span class="mobile-menu-price">' . esc_html($menu_price) . '</span>';
    }
    
    // Botón de alérgenos
    $restaurant_slug = lamusa_get_restaurant_slug($restaurant);
    $allergens_url = lamusa_build_menu_url($restaurant_slug, '', 'allergens');
    echo '<a href="' . esc_url($allergens_url) . '" class="mobile-allergens-btn">ALÉRGENOS</a>';
    echo '</div>';
    
    // Fechas y descripción
    echo '<div class="mobile-menu-info-bottom">';
    if ($start_date && $end_date) {
        echo '<div class="mobile-menu-dates-section">';
        echo '<span class="mobile-date-range">' . date('d/m', strtotime($start_date)) . '</span>';
        // echo '<span class="mobile-date-separator"></span>';
        echo '<span class="mobile-date-range">' . date('d/m', strtotime($end_date)) . '</span>';
        echo '</div>';
    }
    
    if ($menu_introduction) {
        echo '<div class="mobile-menu-description">' . wp_kses_post($menu_introduction) . '</div>';
    }
    echo '</div>';
    
    echo '</div>'; // mobile-restaurant-header
}

/**
 * Renderizar contenido del día para mobile
 */
function lamusa_render_mobile_day_content($menu, $selected_day) {
    $menu_days = get_field('menu_days', $menu->ID);
    $day_content = null;
    
    if ($menu_days) {
        foreach ($menu_days as $day) {
            if ($day['day_name'] === $selected_day) {
                $day_content = $day;
                break;
            }
        }
    }
    
    echo '<div class="mobile-menu-content">';
    
    if ($day_content) {
        if (!empty($day_content['special_note'])) {
            echo '<div class="mobile-special-note">' . esc_html($day_content['special_note']) . '</div>';
        } else if (!empty($day_content['menu_content'])) {
            echo '<div class="mobile-day-menu">' . wp_kses_post($day_content['menu_content']) . '</div>';
        }
    } else {
        echo '<div class="mobile-no-menu">No hay menú disponible para este día.</div>';
    }
    
    echo '</div>'; // mobile-menu-content
}

/**
 * Renderizar barra de restaurantes adicional para mobile (abajo)
 */
function lamusa_render_mobile_restaurants_bottom($current_restaurant_id) {
    // Asegurar que sea un ID numérico
    $current_restaurant_id = is_object($current_restaurant_id) ? $current_restaurant_id->ID : intval($current_restaurant_id);
    
    $restaurants = get_posts(array(
        'post_type' => 'restaurant',
        'post_status' => 'publish',
        'posts_per_page' => -1,
        'orderby' => 'title',
        'order' => 'ASC'
    ));
    
    if (empty($restaurants) || count($restaurants) <= 1) {
        return; // No mostrar si solo hay un restaurante
    }
    
    echo '<div class="lamusa-mobile-restaurants-bottom">';
    echo '<div class="mobile-restaurants-grid">';
    
    foreach ($restaurants as $restaurant) {
        if ($restaurant->ID == $current_restaurant_id) {
            continue; // Saltar el restaurante actual
        }
        
        $restaurant_title = get_field('restaurant_title', $restaurant->ID) ?: $restaurant->post_title;
        $restaurant_title_secondary = get_field('restaurant_title_secondary', $restaurant->ID);
        
        $display_name = $restaurant_title;
        if ($restaurant_title_secondary) {
            $display_name .= ' ' . $restaurant_title_secondary;
        }
        
        $restaurant_slug = lamusa_get_restaurant_slug($restaurant);
        $restaurant_url = lamusa_build_menu_url($restaurant_slug);
        
        echo '<a href="' . esc_url($restaurant_url) . '" class="mobile-restaurant-item">';
        echo '<span class="mobile-restaurant-name">' . esc_html($restaurant_title) . '</span>';
        if ($restaurant_title_secondary) {
            echo '<span class="mobile-restaurant-secondary">' . esc_html($restaurant_title_secondary) . '</span>';
        }
        echo '</a>';
    }
    
    echo '</div>'; // mobile-restaurants-grid
    echo '</div>'; // lamusa-mobile-restaurants-bottom
}

/**
 * Renderizar botones de acción del restaurante (Local, Carta, Take Away)
 */
function lamusa_render_restaurant_action_buttons($restaurant_id) {
    $restaurant = get_post($restaurant_id);
    if (!$restaurant) {
        return;
    }
    
    $restaurant_urls = get_field('restaurant_urls', $restaurant_id);
    if (!$restaurant_urls) {
        return;
    }
    
    // Obtener URLs
    $local_url = $restaurant_urls['url_local'] ?? '';
    $menu_url = $restaurant_urls['url_menu'] ?? '';
    $takeaway_url = $restaurant_urls['url_take_away'] ?? '';
    
    // Solo mostrar si al menos uno de los enlaces existe
    if (!$local_url && !$menu_url && !$takeaway_url) {
        return;
    }
    
    echo '<div class="lamusa-restaurant-action-buttons">';
    echo '<div class="action-buttons-container">';
    
    // Botón LOCAL
    if ($local_url) {
        echo '<a href="' . esc_url($local_url) . '" class="action-button action-button-local" target="_blank">';
        echo '<span class="action-button-text">LOCAL</span>';
        echo '</a>';
    }
    
    // Botón CARTA
    if ($menu_url) {
        echo '<a href="' . esc_url($menu_url) . '" class="action-button action-button-carta" target="_blank">';
        echo '<span class="action-button-text">CARTA</span>';
        echo '</a>';
    }
    
    // Botón TAKE AWAY
    if ($takeaway_url) {
        echo '<a href="' . esc_url($takeaway_url) . '" class="action-button action-button-takeaway" target="_blank">';
        echo '<span class="action-button-text">TAKE AWAY</span>';
        echo '</a>';
    }
    
    echo '</div>'; // action-buttons-container
    echo '</div>'; // lamusa-restaurant-action-buttons
}

/**
 * Renderizar cabecera específica para página de alérgenos
 */
function lamusa_render_allergens_header($restaurant) {
    $restaurant_title = get_field('restaurant_title', $restaurant->ID) ?: $restaurant->post_title;
    $restaurant_title_secondary = get_field('restaurant_title_secondary', $restaurant->ID);
    
    // echo '<div class="lamusa-allergens-header">';
    // echo '<div class="allergens-header-content">';
    
    // // Título del restaurante
    // echo '<div class="allergens-restaurant-title">';
    // echo '<span class="allergens-restaurant-name">' . esc_html($restaurant_title) . '</span>';
    // if ($restaurant_title_secondary) {
    //     echo ' <span class="allergens-restaurant-name-secondary">' . esc_html($restaurant_title_secondary) . '</span>';
    // }
    // echo '</div>';
    
    // // Título de la página de alérgenos
    // echo '<div class="allergens-page-title">ALÉRGENOS</div>';
    
    // echo '</div>';
    // echo '</div>';
}

/**
 * Renderizar resumen de alérgenos
 */
function lamusa_render_allergens_summary($menu) {
    if (!function_exists('lamusa_get_menu_allergens_summary')) {
        return;
    }
    
    $allergen_summary = lamusa_get_menu_allergens_summary($menu->ID);
    
    if (!empty($allergen_summary['contains']) || !empty($allergen_summary['traces'])) {
        echo '<div class="menu-allergens-section">';
        echo '<div class="allergens-badge">ALÉRGENOS</div>';
        echo '<div class="allergens-note">';
        
        if (!empty($allergen_summary['contains'])) {
            echo '<p>Contiene alérgenos ●</p>';
        }
        
        if (!empty($allergen_summary['traces'])) {
            echo '<p>Puede contener trazas T</p>';
        }
        
        // Enlace a página de alérgenos detallada
        $allergens_url = lamusa_get_allergens_url();
        echo '<p><a href="' . esc_url($allergens_url) . '" class="allergens-link">Ver información detallada de alérgenos</a></p>';
        
        echo '</div>';
        echo '</div>';
    }
}

/**
 * Renderizar tabla de alérgenos con iconos en columnas
 */
function lamusa_render_allergens_table($menu) {
    $allergen_groups = get_field('allergen_groups', $menu->ID);
    
    if (!$allergen_groups) {
        echo '<div class="no-allergens-info">';
        echo '<h3>Sin información de alérgenos</h3>';
        echo '<p>No hay información detallada de alérgenos disponible para este menú.</p>';
        echo '</div>';
        return;
    }
    
    // Obtener todos los alérgenos disponibles para las columnas
    $all_allergens = get_terms(array(
        'taxonomy' => 'allergen',
        'hide_empty' => false,
        'meta_key' => 'allergen_order',
        'orderby' => 'meta_value_num',
        'order' => 'ASC'
    ));
    
    if (!$all_allergens || is_wp_error($all_allergens)) {
        echo '<div class="no-allergens-configured">No hay alérgenos configurados en el sistema.</div>';
        return;
    }
    
    echo '<div class="lamusa-allergens-table-container">';
    echo '<table class="lamusa-allergens-table">';
    
    // Cabecera de la tabla
    echo '<thead>';
    echo '<tr>';
    echo '<th class="dish-column">Plato</th>';
    
    // Columna para cada alérgeno con icono personalizado
    foreach ($all_allergens as $allergen) {
        $active = get_term_meta($allergen->term_id, 'allergen_active', true);
        if ($active === '1' || $active === '' || $active === false) {
            echo '<th class="allergen-column" title="' . esc_attr($allergen->name) . '" data-tooltip="' . esc_attr($allergen->description) . '">';
            
            // Usar función helper para renderizar icono (personalizado o por defecto)
            if (function_exists('lamusa_render_allergen_icon')) {
                lamusa_render_allergen_icon($allergen);
            } else {
                // Fallback si la función no está disponible
                $icon = get_term_meta($allergen->term_id, 'allergen_icon', true);
                $color = get_term_meta($allergen->term_id, 'allergen_color', true);
                
                if ($icon) {
                    echo '<i class="' . esc_attr($icon) . '" style="color: ' . esc_attr($color) . '; font-size: 24px;"></i>';
                } else {
                    echo '<span style="color: ' . esc_attr($color) . ';">' . esc_html(substr($allergen->name, 0, 2)) . '</span>';
                }
            }
            
            echo '</th>';
        }
    }
    
    echo '</tr>';
    echo '</thead>';
    
    // Cuerpo de la tabla
    echo '<tbody>';
    
    foreach ($allergen_groups as $group) {
        if (!empty($group['dishes'])) {
            // Fila de título de grupo
            echo '<tr class="group-row">';
            echo '<td class="group-title" colspan="' . (count($all_allergens) + 1) . '">';
            echo '<strong>' . esc_html($group['group_title']) . '</strong>';
            echo '</td>';
            echo '</tr>';
            
            // Filas de platos
            foreach ($group['dishes'] as $dish) {
                $dish_allergens = lamusa_get_dish_allergens($dish);
                
                echo '<tr class="dish-row">';
                
                // Columna del plato
                echo '<td class="dish-name">';
                echo '<strong>' . esc_html($dish['dish_name']) . '</strong>';
                if (!empty($dish['dish_description'])) {
                    echo '<br><small>' . esc_html($dish['dish_description']) . '</small>';
                }
                echo '</td>';
                
                // Columnas de alérgenos
                foreach ($all_allergens as $allergen) {
                    $active = get_term_meta($allergen->term_id, 'allergen_active', true);
                    if ($active === '1' || $active === '' || $active === false) {
                        echo '<td class="allergen-cell" data-allergen="' . esc_attr($allergen->name) . '">';
                        
                        // Verificar si este plato contiene este alérgeno
                        $contains = false;
                        $traces = false;
                        
                        // Los datos de ACF checkbox se guardan como array de strings
                        $dish_contains = $dish['allergens_contains'] ?? array();
                        $dish_traces = $dish['allergens_traces'] ?? array();
                        
                        // Asegurar que son arrays
                        if (!is_array($dish_contains)) {
                            $dish_contains = array();
                        }
                        if (!is_array($dish_traces)) {
                            $dish_traces = array();
                        }
                        
                        error_log('dish_contains después: ' . print_r($dish_contains, true));
                        error_log('dish_traces después: ' . print_r($dish_traces, true));
                        
                        foreach ($dish_contains as $contain_item) {
                            if (is_array($contain_item) && isset($contain_item['value'])) {
                                if ((string)$contain_item['value'] === (string)$allergen->term_id) {
                                    $contains = true;
                                    break;
                                }
                            }
                        }
                        
                        foreach ($dish_traces as $trace_item) {
                            if (is_array($trace_item) && isset($trace_item['value'])) {
                                if ((string)$trace_item['value'] === (string)$allergen->term_id) {
                                    $traces = true;
                                    break;
                                }
                            }
                        }
                        
                        // Mostrar símbolo apropiado
                        if ($contains) {
                            echo '<span class="allergen-contains-symbol">●</span>';
                        } elseif ($traces) {
                            echo '<span class="allergen-traces-symbol">T</span>';
                        }
                        
                        echo '</td>';
                    }
                }
                
                echo '</tr>';
            }
        }
    }
    
    echo '</tbody>';
    echo '</table>';
    
    // Leyenda
    echo '<div class="allergens-legend">';
    echo '<div class="legend-item">';
    echo '<span class="allergen-contains-symbol">●</span><span class="legend-item-text">Contiene alérgenos</span>';
    echo '</div>';
    echo '<div class="legend-item">';
    echo '<span class="allergen-traces-symbol">T</span><span class="legend-item-text">Puede contener trazas</span>';
    echo '</div>';
    echo '</div>';
    
    echo '</div>'; // lamusa-allergens-table-container
}

/**
 * Renderizar navegación entre restaurantes
 */
function lamusa_render_restaurant_navigation($current_restaurant_id, $current_view = 'menu') {
    $restaurants = get_posts(array(
        'post_type' => 'restaurant',
        'post_status' => 'publish',
        'posts_per_page' => -1,
        'orderby' => 'title',
        'order' => 'ASC'
    ));
    
    if (count($restaurants) <= 1) {
        return; // No mostrar navegación si solo hay un restaurante
    }
    
    // echo '<div class="lamusa-restaurant-navigation">';
    // echo '<div class="restaurant-nav-items">';
    
    // foreach ($restaurants as $restaurant) {
    //     if ($restaurant->ID == $current_restaurant_id) {
    //         continue; // Saltar el restaurante actual
    //     }
        
    //     $restaurant_title = get_field('restaurant_title', $restaurant->ID) ?: $restaurant->post_title;
    //     $restaurant_title_secondary = get_field('restaurant_title_secondary', $restaurant->ID);
        
    //     $display_name = $restaurant_title;
    //     if ($restaurant_title_secondary) {
    //         $display_name .= ' ' . $restaurant_title_secondary;
    //     }
        
    //     $restaurant_url = lamusa_get_restaurant_url($restaurant, $current_view);
        
    //     echo '<a href="' . esc_url($restaurant_url) . '" class="restaurant-nav-item">';
    //     echo esc_html($display_name);
    //     echo '</a>';
    // }
    
    // echo '</div>';
    // echo '</div>';
}

