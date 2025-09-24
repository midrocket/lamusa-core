<?php
/**
 * Registro de campos ACF
 */

// Prevenir acceso directo
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Registrar todos los grupos de campos ACF
 */
function lamusa_register_acf_fields() {
    if (!function_exists('acf_add_local_field_group')) {
        return;
    }
    
    lamusa_register_restaurant_fields();
    lamusa_register_weekly_menu_fields();
}

/**
 * Registrar campos para restaurantes
 * Migrados desde las opciones globales
 */
function lamusa_register_restaurant_fields() {
    acf_add_local_field_group(array(
        'key' => 'group_restaurant_details',
        'title' => 'Detalles del Restaurante',
        'fields' => array(
            array(
                'key' => 'field_restaurant_title',
                'label' => 'Título Principal',
                'name' => 'restaurant_title',
                'type' => 'text',
                'instructions' => 'Título principal del restaurante (ej: Limbo)',
                'required' => 1,
                'wrapper' => array(
                    'width' => '50',
                ),
                'placeholder' => 'Limbo',
            ),
            array(
                'key' => 'field_restaurant_title_secondary',
                'label' => 'Título Secundario',
                'name' => 'restaurant_title_secondary',
                'type' => 'text',
                'instructions' => 'Título secundario del restaurante (ej: Tribunal)',
                'required' => 0,
                'wrapper' => array(
                    'width' => '50',
                ),
                'placeholder' => 'Tribunal',
            ),
            array(
                'key' => 'field_restaurant_subtitle',
                'label' => 'Subtítulo Principal',
                'name' => 'restaurant_subtitle',
                'type' => 'text',
                'instructions' => 'Subtítulo principal del restaurante (ej: Pollo al fuego y buen)',
                'required' => 0,
                'wrapper' => array(
                    'width' => '50',
                ),
                'placeholder' => 'Pollo al fuego y buen',
            ),
            array(
                'key' => 'field_restaurant_subtitle_secondary',
                'label' => 'Subtítulo Secundario',
                'name' => 'restaurant_subtitle_secondary',
                'type' => 'text',
                'instructions' => 'Subtítulo secundario del restaurante (ej: HUMO)',
                'required' => 0,
                'wrapper' => array(
                    'width' => '50',
                ),
                'placeholder' => 'HUMO',
            ),
            array(
                'key' => 'field_restaurant_address',
                'label' => 'Dirección',
                'name' => 'address',
                'type' => 'text',
                'instructions' => 'Dirección completa del restaurante',
                'required' => 1,
                'wrapper' => array(
                    'width' => '50',
                ),
                'placeholder' => 'C. de San Mateo, 3, Centro, 28004 Madrid',
            ),
            array(
                'key' => 'field_restaurant_phone',
                'label' => 'Teléfono',
                'name' => 'phone',
                'type' => 'text',
                'instructions' => 'Número de teléfono de contacto',
                'required' => 1,
                'wrapper' => array(
                    'width' => '50',
                ),
                'placeholder' => '+34 919 01 47 60',
            ),
            array(
                'key' => 'field_restaurant_opening_hours',
                'label' => 'Horario',
                'name' => 'opening_hours',
                'type' => 'textarea',
                'instructions' => 'Horarios de apertura del restaurante',
                'required' => 0,
                'rows' => 5,
                'placeholder' => 'D-X: 8:00 – 0:30 J: 8:00 – 1:00 V-S: 8:00 – 2:00',
                'new_lines' => 'wpautop',
            ),
            array(
                'key' => 'field_restaurant_description',
                'label' => 'Descripción',
                'name' => 'description',
                'type' => 'wysiwyg',
                'instructions' => 'Descripción del restaurante para SEO y presentación',
                'required' => 0,
                'media_upload' => 1,
                'toolbar' => 'full',
            ),
            array(
                'key' => 'field_restaurant_urls',
                'label' => 'Enlaces y Menús',
                'name' => 'restaurant_urls',
                'type' => 'group',
                'layout' => 'block',
                'sub_fields' => array(
                    array(
                        'key' => 'field_restaurant_url_breakfast',
                        'label' => 'Desayunos/Brunch',
                        'name' => 'url_breakfast',
                        'type' => 'url',
                        'wrapper' => array('width' => '50'),
                    ),
                    array(
                        'key' => 'field_restaurant_url_menu',
                        'label' => 'Carta',
                        'name' => 'url_menu',
                        'type' => 'url',
                        'wrapper' => array('width' => '50'),
                    ),
                    array(
                        'key' => 'field_restaurant_url_drinks',
                        'label' => 'Bebidas',
                        'name' => 'url_drinks',
                        'type' => 'url',
                        'wrapper' => array('width' => '50'),
                    ),
                    array(
                        'key' => 'field_restaurant_url_cocktails',
                        'label' => 'Cócteles',
                        'name' => 'url_cocktails',
                        'type' => 'url',
                        'wrapper' => array('width' => '50'),
                    ),
                    array(
                        'key' => 'field_restaurant_url_dessert',
                        'label' => 'Postres',
                        'name' => 'url_dessert',
                        'type' => 'url',
                        'wrapper' => array('width' => '50'),
                    ),
                    array(
                        'key' => 'field_restaurant_url_dish_week',
                        'label' => 'Plato de la semana',
                        'name' => 'url_dish_week',
                        'type' => 'url',
                        'wrapper' => array('width' => '50'),
                    ),
                    array(
                        'key' => 'field_restaurant_url_take_away',
                        'label' => 'Take away',
                        'name' => 'url_take_away',
                        'type' => 'url',
                        'wrapper' => array('width' => '50'),
                    ),
                    array(
                        'key' => 'field_restaurant_url_booking',
                        'label' => 'Reserva',
                        'name' => 'url_booking',
                        'type' => 'url',
                        'wrapper' => array('width' => '50'),
                    ),
                    array(
                        'key' => 'field_restaurant_url_nights',
                        'label' => 'Noches/Eventos',
                        'name' => 'url_nights',
                        'type' => 'url',
                        'wrapper' => array('width' => '50'),
                    ),
                    array(
                        'key' => 'field_restaurant_url_djs_program',
                        'label' => 'DJ\'s & Program',
                        'name' => 'url_djs_program',
                        'type' => 'url',
                        'wrapper' => array('width' => '50'),
                    ),
                    array(
                        'key' => 'field_restaurant_url_about',
                        'label' => 'About',
                        'name' => 'url_about',
                        'type' => 'url',
                        'wrapper' => array('width' => '50'),
                    ),
                ),
            ),
            array(
                'key' => 'field_restaurant_settings',
                'label' => 'Configuración',
                'name' => 'restaurant_settings',
                'type' => 'group',
                'layout' => 'block',
                'sub_fields' => array(
                    array(
                        'key' => 'field_restaurant_show_weekly_menu',
                        'label' => 'Mostrar Menú Semanal',
                        'name' => 'show_weekly_menu',
                        'type' => 'true_false',
                        'instructions' => 'Marcar si este restaurante tiene menús semanales',
                        'default_value' => 1,
                        'ui' => 1,
                        'wrapper' => array('width' => '50'),
                    ),
                    array(
                        'key' => 'field_restaurant_featured',
                        'label' => 'Restaurante Destacado',
                        'name' => 'featured',
                        'type' => 'true_false',
                        'instructions' => 'Marcar si es un restaurante destacado',
                        'default_value' => 0,
                        'ui' => 1,
                        'wrapper' => array('width' => '50'),
                    ),
                ),
            ),
        ),
        'location' => array(
            array(
                array(
                    'param' => 'post_type',
                    'operator' => '==',
                    'value' => 'restaurant',
                ),
            ),
        ),
        'menu_order' => 0,
        'position' => 'normal',
        'style' => 'default',
        'label_placement' => 'top',
        'instruction_placement' => 'label',
        'active' => true,
        'description' => 'Campos para la gestión de información de restaurantes',
    ));
}

