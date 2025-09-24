<?php
/**
 * Integración con Elementor
 */

// Prevenir acceso directo
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Verificar si Elementor está activo e inicializar
 */
function lamusa_init_elementor() {
    // Verificar que Elementor esté disponible
    if (!did_action('elementor/loaded')) {
        return;
    }
    
    // Registrar widgets
    add_action('elementor/widgets/widgets_registered', 'lamusa_register_elementor_widgets');
    add_action('elementor/elements/categories_registered', 'lamusa_register_elementor_categories');
    
    // Registrar etiquetas dinámicas
    add_action('elementor/dynamic_tags/register_tags', 'lamusa_register_dynamic_tags');
    
    error_log('La Musa Core: Hooks de Elementor registrados');
}
add_action('init', 'lamusa_init_elementor');

/**
 * Registrar categoría de widgets
 */
function lamusa_register_elementor_categories($elements_manager) {
    $elements_manager->add_category(
        'lamusa-core',
        array(
            'title' => __('La Musa', 'lamusa-core'),
            'icon' => 'fa fa-store',
        )
    );
    error_log('La Musa Core: Categoría de widgets registrada');
}

/**
 * Registrar widgets
 */
function lamusa_register_elementor_widgets() {
    $widget_files = [
        'allergen-info-widget.php',
        'menu-navigator-widget.php'
    ];
    
    foreach ($widget_files as $file) {
        $filepath = LAMUSA_CORE_PLUGIN_DIR . 'includes/elementor-widgets/' . $file;
        if (file_exists($filepath)) {
            require_once $filepath;
        }
    }
    
    // Registrar cada widget
    if (class_exists('LaMusaAllergenInfoWidget')) {
        \Elementor\Plugin::instance()->widgets_manager->register_widget_type(new \LaMusaAllergenInfoWidget());
    }
    
    if (class_exists('LaMusaMenuNavigatorWidget')) {
        \Elementor\Plugin::instance()->widgets_manager->register_widget_type(new \LaMusaMenuNavigatorWidget());
    }
    
    error_log('La Musa Core: Widgets registrados');
}

/**
 * Registrar etiquetas dinámicas
 */
function lamusa_register_dynamic_tags($dynamic_tags) {
    error_log('La Musa Core: Iniciando registro de etiquetas dinámicas - Hook ejecutado');
    
    // Verificar que el objeto sea válido
    if (!$dynamic_tags || !method_exists($dynamic_tags, 'register_group')) {
        error_log('La Musa Core: ERROR - Objeto dynamic_tags inválido');
        return;
    }
    
    // Registrar el grupo
    try {
        $dynamic_tags->register_group(
            'lamusa-core',
            [
                'title' => __('La Musa', 'lamusa-core')
            ]
        );
        error_log('La Musa Core: Grupo "La Musa" registrado correctamente');
    } catch (Exception $e) {
        error_log('La Musa Core: ERROR al registrar grupo - ' . $e->getMessage());
        return;
    }
    
    // Cargar archivos de etiquetas dinámicas
    $tag_files = [
        'restaurant-field-tag.php' => 'LaMusaRestaurantFieldTag',
        'menu-field-tag.php' => 'LaMusaMenuFieldTag',
        'menu-content-tag.php' => 'LaMusaMenuContentTag',
        'allergen-list-tag.php' => 'LaMusaAllergenListTag'
    ];
    
    foreach ($tag_files as $file => $class_name) {
        $filepath = LAMUSA_CORE_PLUGIN_DIR . 'includes/elementor-dynamic-tags/' . $file;
        
        if (file_exists($filepath)) {
            require_once $filepath;
            error_log('La Musa Core: Archivo cargado - ' . $file);
            
            if (class_exists($class_name)) {
                try {
                    $tag_instance = new $class_name();
                    $dynamic_tags->register_tag($tag_instance);
                    error_log('La Musa Core: Etiqueta registrada - ' . $tag_instance->get_name());
                } catch (Exception $e) {
                    error_log('La Musa Core: ERROR al registrar etiqueta ' . $class_name . ' - ' . $e->getMessage());
                }
            } else {
                error_log('La Musa Core: ERROR - Clase no encontrada: ' . $class_name);
            }
        } else {
            error_log('La Musa Core: ERROR - Archivo no encontrado: ' . $filepath);
        }
    }
    
    error_log('La Musa Core: Registro de etiquetas dinámicas completado');
}

/**
 * Debug de estado de Elementor
 */
add_action('wp_loaded', function() {
    if (is_admin()) {
        error_log('La Musa Core: Estado de Elementor:');
        error_log('- Elementor\\Plugin existe: ' . (class_exists('Elementor\\Plugin') ? 'SÍ' : 'NO'));
        error_log('- elementor/loaded ejecutado: ' . (did_action('elementor/loaded') ? 'SÍ' : 'NO'));
        
        if (class_exists('Elementor\\Plugin')) {
            $elementor = \Elementor\Plugin::instance();
            error_log('- Dynamic Tags Manager existe: ' . (isset($elementor->dynamic_tags) ? 'SÍ' : 'NO'));
        }
    }
});