<?php
/**
 * Registro de Taxonomías personalizadas
 */

// Prevenir acceso directo
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Registrar todas las taxonomías del plugin
 */
function lamusa_register_taxonomies() {
    lamusa_register_allergen_taxonomy();
}

/**
 * Registrar taxonomía para Alérgenos
 */
function lamusa_register_allergen_taxonomy() {
    $labels = array(
        'name'                       => _x('Alérgenos', 'Taxonomy General Name', 'lamusa-core'),
        'singular_name'              => _x('Alérgeno', 'Taxonomy Singular Name', 'lamusa-core'),
        'menu_name'                  => __('Alérgenos', 'lamusa-core'),
        'all_items'                  => __('Todos los Alérgenos', 'lamusa-core'),
        'parent_item'                => __('Alérgeno Padre', 'lamusa-core'),
        'parent_item_colon'          => __('Alérgeno Padre:', 'lamusa-core'),
        'new_item_name'              => __('Nuevo Nombre de Alérgeno', 'lamusa-core'),
        'add_new_item'               => __('Añadir Nuevo Alérgeno', 'lamusa-core'),
        'edit_item'                  => __('Editar Alérgeno', 'lamusa-core'),
        'update_item'                => __('Actualizar Alérgeno', 'lamusa-core'),
        'view_item'                  => __('Ver Alérgeno', 'lamusa-core'),
        'separate_items_with_commas' => __('Separar alérgenos con comas', 'lamusa-core'),
        'add_or_remove_items'        => __('Añadir o quitar alérgenos', 'lamusa-core'),
        'choose_from_most_used'      => __('Elegir entre los más usados', 'lamusa-core'),
        'popular_items'              => __('Alérgenos Populares', 'lamusa-core'),
        'search_items'               => __('Buscar Alérgenos', 'lamusa-core'),
        'not_found'                  => __('No Encontrado', 'lamusa-core'),
        'no_terms'                   => __('No hay alérgenos', 'lamusa-core'),
        'items_list'                 => __('Lista de alérgenos', 'lamusa-core'),
        'items_list_navigation'      => __('Navegación de lista de alérgenos', 'lamusa-core'),
    );

    $args = array(
        'labels'                     => $labels,
        'hierarchical'               => false,
        'public'                     => true,
        'show_ui'                    => true,
        'show_admin_column'          => false, // No mostrar en las columnas de los posts por defecto
        'show_in_nav_menus'          => false,
        'show_tagcloud'              => false,
        'show_in_menu'               => 'edit.php?post_type=restaurant', // Mostrar bajo restaurantes
        'query_var'                  => true,
        'rewrite'                    => array('slug' => 'alergeno'),
        'show_in_rest'               => true,
        'rest_base'                  => 'allergens',
        'rest_controller_class'      => 'WP_REST_Terms_Controller',
        'description'                => __('Taxonomía para gestionar los alérgenos de los platos', 'lamusa-core'),
    );

    register_taxonomy('allergen', array('weekly_menu'), $args);
}

/**
 * Añadir campos personalizados a la taxonomía de alérgenos
 */
add_action('allergen_add_form_fields', 'lamusa_add_allergen_fields');
function lamusa_add_allergen_fields() {
    ?>
    <div class="form-field term-icon-wrap">
        <label for="allergen_icon"><?php _e('Icono del Alérgeno', 'lamusa-core'); ?></label>
        <input type="text" id="allergen_icon" name="allergen_icon" value="" placeholder="dashicons-warning" />
        <p><?php _e('Clase CSS del icono (ej: dashicons-warning, fa-exclamation-triangle)', 'lamusa-core'); ?></p>
    </div>
    
    <div class="form-field term-color-wrap">
        <label for="allergen_color"><?php _e('Color del Alérgeno', 'lamusa-core'); ?></label>
        <input type="color" id="allergen_color" name="allergen_color" value="#ff0000" />
        <p><?php _e('Color para identificar visualmente el alérgeno', 'lamusa-core'); ?></p>
    </div>
    
    <div class="form-field term-order-wrap">
        <label for="allergen_order"><?php _e('Orden de Visualización', 'lamusa-core'); ?></label>
        <input type="number" id="allergen_order" name="allergen_order" value="0" min="0" />
        <p><?php _e('Orden numérico para mostrar los alérgenos (0 = primero)', 'lamusa-core'); ?></p>
    </div>
    
    <div class="form-field term-active-wrap">
        <label for="allergen_active">
            <input type="checkbox" id="allergen_active" name="allergen_active" value="1" checked />
            <?php _e('Alérgeno Activo', 'lamusa-core'); ?>
        </label>
        <p><?php _e('Marcar si el alérgeno debe aparecer en los formularios', 'lamusa-core'); ?></p>
    </div>
    <?php
}