/**
 * Registrar campos para menús semanales
 */
function lamusa_register_weekly_menu_fields() {
    acf_add_local_field_group(array(
        'key' => 'group_weekly_menu_details',
        'title' => 'Configuración del Menú',
        'fields' => array(
            array(
                'key' => 'field_menu_tab',
                'label' => 'Configuración del Menú',
                'name' => '',
                'type' => 'tab',
                'instructions' => '',
                'required' => 0,
                'placement' => 'top',
                'endpoint' => 0,
            ),
            array(
                'key' => 'field_weekly_menu_restaurant',
                'label' => 'Restaurante',
                'name' => 'restaurant',
                'type' => 'post_object',
                'instructions' => 'Selecciona el restaurante al que pertenece este menú',
                'required' => 1,
                'post_type' => array('restaurant'),
                'taxonomy' => '',
                'allow_null' => 0,
                'multiple' => 0,
                'return_format' => 'object',
                'ui' => 1,
                'wrapper' => array('width' => '33'),
            ),
            array(
                'key' => 'field_weekly_menu_start_date',
                'label' => 'Fecha de Inicio',
                'name' => 'start_date',
                'type' => 'date_picker',
                'instructions' => 'Primer día de validez del menú',
                'required' => 1,
                'display_format' => 'd/m/Y',
                'return_format' => 'Y-m-d',
                'first_day' => 1, // Lunes
                'wrapper' => array('width' => '33'),
            ),
            array(
                'key' => 'field_weekly_menu_end_date',
                'label' => 'Fecha de Fin',
                'name' => 'end_date',
                'type' => 'date_picker',
                'instructions' => 'Último día de validez del menú',
                'required' => 1,
                'display_format' => 'd/m/Y',
                'return_format' => 'Y-m-d',
                'first_day' => 1, // Lunes
                'wrapper' => array('width' => '33'),
            ),
            array(
                'key' => 'field_weekly_menu_price',
                'label' => 'Precio del Menú',
                'name' => 'menu_price',
                'type' => 'text',
                'instructions' => 'Precio del menú completo (ej: 16,50€)',
                'required' => 0,
                'placeholder' => '16,50€',
                'wrapper' => array('width' => '50'),
            ),
            array(
                'key' => 'field_weekly_menu_active',
                'label' => 'Menú Activo',
                'name' => 'menu_active',
                'type' => 'true_false',
                'instructions' => 'Marcar si el menú está activo y debe mostrarse',
                'default_value' => 1,
                'ui' => 1,
                'wrapper' => array('width' => '50'),
            ),
            array(
                'key' => 'field_weekly_menu_introduction',
                'label' => 'Introducción al Menú',
                'name' => 'menu_introduction',
                'type' => 'wysiwyg',
                'instructions' => 'Texto introductorio para el menú semanal',
                'required' => 0,
                'toolbar' => 'basic',
                'media_upload' => 0,
            ),
            array(
                'key' => 'field_weekly_menu_days',
                'label' => 'Días del Menú',
                'name' => 'menu_days',
                'type' => 'repeater',
                'instructions' => 'Configura el menú para cada día de la semana',
                'required' => 0,
                'layout' => 'block',
                'button_label' => 'Añadir Día',
                'sub_fields' => array(
                    array(
                        'key' => 'field_menu_day_name',
                        'label' => 'Día de la Semana',
                        'name' => 'day_name',
                        'type' => 'select',
                        'required' => 1,
                        'choices' => array(
                            'lunes' => 'Lunes',
                            'martes' => 'Martes',
                            'miercoles' => 'Miércoles',
                            'jueves' => 'Jueves',
                            'viernes' => 'Viernes',
                            'sabado' => 'Sábado',
                            'domingo' => 'Domingo',
                        ),
                        'default_value' => 'lunes',
                        'ui' => 1,
                        'wrapper' => array('width' => '25'),
                    ),
                    array(
                        'key' => 'field_menu_day_date',
                        'label' => 'Fecha Específica',
                        'name' => 'day_date',
                        'type' => 'date_picker',
                        'instructions' => 'Fecha específica para este día (opcional)',
                        'required' => 0,
                        'display_format' => 'd/m/Y',
                        'return_format' => 'Y-m-d',
                        'wrapper' => array('width' => '25'),
                    ),
                    array(
                        'key' => 'field_menu_day_active',
                        'label' => 'Día Activo',
                        'name' => 'day_active',
                        'type' => 'true_false',
                        'instructions' => 'Marcar si hay menú este día',
                        'default_value' => 1,
                        'ui' => 1,
                        'wrapper' => array('width' => '25'),
                    ),
                    array(
                        'key' => 'field_menu_day_special_note',
                        'label' => 'Nota Especial',
                        'name' => 'special_note',
                        'type' => 'text',
                        'instructions' => 'Nota especial para este día (ej: "Cerrado", "Solo cena")',
                        'required' => 0,
                        'wrapper' => array('width' => '25'),
                    ),
                    array(
                        'key' => 'field_menu_day_content',
                        'label' => 'Contenido del Menú',
                        'name' => 'menu_content',
                        'type' => 'wysiwyg',
                        'instructions' => 'Contenido completo del menú para este día',
                        'required' => 0,
                        'toolbar' => 'full',
                        'media_upload' => 1,
                    ),
                ),
            ),
            array(
                'key' => 'field_allergen_tab',
                'label' => 'Gestión de Alérgenos',
                'name' => '',
                'type' => 'tab',
                'instructions' => '',
                'required' => 0,
                'placement' => 'top',
                'endpoint' => 0,
            ),
            array(
                'key' => 'field_menu_allergen_groups',
                'label' => 'Grupos de Platos con Alérgenos',
                'name' => 'allergen_groups',
                'type' => 'repeater',
                'instructions' => 'Organiza los platos por grupos (Primeros, Segundos, Postres, etc.) y gestiona sus alérgenos',
                'required' => 0,
                'layout' => 'block',
                'button_label' => 'Añadir Grupo de Platos',
                'sub_fields' => array(
                    array(
                        'key' => 'field_allergen_group_title',
                        'label' => 'Título del Grupo',
                        'name' => 'group_title',
                        'type' => 'text',
                        'instructions' => 'Nombre del grupo de platos (ej: Primeros Platos, Segundos Platos, Postres)',
                        'required' => 1,
                        'placeholder' => 'Primeros Platos',
                        'wrapper' => array('width' => '70'),
                    ),
                    array(
                        'key' => 'field_allergen_group_order',
                        'label' => 'Orden',
                        'name' => 'group_order',
                        'type' => 'number',
                        'instructions' => 'Orden de visualización',
                        'required' => 0,
                        'default_value' => 1,
                        'min' => 1,
                        'max' => 100,
                        'wrapper' => array('width' => '30'),
                    ),
                    array(
                        'key' => 'field_allergen_group_dishes',
                        'label' => 'Platos del Grupo',
                        'name' => 'dishes',
                        'type' => 'repeater',
                        'instructions' => 'Lista de platos en este grupo con sus alérgenos',
                        'required' => 0,
                        'layout' => 'block',
                        'button_label' => 'Añadir Plato',
                        'sub_fields' => array(
                            array(
                                'key' => 'field_dish_name',
                                'label' => 'Nombre del Plato',
                                'name' => 'dish_name',
                                'type' => 'text',
                                'required' => 1,
                                'placeholder' => 'Ensalada Malagueña con Bacalao',
                                'wrapper' => array('width' => '100'),
                            ),
                            array(
                                'key' => 'field_dish_description',
                                'label' => 'Descripción',
                                'name' => 'dish_description',
                                'type' => 'textarea',
                                'required' => 0,
                                'rows' => 2,
                                'placeholder' => 'Breve descripción del plato y sus ingredientes',
                            ),
                            array(
                                'key' => 'field_dish_allergens_message',
                                'label' => 'Selección de Alérgenos',
                                'name' => 'allergens_message',
                                'type' => 'message',
                                'message' => 'Selecciona los alérgenos correspondientes. <strong>CONTIENE</strong> significa que el plato definitivamente tiene ese alérgeno. <strong>PUEDE CONTENER TRAZAS</strong> significa que puede haber contaminación cruzada.',
                                'new_lines' => 'wpautop',
                                'esc_html' => 0,
                            ),
                            array(
                                'key' => 'field_dish_allergens_contains',
                                'label' => 'CONTIENE Alérgenos',
                                'name' => 'allergens_contains',
                                'type' => 'checkbox',
                                'instructions' => 'Marca los alérgenos que este plato CONTIENE definitivamente',
                                'required' => 0,
                                'choices' => array(
                                    // Opciones básicas como fallback
                                    'loading' => 'Cargando alérgenos...'
                                ),
                                'layout' => 'horizontal',
                                'toggle' => 0,
                                'return_format' => 'array',
                                'allow_custom' => 0,
                            ),
                            array(
                                'key' => 'field_dish_allergens_traces',
                                'label' => 'PUEDE CONTENER TRAZAS',
                                'name' => 'allergens_traces',
                                'type' => 'checkbox',
                                'instructions' => 'Marca los alérgenos de los que este plato PUEDE CONTENER TRAZAS',
                                'required' => 0,
                                'choices' => array(
                                    // Opciones básicas como fallback
                                    'loading' => 'Cargando alérgenos...'
                                ),
                                'layout' => 'horizontal',
                                'toggle' => 0,
                                'return_format' => 'array',
                                'allow_custom' => 0,
                            ),
                        ),
                    ),
                ),
            ),
        ),
        'location' => array(
            array(
                array(
                    'param' => 'post_type',
                    'operator' => '==',
                    'value' => 'weekly_menu',
                ),
            ),
        ),
        'menu_order' => 0,
        'position' => 'normal',
        'style' => 'default',
        'label_placement' => 'top',
        'instruction_placement' => 'label',
        'active' => true,
        'description' => 'Campos para la gestión de menús semanales',
    ));
}


