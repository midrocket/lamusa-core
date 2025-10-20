<?php
/**
 * Página de ayuda en el admin
 */

// Prevenir acceso directo
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Añadir página de ayuda al menú de admin
 */
add_action('admin_menu', 'lamusa_add_help_page');
function lamusa_add_help_page() {
    add_submenu_page(
        'edit.php?post_type=restaurant',
        __('Enlaces y Ayuda', 'lamusa-core'),
        __('Enlaces y Ayuda', 'lamusa-core'),
        'manage_options',
        'lamusa-help',
        'lamusa_help_page_callback'
    );
}

/**
 * Callback para la página de ayuda
 */
function lamusa_help_page_callback() {
    ?>
    <div class="wrap">
        <h1><?php _e('La Musa Core - Enlaces y Ayuda', 'lamusa-core'); ?></h1>
        
        <div class="lamusa-help-container">
            
            <!-- Sección de Enlaces de Menús -->
            <div class="lamusa-help-section">
                <h2><?php _e('Enlaces de Menús del Día', 'lamusa-core'); ?></h2>
                <p><?php _e('Utiliza estos enlaces para acceder directamente a los menús de cada restaurante:', 'lamusa-core'); ?></p>
                
                <?php lamusa_render_menu_links_table(); ?>
            </div>
            
            <!-- Sección de Enlaces de Alérgenos -->
            <div class="lamusa-help-section">
                <h2><?php _e('Enlaces de Alérgenos', 'lamusa-core'); ?></h2>
                <p><?php _e('Enlaces directos a la información detallada de alérgenos por restaurante:', 'lamusa-core'); ?></p>
                
                <?php lamusa_render_allergen_links_table(); ?>
            </div>
            
            <!-- Sección de Shortcodes -->
            <div class="lamusa-help-section">
                <h2><?php _e('Shortcodes Disponibles', 'lamusa-core'); ?></h2>
                <?php lamusa_render_shortcode_help(); ?>
            </div>
            
            <!-- Sección de Configuración -->
            <div class="lamusa-help-section">
                <h2><?php _e('Configuración de URLs', 'lamusa-core'); ?></h2>
                <?php lamusa_render_url_configuration_help(); ?>
            </div>
            
        </div>
    </div>
    
    <style>
    .lamusa-help-container {
        max-width: 1200px;
    }
    
    .lamusa-help-section {
        background: #fff;
        padding: 20px;
        margin: 20px 0;
        border: 1px solid #ccd0d4;
        border-radius: 4px;
    }
    
    .lamusa-help-section h2 {
        color: #e74c3c;
        border-bottom: 2px solid #e74c3c;
        padding-bottom: 10px;
        margin-bottom: 20px;
    }
    
    .lamusa-links-table {
        width: 100%;
        border-collapse: collapse;
        margin: 15px 0;
    }
    
    .lamusa-links-table th,
    .lamusa-links-table td {
        padding: 12px;
        text-align: left;
        border-bottom: 1px solid #ddd;
    }
    
    .lamusa-links-table th {
        background: #f8f9fa;
        font-weight: 600;
        color: #333;
    }
    
    .lamusa-links-table .url-link {
        font-family: monospace;
        background: #f1f1f1;
        padding: 4px 8px;
        border-radius: 3px;
        font-size: 13px;
        word-break: break-all;
    }
    
    .lamusa-links-table .copy-btn {
        background: #007cba;
        color: white;
        border: none;
        padding: 4px 8px;
        border-radius: 3px;
        cursor: pointer;
        font-size: 11px;
        margin-left: 8px;
    }
    
    .lamusa-links-table .copy-btn:hover {
        background: #005a87;
    }
    
    .shortcode-example {
        background: #f9f9f9;
        border: 1px solid #ddd;
        border-radius: 4px;
        padding: 15px;
        margin: 10px 0;
        font-family: monospace;
        font-size: 13px;
    }
    
    .shortcode-example .shortcode-title {
        font-weight: bold;
        color: #e74c3c;
        margin-bottom: 8px;
        font-family: inherit;
    }
    
    .status-indicator {
        display: inline-block;
        width: 10px;
        height: 10px;
        border-radius: 50%;
        margin-right: 8px;
    }
    
    .status-active { background: #46b450; }
    .status-inactive { background: #dc3232; }
    .status-no-menu { background: #ffb900; }
    </style>
    <?php
}

/**
 * Renderizar tabla de enlaces de menús
 */
function lamusa_render_menu_links_table() {
    $restaurants = get_posts(array(
        'post_type' => 'restaurant',
        'post_status' => 'publish',
        'posts_per_page' => -1,
        'orderby' => 'title',
        'order' => 'ASC'
    ));
    
    if (empty($restaurants)) {
        echo '<p>No hay restaurantes configurados.</p>';
        return;
    }
    
    // Obtener página actual que contiene el shortcode
    $pages_with_shortcode = lamusa_find_pages_with_shortcode();
    
    echo '<table class="lamusa-links-table">';
    echo '<thead>';
    echo '<tr>';
    echo '<th>Restaurante</th>';
    echo '<th>Estado</th>';
    echo '<th>URL del Menú</th>';
    echo '<th>Días Disponibles</th>';
    echo '<th>Acciones</th>';
    echo '</tr>';
    echo '</thead>';
    echo '<tbody>';
    
    foreach ($restaurants as $restaurant) {
        $restaurant_title = get_field('restaurant_title', $restaurant->ID) ?: $restaurant->post_title;
        $restaurant_title_secondary = get_field('restaurant_title_secondary', $restaurant->ID);
        
        $display_name = $restaurant_title;
        if ($restaurant_title_secondary) {
            $display_name .= ' ' . $restaurant_title_secondary;
        }
        
        // Verificar si tiene menú activo
        $has_menu = lamusa_restaurant_has_active_menu($restaurant->ID);
        $menu_days = lamusa_get_restaurant_active_days($restaurant->ID);
        
        // Generar slug del restaurante
        $restaurant_slug = sanitize_title($restaurant_title);
        if ($restaurant_title_secondary) {
            $restaurant_slug .= '-' . sanitize_title($restaurant_title_secondary);
        }
        
        echo '<tr>';
        echo '<td><strong>' . esc_html($display_name) . '</strong></td>';
        
        // Estado
        echo '<td>';
        if ($has_menu) {
            echo '<span class="status-indicator status-active"></span>Menú Activo';
        } else {
            echo '<span class="status-indicator status-no-menu"></span>Sin Menú';
        }
        echo '</td>';
        
        // URL del menú
        echo '<td>';
        if (!empty($pages_with_shortcode)) {
            $base_page = $pages_with_shortcode[0];
            $menu_url = home_url('/' . $base_page . '/' . $restaurant_slug);
            echo '<code class="url-link">' . esc_html($menu_url) . '</code>';
            echo '<button class="copy-btn" onclick="copyToClipboard(\'' . esc_js($menu_url) . '\')">Copiar</button>';
        } else {
            echo '<em>Crea una página con el shortcode [lamusa_menu_navigator]</em>';
        }
        echo '</td>';
        
        // Días disponibles
        echo '<td>';
        if (!empty($menu_days)) {
            foreach ($menu_days as $day) {
                echo '<span class="day-badge">' . ucfirst($day) . '</span> ';
            }
        } else {
            echo '—';
        }
        echo '</td>';
        
        // Acciones
        echo '<td>';
        echo '<a href="' . get_edit_post_link($restaurant->ID) . '" class="button button-small">Editar</a> ';
        
        $menus = get_posts(array(
            'post_type' => 'weekly_menu',
            'posts_per_page' => 1,
            'meta_query' => array(
                array(
                    'key' => 'restaurant',
                    'value' => $restaurant->ID,
                    'compare' => '='
                )
            )
        ));
        
        if (!empty($menus)) {
            echo '<a href="' . get_edit_post_link($menus[0]->ID) . '" class="button button-small">Editar Menú</a>';
        } else {
            echo '<a href="' . admin_url('post-new.php?post_type=weekly_menu&restaurant=' . $restaurant->ID) . '" class="button button-primary button-small">Crear Menú</a>';
        }
        echo '</td>';
        
        echo '</tr>';
    }
    
    echo '</tbody>';
    echo '</table>';
}

/**
 * Renderizar tabla de enlaces de alérgenos
 */
function lamusa_render_allergen_links_table() {
    $restaurants = get_posts(array(
        'post_type' => 'restaurant',
        'post_status' => 'publish',
        'posts_per_page' => -1,
        'orderby' => 'title',
        'order' => 'ASC'
    ));
    
    $pages_with_shortcode = lamusa_find_pages_with_shortcode();
    
    echo '<table class="lamusa-links-table">';
    echo '<thead>';
    echo '<tr>';
    echo '<th>Restaurante</th>';
    echo '<th>URL de Alérgenos</th>';
    echo '<th>Alérgenos Configurados</th>';
    echo '</tr>';
    echo '</thead>';
    echo '<tbody>';
    
    foreach ($restaurants as $restaurant) {
        $restaurant_title = get_field('restaurant_title', $restaurant->ID) ?: $restaurant->post_title;
        $restaurant_title_secondary = get_field('restaurant_title_secondary', $restaurant->ID);
        
        $display_name = $restaurant_title;
        if ($restaurant_title_secondary) {
            $display_name .= ' ' . $restaurant_title_secondary;
        }
        
        $restaurant_slug = sanitize_title($restaurant_title);
        if ($restaurant_title_secondary) {
            $restaurant_slug .= '-' . sanitize_title($restaurant_title_secondary);
        }
        
        echo '<tr>';
        echo '<td><strong>' . esc_html($display_name) . '</strong></td>';
        
        // URL de alérgenos
        echo '<td>';
        if (!empty($pages_with_shortcode)) {
            $base_page = $pages_with_shortcode[0];
            $allergens_url = home_url('/' . $base_page . '/' . $restaurant_slug . '/alergenos');
            echo '<code class="url-link">' . esc_html($allergens_url) . '</code>';
            echo '<button class="copy-btn" onclick="copyToClipboard(\'' . esc_js($allergens_url) . '\')">Copiar</button>';
        } else {
            echo '<em>Crea una página con el shortcode</em>';
        }
        echo '</td>';
        
        // Alérgenos configurados
        echo '<td>';
        $allergen_count = lamusa_count_restaurant_allergens($restaurant->ID);
        if ($allergen_count > 0) {
            echo '<span class="status-indicator status-active"></span>' . $allergen_count . ' alérgenos configurados';
        } else {
            echo '<span class="status-indicator status-inactive"></span>Sin alérgenos';
        }
        echo '</td>';
        
        echo '</tr>';
    }
    
    echo '</tbody>';
    echo '</table>';
}

/**
 * Renderizar ayuda de shortcodes
 */
function lamusa_render_shortcode_help() {
    ?>
    <div class="shortcode-example">
        <div class="shortcode-title">Shortcode Principal (Recomendado)</div>
        <code>[lamusa_menu_navigator]</code>
        <p>Navegador completo con detección automática de parámetros de URL.</p>
    </div>
    
    <div class="shortcode-example">
        <div class="shortcode-title">Shortcode de Menú Específico</div>
        <code>[lamusa_restaurant_menu restaurant="limbo-tribunal" day="lunes"]</code>
        <p>Muestra el menú de un restaurante y día específicos.</p>
    </div>
    
    <div class="shortcode-example">
        <div class="shortcode-title">Shortcode de Alérgenos</div>
        <code>[lamusa_restaurant_allergens restaurant="limbo-tribunal"]</code>
        <p>Muestra la información detallada de alérgenos de un restaurante.</p>
    </div>
    
    <h3>Parámetros Disponibles:</h3>
    <ul>
        <li><strong>restaurant:</strong> Slug del restaurante (ej: "limbo-tribunal")</li>
        <li><strong>day:</strong> Día de la semana (lunes, martes, miercoles, jueves, viernes, sabado, domingo)</li>
        <li><strong>view:</strong> Tipo de vista ("menu" o "allergens")</li>
        <li><strong>show_navigation:</strong> Mostrar navegación entre restaurantes ("true" o "false")</li>
        <li><strong>show_all_days:</strong> Mostrar todos los días o solo activos ("true" o "false")</li>
    </ul>
    <?php
}

/**
 * Renderizar ayuda de configuración de URLs
 */
function lamusa_render_url_configuration_help() {
    ?>
    <h3>Estructura de URLs</h3>
    <p>El plugin genera automáticamente URLs amigables con la siguiente estructura:</p>
    
    <div class="shortcode-example">
        <div class="shortcode-title">Menú del Día</div>
        <code>https://tu-sitio.com/menu-del-dia/limbo-tribunal</code>
        <p>Muestra el menú actual del restaurante Limbo Tribunal</p>
    </div>
    
    <div class="shortcode-example">
        <div class="shortcode-title">Día Específico</div>
        <code>https://tu-sitio.com/menu-del-dia/limbo-tribunal/martes</code>
        <p>Muestra el menú del martes del restaurante Limbo Tribunal</p>
    </div>
    
    <div class="shortcode-example">
        <div class="shortcode-title">Alérgenos</div>
        <code>https://tu-sitio.com/menu-del-dia/limbo-tribunal/alergenos</code>
        <p>Muestra la información detallada de alérgenos del restaurante</p>
    </div>
    
    <h3>Configuración Requerida</h3>
    <ol>
        <li><strong>Crear una página</strong> (ej: "Menu del Día") con el slug deseado</li>
        <li><strong>Añadir el shortcode</strong> <code>[lamusa_menu_navigator]</code> al contenido</li>
        <li><strong>Publicar la página</strong></li>
        <li><strong>Las URLs automáticas</strong> estarán disponibles inmediatamente</li>
    </ol>
    
    <div class="notice notice-info inline">
        <p><strong>Nota:</strong> Después de activar el plugin o hacer cambios, ve a <strong>Ajustes > Enlaces permanentes</strong> y haz click en "Guardar cambios" para actualizar las reglas de URL.</p>
    </div>
    
    <h3>Páginas Detectadas con Shortcode</h3>
    <?php
    $pages_with_shortcode = lamusa_find_pages_with_shortcode();
    
    if (!empty($pages_with_shortcode)) {
        echo '<ul>';
        foreach ($pages_with_shortcode as $page_slug) {
            $page = get_page_by_path($page_slug);
            if ($page) {
                echo '<li>';
                echo '<strong>' . esc_html($page->post_title) . '</strong> ';
                echo '(<code>' . esc_html($page_slug) . '</code>) ';
                echo '<a href="' . get_edit_post_link($page->ID) . '" class="button button-small">Editar</a> ';
                echo '<a href="' . get_permalink($page->ID) . '" class="button button-small" target="_blank">Ver</a>';
                echo '</li>';
            }
        }
        echo '</ul>';
    } else {
        echo '<div class="notice notice-warning inline">';
        echo '<p>No se encontraron páginas con el shortcode <code>[lamusa_menu_navigator]</code>. Crea una página y añade el shortcode para empezar.</p>';
        echo '</div>';
    }
    ?>
    
    <script>
    function copyToClipboard(text) {
        navigator.clipboard.writeText(text).then(function() {
            // Mostrar confirmación temporal
            event.target.textContent = 'Copiado!';
            setTimeout(function() {
                event.target.textContent = 'Copiar';
            }, 2000);
        });
    }
    </script>
    <?php
}

/**
 * Funciones helper para la página de ayuda
 */

function lamusa_restaurant_has_active_menu($restaurant_id) {
    $menus = get_posts(array(
        'post_type' => 'weekly_menu',
        'post_status' => 'publish',
        'posts_per_page' => 1,
        'meta_query' => array(
            array(
                'key' => 'restaurant',
                'value' => $restaurant_id,
                'compare' => '='
            )
        )
    ));
    
    return !empty($menus);
}

function lamusa_get_restaurant_active_days($restaurant_id) {
    $menus = get_posts(array(
        'post_type' => 'weekly_menu',
        'post_status' => 'publish',
        'posts_per_page' => 1,
        'meta_query' => array(
            array(
                'key' => 'restaurant',
                'value' => $restaurant_id,
                'compare' => '='
            )
        )
    ));
    
    if (empty($menus)) {
        return array();
    }
    
    $menu_days = get_field('menu_days', $menus[0]->ID);
    $active_days = array();
    
    if ($menu_days) {
        foreach ($menu_days as $day) {
            if ($day['day_active']) {
                $active_days[] = $day['day_name'];
            }
        }
    }
    
    return $active_days;
}

function lamusa_count_restaurant_allergens($restaurant_id) {
    $menus = get_posts(array(
        'post_type' => 'weekly_menu',
        'post_status' => 'publish',
        'posts_per_page' => 1,
        'meta_query' => array(
            array(
                'key' => 'restaurant',
                'value' => $restaurant_id,
                'compare' => '='
            )
        )
    ));
    
    if (empty($menus)) {
        return 0;
    }
    
    if (function_exists('lamusa_get_menu_allergens_summary')) {
        $allergen_summary = lamusa_get_menu_allergens_summary($menus[0]->ID);
        return count($allergen_summary['contains']) + count($allergen_summary['traces']);
    }
    
    return 0;
}

function lamusa_find_pages_with_shortcode() {
    $pages = get_posts(array(
        'post_type' => 'page',
        'post_status' => 'publish',
        'posts_per_page' => -1,
        's' => '[lamusa_menu_navigator]'
    ));
    
    $page_slugs = array();
    foreach ($pages as $page) {
        if (strpos($page->post_content, '[lamusa_menu_navigator]') !== false) {
            $page_slugs[] = $page->post_name;
        }
    }
    
    return $page_slugs;
}
