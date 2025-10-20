<?php
/**
 * Página de ajustes generales del plugin
 */

// Prevenir acceso directo
if (!defined('ABSPATH')) {
    exit;
}


/**
 * Registrar campos ACF para la página de ajustes
 */
add_action('acf/include_fields', 'lamusa_register_settings_fields');
function lamusa_register_settings_fields() {
    if (!function_exists('acf_add_local_field_group')) {
        return;
    }
    
    acf_add_local_field_group(array(
        'key' => 'group_lamusa_settings',
        'title' => 'Ajustes de La Musa Core',
        'fields' => array(
            array(
                'key' => 'field_allergen_icons_tab',
                'label' => 'Iconos de Alérgenos',
                'name' => '',
                'type' => 'tab',
                'instructions' => '',
                'required' => 0,
                'placement' => 'top',
                'endpoint' => 0,
            ),
            array(
                'key' => 'field_allergen_icons_message',
                'label' => 'Configuración de Iconos',
                'name' => 'allergen_icons_message',
                'type' => 'message',
                'message' => 'Sube iconos personalizados para cada alérgeno. Se recomienda usar archivos SVG o PNG de 64x64px para mejor calidad. Si no subes un icono personalizado, se usará el icono por defecto configurado en la taxonomía.',
                'new_lines' => 'wpautop',
                'esc_html' => 0,
            ),
            array(
                'key' => 'field_global_icon_size',
                'label' => 'Tamaño Global de Iconos',
                'name' => 'global_icon_size',
                'type' => 'number',
                'instructions' => 'Tamaño en píxeles para todos los iconos de alérgenos',
                'required' => 0,
                'default_value' => 24,
                'min' => 16,
                'max' => 64,
                'step' => 2,
                'append' => 'px',
                'wrapper' => array('width' => '50'),
            ),
            array(
                'key' => 'field_allergen_custom_icons',
                'label' => 'Iconos Personalizados de Alérgenos',
                'name' => 'allergen_custom_icons',
                'type' => 'repeater',
                'instructions' => 'Configura iconos personalizados para cada alérgeno',
                'required' => 0,
                'layout' => 'table',
                'button_label' => 'Añadir Icono de Alérgeno',
                'sub_fields' => array(
                    array(
                        'key' => 'field_allergen_select',
                        'label' => 'Alérgeno',
                        'name' => 'allergen',
                        'type' => 'taxonomy',
                        'instructions' => 'Selecciona el alérgeno',
                        'required' => 1,
                        'taxonomy' => 'allergen',
                        'field_type' => 'select',
                        'add_term' => 0,
                        'save_terms' => 0,
                        'load_terms' => 0,
                        'return_format' => 'object',
                        'multiple' => 0,
                    ),
                    array(
                        'key' => 'field_allergen_custom_icon',
                        'label' => 'Icono Personalizado',
                        'name' => 'custom_icon',
                        'type' => 'image',
                        'instructions' => 'Sube un icono personalizado (SVG o PNG recomendado)',
                        'required' => 0,
                        'return_format' => 'array',
                        'preview_size' => 'thumbnail',
                        'library' => 'all',
                        'min_width' => '',
                        'min_height' => '',
                        'min_size' => '',
                        'max_width' => 128,
                        'max_height' => 128,
                        'max_size' => '500KB',
                        'mime_types' => 'svg,png,jpg,jpeg',
                    ),
                ),
            ),
        ),
        'location' => array(
            array(
                array(
                    'param' => 'options_page',
                    'operator' => '==',
                    'value' => 'lamusa-settings',
                ),
            ),
        ),
        'menu_order' => 0,
        'position' => 'normal',
        'style' => 'default',
        'label_placement' => 'top',
        'instruction_placement' => 'label',
        'active' => true,
        'description' => 'Configuración general del plugin La Musa Core',
    ));
}


/**
 * Renderizar lista de alérgenos disponibles
 */