/**
 * Añadir campos personalizados al formulario de edición de alérgenos
 */
add_action('allergen_edit_form_fields', 'lamusa_edit_allergen_fields');
function lamusa_edit_allergen_fields($term) {
    $icon = get_term_meta($term->term_id, 'allergen_icon', true);
    $color = get_term_meta($term->term_id, 'allergen_color', true);
    $order = get_term_meta($term->term_id, 'allergen_order', true);
    $active = get_term_meta($term->term_id, 'allergen_active', true);
    ?>
    <tr class="form-field term-icon-wrap">
        <th scope="row"><label for="allergen_icon"><?php _e('Icono del Alérgeno', 'lamusa-core'); ?></label></th>
        <td>
            <input type="text" id="allergen_icon" name="allergen_icon" value="<?php echo esc_attr($icon); ?>" placeholder="dashicons-warning" />
            <p class="description"><?php _e('Clase CSS del icono (ej: dashicons-warning, fa-exclamation-triangle)', 'lamusa-core'); ?></p>
        </td>
    </tr>
    
    <tr class="form-field term-color-wrap">
        <th scope="row"><label for="allergen_color"><?php _e('Color del Alérgeno', 'lamusa-core'); ?></label></th>
        <td>
            <input type="color" id="allergen_color" name="allergen_color" value="<?php echo esc_attr($color ?: '#ff0000'); ?>" />
            <p class="description"><?php _e('Color para identificar visualmente el alérgeno', 'lamusa-core'); ?></p>
        </td>
    </tr>
    
    <tr class="form-field term-order-wrap">
        <th scope="row"><label for="allergen_order"><?php _e('Orden de Visualización', 'lamusa-core'); ?></label></th>
        <td>
            <input type="number" id="allergen_order" name="allergen_order" value="<?php echo esc_attr($order ?: '0'); ?>" min="0" />
            <p class="description"><?php _e('Orden numérico para mostrar los alérgenos (0 = primero)', 'lamusa-core'); ?></p>
        </td>
    </tr>
    
    <tr class="form-field term-active-wrap">
        <th scope="row"><label for="allergen_active"><?php _e('Alérgeno Activo', 'lamusa-core'); ?></label></th>
        <td>
            <label for="allergen_active">
                <input type="checkbox" id="allergen_active" name="allergen_active" value="1" <?php checked($active, '1'); ?> />
                <?php _e('Marcar si el alérgeno debe aparecer en los formularios', 'lamusa-core'); ?>
            </label>
        </td>
    </tr>
    <?php
}

/**
 * Guardar los campos personalizados de alérgenos
 */
add_action('created_allergen', 'lamusa_save_allergen_fields');
add_action('edited_allergen', 'lamusa_save_allergen_fields');
function lamusa_save_allergen_fields($term_id) {
    if (isset($_POST['allergen_icon'])) {
        update_term_meta($term_id, 'allergen_icon', sanitize_text_field($_POST['allergen_icon']));
    }
    
    if (isset($_POST['allergen_color'])) {
        update_term_meta($term_id, 'allergen_color', sanitize_hex_color($_POST['allergen_color']));
    }
    
    if (isset($_POST['allergen_order'])) {
        update_term_meta($term_id, 'allergen_order', absint($_POST['allergen_order']));
    }
    
    if (isset($_POST['allergen_active'])) {
        update_term_meta($term_id, 'allergen_active', '1');
    } else {
        update_term_meta($term_id, 'allergen_active', '0');
    }
}