/**
 * Validación personalizada para fechas de menús semanales
 */
add_filter('acf/validate_value/name=end_date', 'lamusa_validate_menu_dates', 10, 4);
function lamusa_validate_menu_dates($valid, $value, $field, $input) {
    if (!$valid) {
        return $valid;
    }
    
    $start_date = $_POST['acf']['field_weekly_menu_start_date'] ?? '';
    $restaurant_id = $_POST['acf']['field_weekly_menu_restaurant'] ?? '';
    $post_id = $_POST['post_ID'] ?? 0;
    
    if ($start_date && $value && $restaurant_id) {
        // Verificar que la fecha de fin es posterior a la de inicio
        if (strtotime($value) <= strtotime($start_date)) {
            $valid = 'La fecha de fin debe ser posterior a la fecha de inicio.';
            return $valid;
        }
        
        // Verificar solapamientos con otros menús del mismo restaurante
        $conflicting_menus = get_posts(array(
            'post_type' => 'weekly_menu',
            'post_status' => 'publish',
            'posts_per_page' => -1,
            'exclude' => array($post_id),
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
                    array(
                        'relation' => 'AND',
                        array(
                            'key' => 'start_date',
                            'value' => $value,
                            'compare' => '<='
                        ),
                        array(
                            'key' => 'end_date',
                            'value' => $value,
                            'compare' => '>='
                        )
                    ),
                    array(
                        'relation' => 'AND',
                        array(
                            'key' => 'start_date',
                            'value' => $start_date,
                            'compare' => '>='
                        ),
                        array(
                            'key' => 'end_date',
                            'value' => $value,
                            'compare' => '<='
                        )
                    )
                )
            )
        ));
        
        if (!empty($conflicting_menus)) {
            $conflicting_menu = $conflicting_menus[0];
            $conflict_start = get_field('start_date', $conflicting_menu->ID);
            $conflict_end = get_field('end_date', $conflicting_menu->ID);
            
            $valid = sprintf(
                'Las fechas se solapan con el menú "%s" (%s - %s). Por favor, elige fechas diferentes.',
                $conflicting_menu->post_title,
                date('d/m/Y', strtotime($conflict_start)),
                date('d/m/Y', strtotime($conflict_end))
            );
        }
    }
    
    return $valid;
}

