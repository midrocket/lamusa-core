<?php
/**
 * Rewrite Rules para URLs amigables
 */

// Prevenir acceso directo
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Añadir rewrite rules personalizadas
 */
function lamusa_add_rewrite_rules() {
    // Regla para: /pagina/restaurante/alergenos
    add_rewrite_rule(
        '^([^/]+)/([^/]+)/alergenos/?$',
        'index.php?pagename=$matches[1]&lamusa_restaurant=$matches[2]&lamusa_view=allergens',
        'top'
    );
    
    // Regla para: /pagina/restaurante/dia
    add_rewrite_rule(
        '^([^/]+)/([^/]+)/(lunes|martes|miercoles|jueves|viernes|sabado|domingo)/?$',
        'index.php?pagename=$matches[1]&lamusa_restaurant=$matches[2]&lamusa_day=$matches[3]',
        'top'
    );
    
    // Regla para: /pagina/restaurante
    add_rewrite_rule(
        '^([^/]+)/([^/]+)/?$',
        'index.php?pagename=$matches[1]&lamusa_restaurant=$matches[2]',
        'top'
    );
}
add_action('init', 'lamusa_add_rewrite_rules');

/**
 * Añadir query vars personalizadas
 */
function lamusa_add_query_vars($vars) {
    $vars[] = 'lamusa_restaurant';
    $vars[] = 'lamusa_view';
    $vars[] = 'lamusa_day';
    return $vars;
}
add_filter('query_vars', 'lamusa_add_query_vars');



/**
 * Flush rewrite rules cuando se activa el plugin
 */
function lamusa_flush_rewrite_rules() {
    lamusa_add_rewrite_rules();
    flush_rewrite_rules();
}
register_activation_hook(LAMUSA_CORE_PLUGIN_FILE, 'lamusa_flush_rewrite_rules');
register_deactivation_hook(LAMUSA_CORE_PLUGIN_FILE, 'flush_rewrite_rules');