/**
 * Añadir columnas personalizadas a la lista de alérgenos
 */
add_filter('manage_edit-allergen_columns', 'lamusa_allergen_columns');
function lamusa_allergen_columns($columns) {
    $new_columns = array();
    $new_columns['cb'] = $columns['cb'];
    $new_columns['name'] = $columns['name'];
    $new_columns['allergen_icon'] = __('Icono', 'lamusa-core');
    $new_columns['allergen_color'] = __('Color', 'lamusa-core');
    $new_columns['allergen_order'] = __('Orden', 'lamusa-core');
    $new_columns['allergen_active'] = __('Activo', 'lamusa-core');
    $new_columns['description'] = $columns['description'];
    $new_columns['posts'] = $columns['posts'];
    
    return $new_columns;
}

/**
 * Rellenar las columnas personalizadas de alérgenos
 */
add_filter('manage_allergen_custom_column', 'lamusa_allergen_column_content', 10, 3);
function lamusa_allergen_column_content($content, $column_name, $term_id) {
    switch ($column_name) {
        case 'allergen_icon':
            $icon = get_term_meta($term_id, 'allergen_icon', true);
            if ($icon) {
                $content = '<span class="' . esc_attr($icon) . '"></span> ' . esc_html($icon);
            } else {
                $content = '—';
            }
            break;
            
        case 'allergen_color':
            $color = get_term_meta($term_id, 'allergen_color', true);
            if ($color) {
                $content = '<span style="display:inline-block;width:20px;height:20px;background-color:' . esc_attr($color) . ';border-radius:3px;margin-right:8px;"></span>' . esc_html($color);
            } else {
                $content = '—';
            }
            break;
            
        case 'allergen_order':
            $order = get_term_meta($term_id, 'allergen_order', true);
            $content = $order !== '' ? esc_html($order) : '0';
            break;
            
        case 'allergen_active':
            $active = get_term_meta($term_id, 'allergen_active', true);
            $content = $active == '1' ? '<span style="color:green;">✓ Sí</span>' : '<span style="color:red;">✗ No</span>';
            break;
    }
    
    return $content;
}

/**
 * Hacer las columnas ordenables
 */
add_filter('manage_edit-allergen_sortable_columns', 'lamusa_allergen_sortable_columns');
function lamusa_allergen_sortable_columns($columns) {
    $columns['allergen_order'] = 'allergen_order';
    $columns['allergen_active'] = 'allergen_active';
    return $columns;
}

/**
 * Función helper para obtener alérgenos activos ordenados
 */
function lamusa_get_active_allergens() {
    $allergens = get_terms(array(
        'taxonomy' => 'allergen',
        'hide_empty' => false,
        'meta_query' => array(
            array(
                'key' => 'allergen_active',
                'value' => '1',
                'compare' => '='
            )
        ),
        'meta_key' => 'allergen_order',
        'orderby' => 'meta_value_num',
        'order' => 'ASC'
    ));
    
    return $allergens;
}

/**
 * Crear alérgenos predeterminados al activar el plugin
 */