/**
 * Crear datos de ejemplo para los restaurantes existentes
 */
function lamusa_create_example_restaurants() {
    $example_restaurants = array(
        'Limbo Tribunal' => array(
            'restaurant_title' => 'Limbo',
            'restaurant_title_secondary' => 'Tribunal',
            'restaurant_subtitle' => 'Pollo al fuego y buen',
            'restaurant_subtitle_secondary' => 'HUMO',
            'address' => 'C. de San Mateo, 3, Centro, 28004 Madrid',
            'phone' => '+34 919 01 47 60',
            'opening_hours' => 'D-X: 8:00 – 0:30<br>J: 8:00 – 1:00<br>V-S: 8:00 – 2:00',
        ),
        'Ojalá Beach Bar' => array(
            'restaurant_title' => 'Ojalá',
            'restaurant_title_secondary' => 'Beach',
            'restaurant_subtitle' => 'Playa urbana y copas',
            'restaurant_subtitle_secondary' => 'ARENA',
            'address' => 'C. de San Andrés, 1, Malasaña, 28004 Madrid',
            'phone' => '+34 919 01 47 60',
            'opening_hours' => 'D-X: 9:00 – 1:30<br>J: 9:00 – 2:00<br>V-S: 9:00 – 3:00',
        ),
        'Ojalá Pool Bar' => array(
            'restaurant_title' => 'Ojalá',
            'restaurant_title_secondary' => 'Pool',
            'restaurant_subtitle' => 'Piscina y cócteles',
            'restaurant_subtitle_secondary' => 'AGUA',
            'address' => 'C. de San Andrés, 1, Malasaña, 28004 Madrid',
            'phone' => '+34 919 01 47 60',
            'opening_hours' => 'L-D: 16:00 – 22:00',
        ),
        'Cafetería HD' => array(
            'restaurant_title' => 'HD',
            'restaurant_title_secondary' => 'La Musa',
            'restaurant_subtitle' => 'Café de especialidad',
            'restaurant_subtitle_secondary' => 'BEANS',
            'address' => 'C. de Hortaleza, 108, Centro, 28004 Madrid',
            'phone' => '+34 919 01 47 60',
            'opening_hours' => 'L-D: 8:00 – 22:00',
        ),
        'Musa Malasaña' => array(
            'restaurant_title' => 'La Musa',
            'restaurant_title_secondary' => 'Malasaña',
            'restaurant_subtitle' => 'Cocina mediterránea moderna',
            'restaurant_subtitle_secondary' => 'TRADICIÓN',
            'address' => 'C. Manuela Malasaña, 18, Malasaña, 28004 Madrid',
            'phone' => '+34 919 01 47 60',
            'opening_hours' => 'D-X: 9:00 – 1:30<br>J: 9:00 – 2:00<br>V-S: 9:00 – 3:00',
        ),
        'Musa Latina' => array(
            'restaurant_title' => 'La Musa',
            'restaurant_title_secondary' => 'Latina',
            'restaurant_subtitle' => 'Sabores del barrio',
            'restaurant_subtitle_secondary' => 'RAÍCES',
            'address' => 'Costanilla de San Andrés, 12, La Latina, 28005 Madrid',
            'phone' => '+34 913 54 02 55',
            'opening_hours' => 'L-D: 13:00 – 1:30',
        ),
        'Run, Run, Run' => array(
            'restaurant_title' => 'Run',
            'restaurant_title_secondary' => 'Run, Run',
            'restaurant_subtitle' => 'Urban kitchen experience',
            'restaurant_subtitle_secondary' => 'VELOCIDAD',
            'address' => 'C. de la Cava Baja, 8, La Latina, 28005 Madrid',
            'phone' => '+34 913 65 42 79',
            'opening_hours' => 'L-D: 20:00 – 2:00',
        ),
        'Sound Baths' => array(
            'restaurant_title' => 'Sound',
            'restaurant_title_secondary' => 'Baths',
            'restaurant_subtitle' => 'Wellness & music experience',
            'restaurant_subtitle_secondary' => 'VIBRAS',
            'address' => 'C. de San Andrés, 1, Malasaña, 28004 Madrid',
            'phone' => '+34 919 01 47 60',
            'opening_hours' => 'L-V: 19:00 – 22:00',
        ),
        'Down Stairs' => array(
            'restaurant_title' => 'Down',
            'restaurant_title_secondary' => 'Stairs',
            'restaurant_subtitle' => 'Underground bar culture',
            'restaurant_subtitle_secondary' => 'DEPTHS',
            'address' => 'C. de San Andrés, 1, Malasaña, 28004 Madrid',
            'phone' => '+34 919 01 47 60',
            'opening_hours' => 'X-S: 22:00 – 3:00',
        ),
    );
    
    foreach ($example_restaurants as $internal_name => $data) {
        // Verificar si ya existe
        $existing = get_posts(array(
            'post_type' => 'restaurant',
            'title' => $internal_name,
            'post_status' => 'any',
            'numberposts' => 1
        ));
        
        if (empty($existing)) {
            $post_id = wp_insert_post(array(
                'post_title' => $internal_name . ' (Gestión)', // Título interno más descriptivo
                'post_type' => 'restaurant',
                'post_status' => 'publish',
                'post_content' => 'Restaurante del grupo La Musa.',
            ));
            
            if ($post_id && !is_wp_error($post_id)) {
                update_field('restaurant_title', $data['restaurant_title'], $post_id);
                update_field('restaurant_title_secondary', $data['restaurant_title_secondary'], $post_id);
                update_field('restaurant_subtitle', $data['restaurant_subtitle'], $post_id);
                update_field('restaurant_subtitle_secondary', $data['restaurant_subtitle_secondary'], $post_id);
                update_field('address', $data['address'], $post_id);
                update_field('phone', $data['phone'], $post_id);
                update_field('opening_hours', $data['opening_hours'], $post_id);
                update_field('show_weekly_menu', true, $post_id);
                
                // Crear un menú semanal de ejemplo para este restaurante
                lamusa_create_example_weekly_menu($post_id);
            }
        }
    }
}