function lamusa_render_available_allergens_list() {
    $allergens = get_terms(array(
        'taxonomy' => 'allergen',
        'hide_empty' => false,
        'meta_key' => 'allergen_order',
        'orderby' => 'meta_value_num',
        'order' => 'ASC'
    ));
    
    if (!$allergens || is_wp_error($allergens)) {
        echo '<p>No hay alérgenos configurados. <a href="' . admin_url('edit-tags.php?taxonomy=allergen&post_type=weekly_menu') . '">Crear alérgenos</a></p>';
        return;
    }
    
    echo '<div class="allergens-preview-list">';
    
    foreach ($allergens as $allergen) {
        $active = get_term_meta($allergen->term_id, 'allergen_active', true);
        $icon = get_term_meta($allergen->term_id, 'allergen_icon', true);
        $color = get_term_meta($allergen->term_id, 'allergen_color', true);
        
        // Verificar si tiene icono personalizado
        $custom_icon = lamusa_get_custom_allergen_icon($allergen->term_id);
        
        echo '<div class="allergen-preview-item">';
        
        // Mostrar icono
        echo '<div class="allergen-preview-icon">';
        if ($custom_icon) {
            echo '<img src="' . esc_url($custom_icon['url']) . '" alt="' . esc_attr($allergen->name) . '" style="width: 24px; height: 24px; object-fit: contain;">';
        } elseif ($icon) {
            echo '<i class="' . esc_attr($icon) . '" style="color: ' . esc_attr($color) . '; font-size: 24px;"></i>';
        } else {
            echo '<span style="color: ' . esc_attr($color) . '; font-weight: bold;">' . esc_html(substr($allergen->name, 0, 2)) . '</span>';
        }
        echo '</div>';
        
        // Nombre del alérgeno
        echo '<div class="allergen-preview-name">' . esc_html($allergen->name) . '</div>';
        
        // Estado
        echo '<div class="allergen-preview-status">';
        if ($custom_icon) {
            echo 'Icono personalizado';
        } elseif ($icon) {
            echo 'Icono por defecto';
        } else {
            echo 'Sin icono';
        }
        echo '</div>';
        
        echo '</div>';
    }
    
    echo '</div>';
}

/**
 * Obtener icono personalizado de un alérgeno
 */
function lamusa_get_custom_allergen_icon($allergen_id) {
    $custom_icons = get_field('allergen_custom_icons', 'option');
    
    if (!$custom_icons) {
        return null;
    }
    
    foreach ($custom_icons as $icon_config) {
        if (isset($icon_config['allergen']) && $icon_config['allergen']->term_id == $allergen_id) {
            return $icon_config['custom_icon'] ?? null;
        }
    }
    
    return null;
}

/**
 * Obtener tamaño global de iconos de alérgenos
 */
function lamusa_get_global_allergen_icon_size() {
    $global_size = get_field('global_icon_size', 'option');
    return $global_size ?: 24;
}

/**
 * Renderizar icono de alérgeno (personalizado o por defecto)
 */
function lamusa_render_allergen_icon($allergen, $size = null) {
    // Usar tamaño global si no se especifica
    if (!$size) {
        $size = lamusa_get_global_allergen_icon_size();
    }
    
    // Intentar obtener icono personalizado primero
    $custom_icon = lamusa_get_custom_allergen_icon($allergen->term_id);
    
    if ($custom_icon) {
        echo '<img src="' . esc_url($custom_icon['url']) . '" alt="' . esc_attr($allergen->name) . '" style="width: ' . esc_attr($size) . 'px; height: ' . esc_attr($size) . 'px; object-fit: contain;">';
        return;
    }
    
    // Fallback a icono por defecto
    $icon = get_term_meta($allergen->term_id, 'allergen_icon', true);
    $color = get_term_meta($allergen->term_id, 'allergen_color', true);
    
    if ($icon) {
        echo '<i class="' . esc_attr($icon) . '" style="color: ' . esc_attr($color) . '; font-size: ' . esc_attr($size) . 'px;"></i>';
    } else {
        // Fallback final: iniciales
        echo '<span style="color: ' . esc_attr($color) . '; font-weight: bold; font-size: ' . esc_attr($size * 0.8) . 'px;">' . esc_html(substr($allergen->name, 0, 2)) . '</span>';
    }
}

/**
 * Inicializar página de opciones ACF
 */
add_action('acf/init', 'lamusa_acf_add_options_pages');
function lamusa_acf_add_options_pages() {
    if (function_exists('acf_add_options_sub_page')) {
        acf_add_options_sub_page(array(
            'page_title' => __('Ajustes de La Musa Core', 'lamusa-core'),
            'menu_title' => __('Ajustes', 'lamusa-core'),
            'menu_slug' => 'lamusa-settings',
            'parent_slug' => 'edit.php?post_type=restaurant',
            'capability' => 'manage_options',
        ));
    }
}