function lamusa_create_default_allergens() {
    $default_allergens = array(
        array(
            'name' => 'Gluten',
            'slug' => 'gluten',
            'description' => 'Cereales que contengan gluten',
            'icon' => 'dashicons-warning',
            'color' => '#F39C12',
            'order' => 1
        ),
        array(
            'name' => 'Crustáceos',
            'slug' => 'crustaceos',
            'description' => 'Crustáceos y productos a base de crustáceos',
            'icon' => 'dashicons-warning',
            'color' => '#E74C3C',
            'order' => 2
        ),
        array(
            'name' => 'Huevos',
            'slug' => 'huevos',
            'description' => 'Huevos y productos a base de huevo',
            'icon' => 'dashicons-warning',
            'color' => '#F1C40F',
            'order' => 3
        ),
        array(
            'name' => 'Pescado',
            'slug' => 'pescado',
            'description' => 'Pescado y productos a base de pescado',
            'icon' => 'dashicons-warning',
            'color' => '#3498DB',
            'order' => 4
        ),
        array(
            'name' => 'Cacahuetes',
            'slug' => 'cacahuetes',
            'description' => 'Cacahuetes y productos a base de cacahuetes',
            'icon' => 'dashicons-warning',
            'color' => '#D35400',
            'order' => 5
        ),
        array(
            'name' => 'Soja',
            'slug' => 'soja',
            'description' => 'Soja y productos a base de soja',
            'icon' => 'dashicons-warning',
            'color' => '#27AE60',
            'order' => 6
        ),
        array(
            'name' => 'Lácteos',
            'slug' => 'lacteos',
            'description' => 'Leche y sus derivados (incluida la lactosa)',
            'icon' => 'dashicons-warning',
            'color' => '#FFFFFF',
            'order' => 7
        ),
        array(
            'name' => 'Frutos de cáscara',
            'slug' => 'frutos-cascara',
            'description' => 'Frutos de cáscara (almendras, avellanas, nueces, etc.)',
            'icon' => 'dashicons-warning',
            'color' => '#8E44AD',
            'order' => 8
        ),
        array(
            'name' => 'Apio',
            'slug' => 'apio',
            'description' => 'Apio y productos derivados',
            'icon' => 'dashicons-warning',
            'color' => '#2ECC71',
            'order' => 9
        ),
        array(
            'name' => 'Mostaza',
            'slug' => 'mostaza',
            'description' => 'Mostaza y productos derivados',
            'icon' => 'dashicons-warning',
            'color' => '#F39C12',
            'order' => 10
        ),
        array(
            'name' => 'Granos de sésamo',
            'slug' => 'sesamo',
            'description' => 'Granos de sésamo y productos a base de granos de sésamo',
            'icon' => 'dashicons-warning',
            'color' => '#95A5A6',
            'order' => 11
        ),
        array(
            'name' => 'Dióxido de azufre y sulfitos',
            'slug' => 'sulfitos',
            'description' => 'Dióxido de azufre y sulfitos en concentraciones superiores a 10 mg/kg',
            'icon' => 'dashicons-warning',
            'color' => '#E67E22',
            'order' => 12
        ),
        array(
            'name' => 'Altramuces',
            'slug' => 'altramuces',
            'description' => 'Altramuces y productos a base de altramuces',
            'icon' => 'dashicons-warning',
            'color' => '#9B59B6',
            'order' => 13
        ),
        array(
            'name' => 'Moluscos',
            'slug' => 'moluscos',
            'description' => 'Moluscos y productos a base de moluscos',
            'icon' => 'dashicons-warning',
            'color' => '#34495E',
            'order' => 14
        )
    );
    
    foreach ($default_allergens as $allergen_data) {
        // Verificar si ya existe
        $existing_term = term_exists($allergen_data['slug'], 'allergen');
        
        if (!$existing_term) {
            $term = wp_insert_term(
                $allergen_data['name'],
                'allergen',
                array(
                    'slug' => $allergen_data['slug'],
                    'description' => $allergen_data['description']
                )
            );
            
            if (!is_wp_error($term)) {
                update_term_meta($term['term_id'], 'allergen_icon', $allergen_data['icon']);
                update_term_meta($term['term_id'], 'allergen_color', $allergen_data['color']);
                update_term_meta($term['term_id'], 'allergen_order', $allergen_data['order']);
                update_term_meta($term['term_id'], 'allergen_active', '1');
            }
        }
    }
}

// Ejecutar al activar el plugin
register_activation_hook(LAMUSA_CORE_PLUGIN_FILE, 'lamusa_create_default_allergens');