/**
 * Crear un menú semanal de ejemplo para un restaurante
 */
function lamusa_create_example_weekly_menu($restaurant_id) {
    // Verificar si ya existe un menú para este restaurante
    $existing_menu = get_posts(array(
        'post_type' => 'weekly_menu',
        'post_status' => 'any',
        'posts_per_page' => 1,
        'meta_query' => array(
            array(
                'key' => 'restaurant',
                'value' => $restaurant_id,
                'compare' => '='
            )
        )
    ));
    
    // Si ya existe, no crear otro
    if (!empty($existing_menu)) {
        return;
    }
    
    $restaurant = get_post($restaurant_id);
    if (!$restaurant) {
        return;
    }
    
    // Crear menú semanal
    $menu_title = 'Menú Semanal - ' . $restaurant->post_title;
    
    $menu_id = wp_insert_post(array(
        'post_title' => $menu_title,
        'post_type' => 'weekly_menu',
        'post_status' => 'publish',
        'post_content' => 'Menú semanal para ' . $restaurant->post_title,
    ));
    
    if ($menu_id && !is_wp_error($menu_id)) {
        // Configurar fechas (semana actual)
        $today = current_time('Y-m-d');
        $monday = date('Y-m-d', strtotime('monday this week', strtotime($today)));
        $friday = date('Y-m-d', strtotime('friday this week', strtotime($today)));
        
        // Campos básicos del menú
        update_field('restaurant', $restaurant_id, $menu_id);
        update_field('start_date', $monday, $menu_id);
        update_field('end_date', $friday, $menu_id);
        update_field('menu_price', '16,50€', $menu_id);
        update_field('menu_active', 1, $menu_id);
        update_field('menu_introduction', 'Primero + Segundo + Postre o café, pan y bebida incluidos / De lunes a viernes de 13.00h a 17.00h, excepto festivos', $menu_id);
        
        // Crear días de ejemplo
        $example_days = array(
            array(
                'day_name' => 'lunes',
                'day_active' => true,
                'menu_content' => '<h3>PRIMEROS</h3><p>Ensalada Malagueña con Bacalao confitado, Naranja en rodajas y Aceitunas Negras</p><p>Crema templada de remolacha con queso feta</p><h3>SEGUNDOS</h3><p>Espaguetis Nero Di sepia con Chipirones Estofados</p><p>Milanesa de Chuleta de Cerdo a la Napolitana acompañada de Coleslaw</p><h3>POSTRES</h3><p>Panna Cotta de Frutos Rojos, Mousse de chocolate, pudding de croissant</p>'
            ),
            array(
                'day_name' => 'martes',
                'day_active' => true,
                'menu_content' => '<h3>PRIMEROS</h3><p>Gazpacho andaluz con aceite de oliva virgen extra</p><p>Ensalada de quinoa con verduras de temporada</p><h3>SEGUNDOS</h3><p>Salmón a la plancha con verduras al vapor</p><p>Pollo al curry con arroz basmati</p><h3>POSTRES</h3><p>Tiramisú casero o fruta de temporada</p>'
            ),
            array(
                'day_name' => 'miercoles',
                'day_active' => true,
                'menu_content' => '<h3>PRIMEROS</h3><p>Sopa de tomate con albahaca fresca</p><p>Hummus casero con crudités</p><h3>SEGUNDOS</h3><p>Paella valenciana tradicional</p><p>Hamburguesa gourmet con patatas</p><h3>POSTRES</h3><p>Tarta de queso o helado artesanal</p>'
            ),
            array(
                'day_name' => 'jueves',
                'day_active' => true,
                'menu_content' => '<h3>PRIMEROS</h3><p>Carpaccio de ternera con rúcula y parmesano</p><p>Crema de calabaza con semillas tostadas</p><h3>SEGUNDOS</h3><p>Lubina al horno con patatas panadera</p><p>Risotto de setas con trufa</p><h3>POSTRES</h3><p>Brownie de chocolate con helado de vainilla</p>'
            ),
            array(
                'day_name' => 'viernes',
                'day_active' => true,
                'menu_content' => '<h3>PRIMEROS</h3><p>Tartar de atún con aguacate</p><p>Ensalada César con pollo</p><h3>SEGUNDOS</h3><p>Cordero lechal asado con hierbas</p><p>Pasta fresca con salsa de tomate y albahaca</p><h3>POSTRES</h3><p>Crème brûlée o sorbete de limón</p>'
            )
        );
        
        update_field('menu_days', $example_days, $menu_id);
    }
}

// Ejecutar al activar el plugin
register_activation_hook(LAMUSA_CORE_PLUGIN_FILE, 'lamusa_create_example_restaurants');

/**
 * Crear alérgenos por defecto si no existen
 */
function lamusa_create_default_allergens_if_missing() {
    // Verificar si ya existen alérgenos
    $existing_allergens = get_terms(array(
        'taxonomy' => 'allergen',
        'hide_empty' => false,
        'count' => true
    ));
    
    // Si ya hay alérgenos, no hacer nada
    if ($existing_allergens && $existing_allergens > 0) {
        return;
    }
    
    // Incluir la función de creación de alérgenos desde taxonomies.php
    if (function_exists('lamusa_create_default_allergens')) {
        lamusa_create_default_allergens();
    }
}

/**
 * Ejecutar creación de alérgenos en init para asegurar que existan
 */
add_action('init', 'lamusa_ensure_allergens_exist', 20);
function lamusa_ensure_allergens_exist() {
    // Solo ejecutar en admin y si la taxonomía está registrada
    if (is_admin() && taxonomy_exists('allergen')) {
        lamusa_create_default_allergens_if_missing();
    }
}

/**
 * Poblar dinámicamente las opciones de alérgenos con iconos
 */
add_filter('acf/load_field/name=allergens_contains', 'lamusa_load_allergen_choices');
add_filter('acf/load_field/name=allergens_traces', 'lamusa_load_allergen_choices');
function lamusa_load_allergen_choices($field) {
    // Debug: log que se está ejecutando la función
    error_log('La Musa Core: Cargando opciones de alérgenos para campo: ' . $field['name']);
    
    // Limpiar las opciones de "loading"
    if (isset($field['choices']['loading'])) {
        unset($field['choices']['loading']);
    }
    
    // Obtener todos los alérgenos sin filtros primero
    $allergens = get_terms(array(
        'taxonomy' => 'allergen',
        'hide_empty' => false,
        'orderby' => 'name',
        'order' => 'ASC'
    ));
    
    error_log('La Musa Core: Encontrados ' . (is_array($allergens) ? count($allergens) : '0') . ' alérgenos');
    
    if ($allergens && !is_wp_error($allergens) && count($allergens) > 0) {
        foreach ($allergens as $allergen) {
            // Por ahora, incluir todos los alérgenos independientemente del estado "activo"
            // para debug
            $icon = get_term_meta($allergen->term_id, 'allergen_icon', true);
            $color = get_term_meta($allergen->term_id, 'allergen_color', true);
            $active = get_term_meta($allergen->term_id, 'allergen_active', true);
            
            error_log('La Musa Core: Alérgeno ' . $allergen->name . ' - Activo: ' . $active . ' - Icono: ' . $icon);
            
            // Crear label básico primero
            $label = esc_html($allergen->name);
            
            // Si tiene icono, añadirlo
            if ($icon) {
                $color = $color ?: '#333333';
                $label = '<span class="allergen-choice-item" style="color: ' . esc_attr($color) . ';">';
                $label .= '<i class="' . esc_attr($icon) . '"></i> ';
                $label .= esc_html($allergen->name);
                $label .= '</span>';
            }
            
            $field['choices'][$allergen->term_id] = $label;
        }
    } else {
        error_log('La Musa Core: No se encontraron alérgenos, intentando crear por defecto');
        
        // Intentar crear alérgenos por defecto
        lamusa_create_default_allergens_if_missing();
        
        // Intentar obtenerlos de nuevo
        $allergens = get_terms(array(
            'taxonomy' => 'allergen',
            'hide_empty' => false
        ));
        
        if ($allergens && !is_wp_error($allergens)) {
            foreach ($allergens as $allergen) {
                $icon = get_term_meta($allergen->term_id, 'allergen_icon', true);
                $color = get_term_meta($allergen->term_id, 'allergen_color', true) ?: '#333333';
                
                $label = esc_html($allergen->name);
                if ($icon) {
                    $label = '<span class="allergen-choice-item" style="color: ' . esc_attr($color) . ';">';
                    $label .= '<i class="' . esc_attr($icon) . '"></i> ';
                    $label .= esc_html($allergen->name);
                    $label .= '</span>';
                }
                
                $field['choices'][$allergen->term_id] = $label;
            }
        }
    }
    
    // Si después de todo aún no hay opciones, añadir mensaje de ayuda
    if (empty($field['choices'])) {
        $field['choices']['help'] = '⚠️ No hay alérgenos disponibles. <a href="' . admin_url('edit-tags.php?taxonomy=allergen&post_type=weekly_menu') . '" target="_blank">Crear alérgenos</a>';
        error_log('La Musa Core: No se pudieron cargar alérgenos después de todos los intentos');
    }
    
    error_log('La Musa Core: Opciones finales del campo: ' . count($field['choices']));
    
    return $field;
}

/**
 * Añadir CSS personalizado para los checkboxes de alérgenos en el admin
 */
add_action('acf/input/admin_head', 'lamusa_allergen_admin_css');
function lamusa_allergen_admin_css() {
    global $post_type;
    
    if ($post_type === 'weekly_menu') {
        ?>
        <style>
        .acf-field[data-name="allergens_contains"] .acf-checkbox-list,
        .acf-field[data-name="allergens_traces"] .acf-checkbox-list {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 10px;
            margin-top: 10px;
        }
        
        .acf-field[data-name="allergens_contains"] .acf-checkbox-list li,
        .acf-field[data-name="allergens_traces"] .acf-checkbox-list li {
            margin: 0;
            padding: 8px 12px;
            border: 2px solid #e1e5e9;
            border-radius: 6px;
            background: #f8f9fa;
            transition: all 0.2s ease;
            cursor: pointer;
        }
        
        .acf-field[data-name="allergens_contains"] .acf-checkbox-list li:hover,
        .acf-field[data-name="allergens_traces"] .acf-checkbox-list li:hover {
            border-color: #007cba;
            background: #f0f8ff;
        }
        
        .acf-field[data-name="allergens_contains"] .acf-checkbox-list li.selected,
        .acf-field[data-name="allergens_traces"] .acf-checkbox-list li.selected {
            border-color: #00a32a;
            background: #f0fff4;
        }
        
        /* Estilo específico para "contiene" vs "trazas" */
        .acf-field[data-name="allergens_contains"] .acf-checkbox-list li.selected {
            border-color: #d63638;
            background: #fff5f5;
        }
        
        .acf-field[data-name="allergens_traces"] .acf-checkbox-list li.selected {
            border-color: #dba617;
            background: #fffbf0;
        }
        
        .acf-field[data-name="allergens_contains"] .acf-checkbox-list label,
        .acf-field[data-name="allergens_traces"] .acf-checkbox-list label {
            display: flex;
            align-items: center;
            font-weight: 500;
            cursor: pointer;
            margin: 0;
        }
        
        .acf-field[data-name="allergens_contains"] .acf-checkbox-list input[type="checkbox"],
        .acf-field[data-name="allergens_traces"] .acf-checkbox-list input[type="checkbox"] {
            margin-right: 8px;
            transform: scale(1.2);
        }
        
        .allergen-choice-item {
            display: flex;
            align-items: center;
            font-size: 14px;
        }
        
        .allergen-choice-item i {
            font-size: 16px;
            margin-right: 6px;
        }
        
        /* Mensaje explicativo más visible */
        .acf-field[data-name="allergens_message"] .acf-input {
            background: #e7f3ff;
            border: 1px solid #72aee6;
            border-radius: 4px;
            padding: 12px;
            margin-bottom: 15px;
        }
        
        .acf-field[data-name="allergens_message"] .acf-input p {
            margin: 0;
            color: #1e1e1e;
            font-size: 13px;
            line-height: 1.4;
        }
        
        /* Títulos de sección más destacados */
        .acf-field[data-name="allergens_contains"] .acf-label,
        .acf-field[data-name="allergens_traces"] .acf-label {
            background: #f0f0f0;
            padding: 8px 12px;
            border-radius: 4px;
            border-left: 4px solid #007cba;
            margin-bottom: 10px;
        }
        
        .acf-field[data-name="allergens_contains"] .acf-label {
            border-left-color: #d63638;
        }
        
        .acf-field[data-name="allergens_traces"] .acf-label {
            border-left-color: #dba617;
        }
        
        .acf-field[data-name="allergens_contains"] .acf-label label,
        .acf-field[data-name="allergens_traces"] .acf-label label {
            font-weight: 600;
            font-size: 14px;
            margin: 0;
        }
        
        /* Responsive */
        @media (max-width: 768px) {
            .acf-field[data-name="allergens_contains"] .acf-checkbox-list,
            .acf-field[data-name="allergens_traces"] .acf-checkbox-list {
                grid-template-columns: 1fr;
            }
        }
        </style>
        
        <script>
        jQuery(document).ready(function($) {
            // Añadir clase 'selected' a los checkboxes marcados
            function updateSelectedState() {
                $('.acf-field[data-name="allergens_contains"] .acf-checkbox-list li, .acf-field[data-name="allergens_traces"] .acf-checkbox-list li').each(function() {
                    var $li = $(this);
                    var $checkbox = $li.find('input[type="checkbox"]');
                    
                    if ($checkbox.is(':checked')) {
                        $li.addClass('selected');
                    } else {
                        $li.removeClass('selected');
                    }
                });
            }
            
            // Ejecutar al cargar
            updateSelectedState();
            
            // Ejecutar cuando cambie el estado de los checkboxes
            $(document).on('change', '.acf-field[data-name="allergens_contains"] input[type="checkbox"], .acf-field[data-name="allergens_traces"] input[type="checkbox"]', function() {
                updateSelectedState();
            });
            
            // Hacer clickeable toda la celda
            $(document).on('click', '.acf-field[data-name="allergens_contains"] .acf-checkbox-list li, .acf-field[data-name="allergens_traces"] .acf-checkbox-list li', function(e) {
                if (e.target.type !== 'checkbox') {
                    var $checkbox = $(this).find('input[type="checkbox"]');
                    $checkbox.prop('checked', !$checkbox.prop('checked')).trigger('change');
                }
            });
        });
        </script>
        <?php
    }
}
