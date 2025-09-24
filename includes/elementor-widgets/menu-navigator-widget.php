<?php
/**
 * Widget Navegador de Menús para Elementor
 */

if (!defined('ABSPATH')) {
    exit;
}

class LaMusaMenuNavigatorWidget extends \Elementor\Widget_Base {

    public function get_name() {
        return 'lamusa_menu_navigator';
    }

    public function get_title() {
        return __('Navegador de Menús', 'lamusa-core');
    }

    public function get_icon() {
        return 'eicon-nav-menu';
    }

    public function get_categories() {
        return ['lamusa-core'];
    }

    public function get_keywords() {
        return ['menu', 'navigator', 'restaurant', 'lamusa', 'navigation'];
    }

    public function get_script_depends() {
        return ['lamusa-menu-navigator'];
    }

    public function get_style_depends() {
        return ['lamusa-menu-navigator'];
    }

    protected function _register_controls() {
        
        // Sección de configuración
        $this->start_controls_section(
            'content_section',
            [
                'label' => __('Configuración', 'lamusa-core'),
                'tab' => \Elementor\Controls_Manager::TAB_CONTENT,
            ]
        );

        $this->add_control(
            'default_restaurant',
            [
                'label' => __('Restaurante por Defecto', 'lamusa-core'),
                'type' => \Elementor\Controls_Manager::SELECT,
                'options' => $this->get_restaurant_options(),
                'default' => '',
                'description' => __('Restaurante que se mostrará al cargar la página', 'lamusa-core'),
            ]
        );

        $this->add_control(
            'restaurants_to_show',
            [
                'label' => __('Restaurantes a Mostrar', 'lamusa-core'),
                'type' => \Elementor\Controls_Manager::SELECT2,
                'multiple' => true,
                'options' => $this->get_restaurant_options(),
                'default' => [],
                'description' => __('Deja vacío para mostrar todos los restaurantes', 'lamusa-core'),
            ]
        );

        $this->add_control(
            'navigation_style',
            [
                'label' => __('Estilo de Navegación', 'lamusa-core'),
                'type' => \Elementor\Controls_Manager::SELECT,
                'options' => [
                    'tabs' => __('Pestañas Horizontales', 'lamusa-core'),
                    'pills' => __('Pills/Botones', 'lamusa-core'),
                    'dropdown' => __('Desplegable', 'lamusa-core'),
                ],
                'default' => 'tabs',
            ]
        );

        $this->add_control(
            'show_all_days',
            [
                'label' => __('Mostrar Todos los Días', 'lamusa-core'),
                'type' => \Elementor\Controls_Manager::SWITCHER,
                'label_on' => __('Sí', 'lamusa-core'),
                'label_off' => __('Solo Activos', 'lamusa-core'),
                'return_value' => 'yes',
                'default' => 'no',
                'description' => __('Mostrar días sin menú como "No disponible"', 'lamusa-core'),
            ]
        );

        $this->add_control(
            'show_header_info',
            [
                'label' => __('Mostrar Información de Cabecera', 'lamusa-core'),
                'type' => \Elementor\Controls_Manager::SWITCHER,
                'label_on' => __('Sí', 'lamusa-core'),
                'label_off' => __('No', 'lamusa-core'),
                'return_value' => 'yes',
                'default' => 'yes',
            ]
        );

        $this->end_controls_section();

        // Sección de estilo - Navegación
        $this->start_controls_section(
            'navigation_style_section',
            [
                'label' => __('Estilo de Navegación', 'lamusa-core'),
                'tab' => \Elementor\Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_control(
            'nav_background_color',
            [
                'label' => __('Color de Fondo Navegación', 'lamusa-core'),
                'type' => \Elementor\Controls_Manager::COLOR,
                'default' => '#e74c3c',
                'selectors' => [
                    '{{WRAPPER}} .menu-nav-restaurants' => 'background-color: {{VALUE}}',
                ],
            ]
        );

        $this->add_control(
            'nav_text_color',
            [
                'label' => __('Color de Texto Navegación', 'lamusa-core'),
                'type' => \Elementor\Controls_Manager::COLOR,
                'default' => '#ffffff',
                'selectors' => [
                    '{{WRAPPER}} .menu-nav-restaurants .nav-item' => 'color: {{VALUE}}',
                ],
            ]
        );

        $this->add_control(
            'nav_active_color',
            [
                'label' => __('Color Elemento Activo', 'lamusa-core'),
                'type' => \Elementor\Controls_Manager::COLOR,
                'default' => '#c0392b',
                'selectors' => [
                    '{{WRAPPER}} .menu-nav-restaurants .nav-item.active' => 'background-color: {{VALUE}}',
                ],
            ]
        );

        $this->add_group_control(
            \Elementor\Group_Control_Typography::get_type(),
            [
                'name' => 'nav_typography',
                'label' => __('Tipografía Navegación', 'lamusa-core'),
                'selector' => '{{WRAPPER}} .menu-nav-restaurants .nav-item',
            ]
        );

        $this->end_controls_section();

        // Sección de estilo - Días
        $this->start_controls_section(
            'days_style_section',
            [
                'label' => __('Estilo de Días', 'lamusa-core'),
                'tab' => \Elementor\Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_control(
            'days_background_color',
            [
                'label' => __('Color de Fondo Días', 'lamusa-core'),
                'type' => \Elementor\Controls_Manager::COLOR,
                'default' => '#ffffff',
                'selectors' => [
                    '{{WRAPPER}} .menu-nav-days' => 'background-color: {{VALUE}}',
                ],
            ]
        );

        $this->add_control(
            'days_text_color',
            [
                'label' => __('Color de Texto Días', 'lamusa-core'),
                'type' => \Elementor\Controls_Manager::COLOR,
                'default' => '#333333',
                'selectors' => [
                    '{{WRAPPER}} .menu-nav-days .day-item' => 'color: {{VALUE}}',
                ],
            ]
        );

        $this->add_control(
            'days_active_color',
            [
                'label' => __('Color Día Activo', 'lamusa-core'),
                'type' => \Elementor\Controls_Manager::COLOR,
                'default' => '#e74c3c',
                'selectors' => [
                    '{{WRAPPER}} .menu-nav-days .day-item.active' => 'background-color: {{VALUE}}; color: #ffffff;',
                ],
            ]
        );

        $this->end_controls_section();

        // Sección de estilo - Títulos del Restaurante
        $this->start_controls_section(
            'restaurant_titles_style_section',
            [
                'label' => __('Títulos del Restaurante', 'lamusa-core'),
                'tab' => \Elementor\Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_control(
            'restaurant_name_color',
            [
                'label' => __('Color Título Principal', 'lamusa-core'),
                'type' => \Elementor\Controls_Manager::COLOR,
                'default' => '#e74c3c',
                'selectors' => [
                    '{{WRAPPER}} .restaurant-name' => 'color: {{VALUE}}',
                ],
            ]
        );

        $this->add_group_control(
            \Elementor\Group_Control_Typography::get_type(),
            [
                'name' => 'restaurant_name_typography',
                'label' => __('Tipografía Título Principal', 'lamusa-core'),
                'selector' => '{{WRAPPER}} .restaurant-name',
            ]
        );

        $this->add_control(
            'restaurant_name_secondary_color',
            [
                'label' => __('Color Título Secundario', 'lamusa-core'),
                'type' => \Elementor\Controls_Manager::COLOR,
                'default' => '#333333',
                'selectors' => [
                    '{{WRAPPER}} .restaurant-name-secondary' => 'color: {{VALUE}}',
                ],
            ]
        );

        $this->add_group_control(
            \Elementor\Group_Control_Typography::get_type(),
            [
                'name' => 'restaurant_name_secondary_typography',
                'label' => __('Tipografía Título Secundario', 'lamusa-core'),
                'selector' => '{{WRAPPER}} .restaurant-name-secondary',
            ]
        );

        $this->add_control(
            'restaurant_subtitle_color',
            [
                'label' => __('Color Subtítulo Principal', 'lamusa-core'),
                'type' => \Elementor\Controls_Manager::COLOR,
                'default' => '#666666',
                'selectors' => [
                    '{{WRAPPER}} .menu-header-subtitle' => 'color: {{VALUE}}',
                ],
            ]
        );

        $this->add_group_control(
            \Elementor\Group_Control_Typography::get_type(),
            [
                'name' => 'restaurant_subtitle_typography',
                'label' => __('Tipografía Subtítulo Principal', 'lamusa-core'),
                'selector' => '{{WRAPPER}} .menu-header-subtitle',
            ]
        );

        $this->add_control(
            'restaurant_subtitle_secondary_color',
            [
                'label' => __('Color Subtítulo Secundario', 'lamusa-core'),
                'type' => \Elementor\Controls_Manager::COLOR,
                'default' => '#999999',
                'selectors' => [
                    '{{WRAPPER}} .menu-header-subtitle-secondary' => 'color: {{VALUE}}',
                ],
            ]
        );

        $this->add_group_control(
            \Elementor\Group_Control_Typography::get_type(),
            [
                'name' => 'restaurant_subtitle_secondary_typography',
                'label' => __('Tipografía Subtítulo Secundario', 'lamusa-core'),
                'selector' => '{{WRAPPER}} .menu-header-subtitle-secondary',
            ]
        );

        $this->end_controls_section();

        // Sección de estilo - Precio y Fechas
        $this->start_controls_section(
            'price_dates_style_section',
            [
                'label' => __('Precio y Fechas', 'lamusa-core'),
                'tab' => \Elementor\Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_control(
            'price_color',
            [
                'label' => __('Color del Precio', 'lamusa-core'),
                'type' => \Elementor\Controls_Manager::COLOR,
                'default' => '#e74c3c',
                'selectors' => [
                    '{{WRAPPER}} .menu-price' => 'color: {{VALUE}}',
                ],
            ]
        );

        $this->add_group_control(
            \Elementor\Group_Control_Typography::get_type(),
            [
                'name' => 'price_typography',
                'label' => __('Tipografía del Precio', 'lamusa-core'),
                'selector' => '{{WRAPPER}} .menu-price',
            ]
        );

        $this->add_control(
            'dates_background_color',
            [
                'label' => __('Color de Fondo Fechas', 'lamusa-core'),
                'type' => \Elementor\Controls_Manager::COLOR,
                'default' => '#e74c3c',
                'selectors' => [
                    '{{WRAPPER}} .menu-dates' => 'background-color: {{VALUE}}',
                ],
            ]
        );

        $this->add_control(
            'dates_text_color',
            [
                'label' => __('Color Texto Fechas', 'lamusa-core'),
                'type' => \Elementor\Controls_Manager::COLOR,
                'default' => '#ffffff',
                'selectors' => [
                    '{{WRAPPER}} .menu-dates' => 'color: {{VALUE}}',
                ],
            ]
        );

        $this->add_group_control(
            \Elementor\Group_Control_Typography::get_type(),
            [
                'name' => 'dates_typography',
                'label' => __('Tipografía Fechas', 'lamusa-core'),
                'selector' => '{{WRAPPER}} .menu-dates',
            ]
        );

        $this->end_controls_section();

        // Sección de estilo - Introducción
        $this->start_controls_section(
            'introduction_style_section',
            [
                'label' => __('Introducción del Menú', 'lamusa-core'),
                'tab' => \Elementor\Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_control(
            'introduction_background_color',
            [
                'label' => __('Color de Fondo', 'lamusa-core'),
                'type' => \Elementor\Controls_Manager::COLOR,
                'default' => '#f8f9fa',
                'selectors' => [
                    '{{WRAPPER}} .menu-introduction' => 'background-color: {{VALUE}}',
                ],
            ]
        );

        $this->add_control(
            'introduction_text_color',
            [
                'label' => __('Color del Texto', 'lamusa-core'),
                'type' => \Elementor\Controls_Manager::COLOR,
                'default' => '#555555',
                'selectors' => [
                    '{{WRAPPER}} .menu-introduction' => 'color: {{VALUE}}',
                ],
            ]
        );

        $this->add_control(
            'introduction_border_color',
            [
                'label' => __('Color del Borde Superior', 'lamusa-core'),
                'type' => \Elementor\Controls_Manager::COLOR,
                'default' => '#e74c3c',
                'selectors' => [
                    '{{WRAPPER}} .menu-introduction' => 'border-top-color: {{VALUE}}',
                ],
            ]
        );

        $this->add_group_control(
            \Elementor\Group_Control_Typography::get_type(),
            [
                'name' => 'introduction_typography',
                'label' => __('Tipografía', 'lamusa-core'),
                'selector' => '{{WRAPPER}} .menu-introduction',
            ]
        );

        $this->add_responsive_control(
            'introduction_padding',
            [
                'label' => __('Padding', 'lamusa-core'),
                'type' => \Elementor\Controls_Manager::DIMENSIONS,
                'size_units' => ['px', 'em', '%'],
                'selectors' => [
                    '{{WRAPPER}} .menu-introduction' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->end_controls_section();

        // Sección de estilo - Contenido del Menú
        $this->start_controls_section(
            'menu_content_style_section',
            [
                'label' => __('Contenido del Menú', 'lamusa-core'),
                'tab' => \Elementor\Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_control(
            'content_background_color',
            [
                'label' => __('Color de Fondo del Contenido', 'lamusa-core'),
                'type' => \Elementor\Controls_Manager::COLOR,
                'default' => '#ffffff',
                'selectors' => [
                    '{{WRAPPER}} .menu-content-container' => 'background-color: {{VALUE}}',
                ],
            ]
        );

        $this->add_control(
            'section_title_color',
            [
                'label' => __('Color Títulos de Sección (PRIMEROS, SEGUNDOS)', 'lamusa-core'),
                'type' => \Elementor\Controls_Manager::COLOR,
                'default' => '#333333',
                'selectors' => [
                    '{{WRAPPER}} .menu-day-content h3' => 'color: {{VALUE}}',
                ],
            ]
        );

        $this->add_group_control(
            \Elementor\Group_Control_Typography::get_type(),
            [
                'name' => 'section_title_typography',
                'label' => __('Tipografía Títulos de Sección', 'lamusa-core'),
                'selector' => '{{WRAPPER}} .menu-day-content h3',
            ]
        );

        $this->add_control(
            'menu_text_color',
            [
                'label' => __('Color Texto del Menú', 'lamusa-core'),
                'type' => \Elementor\Controls_Manager::COLOR,
                'default' => '#666666',
                'selectors' => [
                    '{{WRAPPER}} .menu-day-content p' => 'color: {{VALUE}}',
                ],
            ]
        );

        $this->add_group_control(
            \Elementor\Group_Control_Typography::get_type(),
            [
                'name' => 'menu_text_typography',
                'label' => __('Tipografía Texto del Menú', 'lamusa-core'),
                'selector' => '{{WRAPPER}} .menu-day-content p',
            ]
        );

        $this->add_responsive_control(
            'content_padding',
            [
                'label' => __('Padding del Contenido', 'lamusa-core'),
                'type' => \Elementor\Controls_Manager::DIMENSIONS,
                'size_units' => ['px', 'em', '%'],
                'default' => [
                    'top' => '30',
                    'right' => '40',
                    'bottom' => '30',
                    'left' => '40',
                    'unit' => 'px',
                ],
                'selectors' => [
                    '{{WRAPPER}} .menu-content-container' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->end_controls_section();

        // Sección de estilo - Alérgenos
        $this->start_controls_section(
            'allergens_style_section',
            [
                'label' => __('Sección de Alérgenos', 'lamusa-core'),
                'tab' => \Elementor\Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_control(
            'allergens_badge_background',
            [
                'label' => __('Color Fondo Badge Alérgenos', 'lamusa-core'),
                'type' => \Elementor\Controls_Manager::COLOR,
                'default' => '#e74c3c',
                'selectors' => [
                    '{{WRAPPER}} .allergens-badge' => 'background-color: {{VALUE}}',
                ],
            ]
        );

        $this->add_control(
            'allergens_badge_text_color',
            [
                'label' => __('Color Texto Badge Alérgenos', 'lamusa-core'),
                'type' => \Elementor\Controls_Manager::COLOR,
                'default' => '#ffffff',
                'selectors' => [
                    '{{WRAPPER}} .allergens-badge' => 'color: {{VALUE}}',
                ],
            ]
        );

        $this->add_group_control(
            \Elementor\Group_Control_Typography::get_type(),
            [
                'name' => 'allergens_badge_typography',
                'label' => __('Tipografía Badge Alérgenos', 'lamusa-core'),
                'selector' => '{{WRAPPER}} .allergens-badge',
            ]
        );

        $this->add_control(
            'allergens_section_background',
            [
                'label' => __('Color Fondo Sección Alérgenos', 'lamusa-core'),
                'type' => \Elementor\Controls_Manager::COLOR,
                'default' => '#f8f9fa',
                'selectors' => [
                    '{{WRAPPER}} .menu-allergens-section' => 'background-color: {{VALUE}}',
                ],
            ]
        );

        $this->end_controls_section();

        // Sección de estilo - Espaciado y Layout
        $this->start_controls_section(
            'layout_style_section',
            [
                'label' => __('Espaciado y Layout', 'lamusa-core'),
                'tab' => \Elementor\Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_responsive_control(
            'header_padding',
            [
                'label' => __('Padding de la Cabecera', 'lamusa-core'),
                'type' => \Elementor\Controls_Manager::DIMENSIONS,
                'size_units' => ['px', 'em', '%'],
                'default' => [
                    'top' => '30',
                    'right' => '40',
                    'bottom' => '30',
                    'left' => '40',
                    'unit' => 'px',
                ],
                'selectors' => [
                    '{{WRAPPER}} .menu-header-content' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->add_responsive_control(
            'title_spacing',
            [
                'label' => __('Espaciado entre Títulos', 'lamusa-core'),
                'type' => \Elementor\Controls_Manager::SLIDER,
                'size_units' => ['px', 'em'],
                'range' => [
                    'px' => [
                        'min' => 0,
                        'max' => 50,
                    ],
                ],
                'default' => [
                    'unit' => 'px',
                    'size' => 15,
                ],
                'selectors' => [
                    '{{WRAPPER}} .menu-header-title' => 'margin-bottom: {{SIZE}}{{UNIT}};',
                    '{{WRAPPER}} .menu-header-subtitle' => 'margin-bottom: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        $this->add_responsive_control(
            'content_section_spacing',
            [
                'label' => __('Espaciado entre Secciones del Menú', 'lamusa-core'),
                'type' => \Elementor\Controls_Manager::SLIDER,
                'size_units' => ['px', 'em'],
                'range' => [
                    'px' => [
                        'min' => 10,
                        'max' => 100,
                    ],
                ],
                'default' => [
                    'unit' => 'px',
                    'size' => 30,
                ],
                'selectors' => [
                    '{{WRAPPER}} .menu-day-content h3' => 'margin-top: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        $this->end_controls_section();
    }

    private function get_restaurant_options() {
        $restaurant_options = array('' => __('Seleccionar restaurante...', 'lamusa-core'));
        
        // Verificar que la función existe antes de usarla
        if (function_exists('lamusa_get_restaurants')) {
            $restaurants = lamusa_get_restaurants();
            foreach ($restaurants as $restaurant) {
                $restaurant_options[$restaurant->ID] = $restaurant->post_title;
            }
        } else {
            // Fallback: obtener restaurantes directamente
            $restaurants = get_posts(array(
                'post_type' => 'restaurant',
                'post_status' => 'publish',
                'posts_per_page' => -1,
                'orderby' => 'title',
                'order' => 'ASC'
            ));
            
            foreach ($restaurants as $restaurant) {
                $restaurant_options[$restaurant->ID] = $restaurant->post_title;
            }
        }
        
        return $restaurant_options;
    }

    protected function render() {
        $settings = $this->get_settings_for_display();
        $default_restaurant = $settings['default_restaurant'];
        $restaurants_to_show = $settings['restaurants_to_show'];
        $navigation_style = $settings['navigation_style'];
        $show_all_days = $settings['show_all_days'] === 'yes';
        $show_header_info = $settings['show_header_info'] === 'yes';

        // Obtener restaurantes disponibles
        $restaurants = $this->get_available_restaurants($restaurants_to_show);
        
        if (empty($restaurants)) {
            $message = __('No hay restaurantes disponibles.', 'lamusa-core');
            echo '<div class="menu-error">' . $message . '</div>';
            return;
        }

        // Usar el primer restaurante como default si no se especifica
        if (!$default_restaurant || !in_array($default_restaurant, array_keys($restaurants))) {
            $default_restaurant = array_key_first($restaurants);
        }

        echo '<div class="lamusa-menu-navigator" data-default-restaurant="' . esc_attr($default_restaurant) . '">';

        // Navegación de restaurantes
        $this->render_restaurant_navigation($restaurants, $navigation_style, $default_restaurant);

        // Contenedor principal del menú
        echo '<div class="menu-navigator-content">';

        // Información de cabecera
        if ($show_header_info) {
            echo '<div class="menu-header-section">';
            $this->render_initial_header($default_restaurant);
            echo '</div>';
        }

        // Navegación de días
        echo '<div class="menu-nav-days-container">';
        echo '<div class="menu-nav-days">';
        $this->render_initial_days($default_restaurant, $show_all_days);
        echo '</div>';
        echo '</div>';

        // Contenido del menú
        echo '<div class="menu-content-container">';
        echo '<div class="menu-content-display">';
        $this->render_initial_content($default_restaurant);
        echo '</div>';
        echo '</div>';

        echo '</div>'; // menu-navigator-content
        echo '</div>'; // lamusa-menu-navigator

        // Script JavaScript inline para asegurar que se carga
        $this->render_inline_script();
    }

    private function get_available_restaurants($restaurants_to_show) {
        $restaurants = array();
        
        // Obtener restaurantes directamente sin dependencias
        $all_restaurants = get_posts(array(
            'post_type' => 'restaurant',
            'post_status' => 'publish',
            'posts_per_page' => -1,
            'orderby' => 'title',
            'order' => 'ASC'
        ));

        foreach ($all_restaurants as $restaurant) {
            // Si se especificaron restaurantes, solo incluir esos
            if (!empty($restaurants_to_show) && !in_array($restaurant->ID, $restaurants_to_show)) {
                continue;
            }

            $restaurants[$restaurant->ID] = $restaurant->post_title;
        }

        return $restaurants;
    }

    private function render_restaurant_navigation($restaurants, $style, $default_restaurant = null) {
        $nav_class = 'menu-nav-restaurants menu-nav-' . $style;
        
        echo '<div class="' . esc_attr($nav_class) . '">';
        
        $first_restaurant = true;
        foreach ($restaurants as $restaurant_id => $restaurant_name) {
            $restaurant_title = get_field('restaurant_title', $restaurant_id);
            $restaurant_title_secondary = get_field('restaurant_title_secondary', $restaurant_id);
            
            // Combinar títulos si existen
            $display_name = $restaurant_title ?: $restaurant_name;
            if ($restaurant_title_secondary) {
                $display_name .= ' ' . $restaurant_title_secondary;
            }
            
            // Determinar si es el activo
            $is_active = ($restaurant_id == $default_restaurant) || ($first_restaurant && !$default_restaurant);
            $nav_item_class = 'nav-item' . ($is_active ? ' active' : '');
            
            echo '<div class="' . esc_attr($nav_item_class) . '" data-restaurant-id="' . esc_attr($restaurant_id) . '" tabindex="0" role="tab">';
            echo '<span class="nav-title">' . esc_html($display_name) . '</span>';
            echo '</div>';
            
            $first_restaurant = false;
        }
        
        echo '</div>';
    }

    private function render_initial_header($restaurant_id) {
        if (!$restaurant_id) {
            echo '<div class="menu-no-restaurant">Selecciona un restaurante para ver el menú</div>';
            return;
        }

        $restaurant = get_post($restaurant_id);
        if (!$restaurant) {
            echo '<div class="menu-error">Restaurante no encontrado</div>';
            return;
        }

        // Obtener datos del restaurante
        $restaurant_title = get_field('restaurant_title', $restaurant_id) ?: $restaurant->post_title;
        $restaurant_title_secondary = get_field('restaurant_title_secondary', $restaurant_id);
        $restaurant_subtitle = get_field('restaurant_subtitle', $restaurant_id);
        $restaurant_subtitle_secondary = get_field('restaurant_subtitle_secondary', $restaurant_id);

        // Obtener menú activo - método directo más flexible
        $menu_posts = get_posts(array(
            'post_type' => 'weekly_menu',
            'post_status' => 'publish',
            'posts_per_page' => 1,
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
        
        $menu = !empty($menu_posts) ? $menu_posts[0] : null;
        
        // Si no se encuentra con meta_query, intentar de otra manera
        if (!$menu) {
            $all_menus = get_posts(array(
                'post_type' => 'weekly_menu',
                'post_status' => 'publish',
                'posts_per_page' => -1,
                'orderby' => 'date',
                'order' => 'DESC'
            ));
            
            foreach ($all_menus as $menu_post) {
                $menu_restaurant = get_field('restaurant', $menu_post->ID);
                if ($menu_restaurant && ($menu_restaurant == $restaurant_id || (is_object($menu_restaurant) && $menu_restaurant->ID == $restaurant_id))) {
                    $menu = $menu_post;
                    break;
                }
            }
        }

        echo '<div class="menu-header">';
        echo '<div class="menu-header-content">';

        // Sección de títulos (lado izquierdo)
        echo '<div class="menu-header-title-section">';
        echo '<div class="menu-header-title">';
        echo '<span class="restaurant-name">' . esc_html($restaurant_title) . '</span>';
        if ($restaurant_title_secondary) {
            echo '<span class="restaurant-name-secondary">' . esc_html($restaurant_title_secondary) . '</span>';
        }
        echo '</div>';

        // Subtítulos
        if ($restaurant_subtitle) {
            echo '<div class="menu-header-subtitle">' . esc_html($restaurant_subtitle) . '</div>';
        }
        if ($restaurant_subtitle_secondary) {
            echo '<div class="menu-header-subtitle-secondary">' . esc_html($restaurant_subtitle_secondary) . '</div>';
        }
        echo '</div>'; // menu-header-title-section

        // Información del menú (lado derecho)
        if ($menu) {
            $menu_price = get_field('menu_price', $menu->ID);
            $start_date = get_field('start_date', $menu->ID);
            $end_date = get_field('end_date', $menu->ID);
            $menu_introduction = get_field('menu_introduction', $menu->ID);

            // Precio (esquina superior derecha)
            if ($menu_price) {
                echo '<div class="menu-price-section">';
                echo '<div class="menu-price">' . esc_html($menu_price) . '</div>';
                echo '</div>';
            }

            // Fechas (debajo del precio)
            if ($start_date && $end_date) {
                echo '<div class="menu-dates-section">';
                $dates = date('d/m', strtotime($start_date)) . ' - ' . date('d/m', strtotime($end_date));
                echo '<div class="menu-dates">' . esc_html($dates) . '</div>';
                echo '</div>';
            }

            // Descripción general (ancho completo)
            if ($menu_introduction) {
                echo '<div class="menu-introduction">' . wp_kses_post($menu_introduction) . '</div>';
            }
        } else {
            echo '<div class="menu-not-available"><p>No hay menú semanal activo para este restaurante</p></div>';
        }

        echo '</div>'; // menu-header-content
        echo '</div>'; // menu-header
    }

    private function render_initial_days($restaurant_id, $show_all_days = false) {
        if (!$restaurant_id) {
            return;
        }

        // Obtener menú activo - método directo más flexible
        $menu_posts = get_posts(array(
            'post_type' => 'weekly_menu',
            'post_status' => 'publish',
            'posts_per_page' => 1,
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
        
        $menu = !empty($menu_posts) ? $menu_posts[0] : null;
        
        // Si no se encuentra con meta_query, intentar de otra manera
        if (!$menu) {
            $all_menus = get_posts(array(
                'post_type' => 'weekly_menu',
                'post_status' => 'publish',
                'posts_per_page' => -1,
                'orderby' => 'date',
                'order' => 'DESC'
            ));
            
            foreach ($all_menus as $menu_post) {
                $menu_restaurant = get_field('restaurant', $menu_post->ID);
                if ($menu_restaurant && ($menu_restaurant == $restaurant_id || (is_object($menu_restaurant) && $menu_restaurant->ID == $restaurant_id))) {
                    $menu = $menu_post;
                    break;
                }
            }
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

        $menu_days = array();
        if ($menu) {
            $menu_days_data = get_field('menu_days', $menu->ID);
            if ($menu_days_data) {
                foreach ($menu_days_data as $day) {
                    $menu_days[$day['day_name']] = $day;
                }
            }
        }

        $first_active_day = null;

        foreach ($week_days as $day_key => $day_display) {
            $day_info = $menu_days[$day_key] ?? null;
            $is_active = $day_info && $day_info['day_active'];
            
            // Si no se muestran todos los días y este día no está activo, saltar
            if (!$show_all_days && !$is_active) {
                continue;
            }
            
            if ($is_active && !$first_active_day) {
                $first_active_day = $day_key;
            }

            $day_class = 'day-item' . ($is_active ? ' has-menu' : ' no-menu');
            if ($day_key === $first_active_day) {
                $day_class .= ' active';
            }

            echo '<div class="' . esc_attr($day_class) . '" data-day="' . esc_attr($day_key) . '" tabindex="0" role="tab">';
            echo '<span class="day-name">' . esc_html($day_display) . '</span>';

            if ($day_info && !empty($day_info['day_date'])) {
                echo '<span class="day-date">' . date('d/m', strtotime($day_info['day_date'])) . '</span>';
            }

            if (!$is_active && $show_all_days) {
                echo '<span class="day-status">No disponible</span>';
            }

            echo '</div>';
        }
    }

    private function render_initial_content($restaurant_id) {
        if (!$restaurant_id) {
            echo '<div class="menu-error">No hay restaurante seleccionado</div>';
            return;
        }

        // Obtener menú activo - método directo más flexible
        $menu_posts = get_posts(array(
            'post_type' => 'weekly_menu',
            'post_status' => 'publish',
            'posts_per_page' => 1,
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
        
        $menu = !empty($menu_posts) ? $menu_posts[0] : null;
        
        // Si no se encuentra con meta_query, intentar de otra manera
        if (!$menu) {
            $all_menus = get_posts(array(
                'post_type' => 'weekly_menu',
                'post_status' => 'publish',
                'posts_per_page' => -1,
                'orderby' => 'date',
                'order' => 'DESC'
            ));
            
            foreach ($all_menus as $menu_post) {
                $menu_restaurant = get_field('restaurant', $menu_post->ID);
                if ($menu_restaurant && ($menu_restaurant == $restaurant_id || (is_object($menu_restaurant) && $menu_restaurant->ID == $restaurant_id))) {
                    $menu = $menu_post;
                    break;
                }
            }
        }

        if (!$menu) {
            echo '<div class="menu-not-available"><p>No hay menú semanal activo para este restaurante</p></div>';
            return;
        }

        // Obtener el primer día activo
        $menu_days = get_field('menu_days', $menu->ID);
        $first_day = null;

        if ($menu_days) {
            foreach ($menu_days as $day) {
                if ($day['day_active']) {
                    $first_day = $day;
                    break;
                }
            }
        }

        if ($first_day) {
            if (!empty($first_day['special_note'])) {
                echo '<div class="menu-special-note">' . esc_html($first_day['special_note']) . '</div>';
            } else if (!empty($first_day['menu_content'])) {
                echo '<div class="menu-day-content">' . wp_kses_post($first_day['menu_content']) . '</div>';
            }

            // Añadir información de alérgenos
            if (function_exists('lamusa_get_menu_allergens_summary')) {
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
                    
                    echo '</div>';
                    echo '</div>';
                }
            }
        } else {
            echo '<div class="menu-not-available"><p>No hay contenido de menú disponible</p></div>';
        }
    }

    private function render_inline_script() {
        ?>
        <script>
        jQuery(document).ready(function($) {
            console.log('La Musa Menu Navigator: Inicializando...');
            
            $('.lamusa-menu-navigator').each(function() {
                var $container = $(this);
                var defaultRestaurant = $container.data('default-restaurant');
                
                console.log('Restaurante por defecto:', defaultRestaurant);
                
                // Navegación de restaurantes
                $container.on('click', '.nav-item', function() {
                    var restaurantId = $(this).data('restaurant-id');
                    console.log('Cambiando a restaurante:', restaurantId);
                    
                    // Actualizar estado visual
                    $container.find('.nav-item').removeClass('active');
                    $(this).addClass('active');
                    
                    // Ya no usamos loading simulado, funciona directamente
                    console.log('Datos cargados para restaurante:', restaurantId);
                });
                
                // Navegación de días
                $container.on('click', '.day-item:not(.no-menu)', function() {
                    var dayName = $(this).data('day');
                    console.log('Cambiando a día:', dayName);
                    
                    // Actualizar estado visual
                    $container.find('.day-item').removeClass('active');
                    $(this).addClass('active');
                    
                    // Ya no usamos loading simulado para días
                    console.log('Contenido cargado para día:', dayName);
                });
                
                console.log('La Musa Menu Navigator: Inicializado correctamente');
            });
        });
        </script>
        
        <style>
        /* Estilos específicos para este widget */
        .lamusa-menu-navigator .menu-nav-days {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(120px, 1fr));
        }
        
        /* Adaptación automática según número de días */
        .lamusa-menu-navigator .menu-nav-days .day-item:first-child:nth-last-child(5),
        .lamusa-menu-navigator .menu-nav-days .day-item:first-child:nth-last-child(5) ~ .day-item {
            /* 5 días */
            min-width: 20%;
        }
        
        .lamusa-menu-navigator .menu-nav-days .day-item:first-child:nth-last-child(6),
        .lamusa-menu-navigator .menu-nav-days .day-item:first-child:nth-last-child(6) ~ .day-item {
            /* 6 días */
            min-width: 16.666%;
        }
        
        .lamusa-menu-navigator .menu-nav-days .day-item:first-child:nth-last-child(7),
        .lamusa-menu-navigator .menu-nav-days .day-item:first-child:nth-last-child(7) ~ .day-item {
            /* 7 días */
            min-width: 14.285%;
        }
        </style>
        <?php
    }

    private function render_javascript() {
        ?>
        <script>
        (function($) {
            'use strict';
            
            const MenuNavigator = {
                currentRestaurant: null,
                currentDay: null,
                
                init: function($container) {
                    this.$container = $container;
                    this.currentRestaurant = $container.data('default-restaurant');
                    
                    this.bindEvents();
                    this.loadRestaurantData(this.currentRestaurant);
                },
                
                bindEvents: function() {
                    const self = this;
                    
                    // Navegación de restaurantes
                    this.$container.on('click', '.nav-item', function() {
                        const restaurantId = $(this).data('restaurant-id');
                        self.switchRestaurant(restaurantId);
                    });
                    
                    // Navegación de días
                    this.$container.on('click', '.day-item', function() {
                        const dayName = $(this).data('day');
                        self.switchDay(dayName);
                    });
                },
                
                switchRestaurant: function(restaurantId) {
                    if (restaurantId === this.currentRestaurant) return;
                    
                    // Actualizar estado visual
                    this.$container.find('.nav-item').removeClass('active');
                    this.$container.find('.nav-item[data-restaurant-id="' + restaurantId + '"]').addClass('active');
                    
                    this.currentRestaurant = restaurantId;
                    this.loadRestaurantData(restaurantId);
                },
                
                switchDay: function(dayName) {
                    if (dayName === this.currentDay) return;
                    
                    // Actualizar estado visual
                    this.$container.find('.day-item').removeClass('active');
                    this.$container.find('.day-item[data-day="' + dayName + '"]').addClass('active');
                    
                    this.currentDay = dayName;
                    this.loadDayContent(dayName);
                },
                
                loadRestaurantData: function(restaurantId) {
                    const self = this;
                    
                    // Mostrar loading
                    this.showLoading();
                    
                    $.ajax({
                        url: lamusaNavigator.ajaxurl,
                        type: 'POST',
                        data: {
                            action: 'lamusa_get_restaurant_menu_data',
                            restaurant_id: restaurantId,
                            nonce: lamusaNavigator.nonce
                        },
                        success: function(response) {
                            if (response.success) {
                                self.renderRestaurantData(response.data);
                            } else {
                                self.showError(response.data || 'Error al cargar los datos');
                            }
                        },
                        error: function() {
                            self.showError('Error de conexión');
                        }
                    });
                },
                
                loadDayContent: function(dayName) {
                    const self = this;
                    
                    $.ajax({
                        url: lamusaNavigator.ajaxurl,
                        type: 'POST',
                        data: {
                            action: 'lamusa_get_day_menu_content',
                            restaurant_id: this.currentRestaurant,
                            day_name: dayName,
                            nonce: lamusaNavigator.nonce
                        },
                        success: function(response) {
                            if (response.success) {
                                self.renderDayContent(response.data);
                            } else {
                                self.showError(response.data || 'Error al cargar el menú del día');
                            }
                        }
                    });
                },
                
                renderRestaurantData: function(data) {
                    // Renderizar cabecera
                    this.renderHeader(data.restaurant, data.menu);
                    
                    // Renderizar navegación de días
                    this.renderDaysNavigation(data.days);
                    
                    // Cargar el primer día disponible
                    if (data.days && data.days.length > 0) {
                        const firstDay = data.days.find(day => day.active) || data.days[0];
                        this.switchDay(firstDay.name);
                    }
                },
                
                renderHeader: function(restaurant, menu) {
                    let headerHtml = '<div class="menu-header">';
                    
                    // Título del restaurante
                    headerHtml += '<div class="menu-header-title">';
                    headerHtml += '<span class="restaurant-name">' + restaurant.title + '</span>';
                    if (restaurant.title_secondary) {
                        headerHtml += ' <span class="restaurant-name-secondary">' + restaurant.title_secondary + '</span>';
                    }
                    headerHtml += '</div>';
                    
                    // Subtítulos
                    if (restaurant.subtitle) {
                        headerHtml += '<div class="menu-header-subtitle">' + restaurant.subtitle + '</div>';
                    }
                    if (restaurant.subtitle_secondary) {
                        headerHtml += '<div class="menu-header-subtitle-secondary">' + restaurant.subtitle_secondary + '</div>';
                    }
                    
                    // Información del menú
                    if (menu) {
                        headerHtml += '<div class="menu-info-row">';
                        
                        // Precio
                        if (menu.price) {
                            headerHtml += '<div class="menu-price">' + menu.price + '</div>';
                        }
                        
                        // Fechas
                        if (menu.dates) {
                            headerHtml += '<div class="menu-dates">' + menu.dates + '</div>';
                        }
                        
                        headerHtml += '</div>';
                        
                        // Descripción general
                        if (menu.introduction) {
                            headerHtml += '<div class="menu-introduction">' + menu.introduction + '</div>';
                        }
                    }
                    
                    headerHtml += '</div>';
                    
                    this.$container.find('.menu-header-section').html(headerHtml);
                },
                
                renderDaysNavigation: function(days) {
                    let daysHtml = '';
                    
                    days.forEach(day => {
                        const dayClass = 'day-item' + (day.active ? ' has-menu' : ' no-menu');
                        daysHtml += '<div class="' + dayClass + '" data-day="' + day.name + '">';
                        daysHtml += '<span class="day-name">' + day.display_name + '</span>';
                        if (day.date) {
                            daysHtml += '<span class="day-date">' + day.date + '</span>';
                        }
                        if (!day.active) {
                            daysHtml += '<span class="day-status">No disponible</span>';
                        }
                        daysHtml += '</div>';
                    });
                    
                    this.$container.find('.menu-nav-days').html(daysHtml);
                },
                
                renderDayContent: function(content) {
                    this.$container.find('.menu-content-display').html(content);
                },
                
                showLoading: function() {
                    this.$container.find('.menu-navigator-content').addClass('loading');
                },
                
                hideLoading: function() {
                    this.$container.find('.menu-navigator-content').removeClass('loading');
                },
                
                showError: function(message) {
                    this.hideLoading();
                    this.$container.find('.menu-content-display').html(
                        '<div class="menu-error">Error: ' + message + '</div>'
                    );
                }
            };
            
            // Inicializar cada instancia del widget
            $(document).ready(function() {
                $('.lamusa-menu-navigator').each(function() {
                    MenuNavigator.init($(this));
                });
            });
            
        })(jQuery);
        </script>
        <?php
    }

    protected function _content_template() {
        ?>
        <div class="lamusa-menu-navigator">
            <div class="menu-nav-restaurants menu-nav-tabs">
                <div class="nav-item active">
                    <span class="nav-title"><?php _e('Ojalá Pool Bar', 'lamusa-core'); ?></span>
                </div>
                <div class="nav-item">
                    <span class="nav-title"><?php _e('La Musa Malasaña', 'lamusa-core'); ?></span>
                </div>
                <div class="nav-item">
                    <span class="nav-title"><?php _e('Limbo Tribunal', 'lamusa-core'); ?></span>
                </div>
            </div>
            
            <div class="menu-navigator-content">
                <div class="menu-header-section">
                    <div class="menu-header">
                        <div class="menu-header-title">
                            <span class="restaurant-name"><?php _e('OJALÁ', 'lamusa-core'); ?></span>
                            <span class="restaurant-name-secondary"><?php _e('Pool Bar', 'lamusa-core'); ?></span>
                        </div>
                        <div class="menu-info-row">
                            <div class="menu-price"><?php _e('16,5€', 'lamusa-core'); ?></div>
                            <div class="menu-dates"><?php _e('19/05 - 25/05', 'lamusa-core'); ?></div>
                        </div>
                        <div class="menu-introduction"><?php _e('Primero + Segundo + Postre o café, pan y bebida incluidos / De lunes a viernes de 13.00h a 17.00h, excepto festivos', 'lamusa-core'); ?></div>
                    </div>
                </div>
                
                <div class="menu-nav-days-container">
                    <div class="menu-nav-days">
                        <div class="day-item active has-menu" data-day="lunes">
                            <span class="day-name"><?php _e('LUNES', 'lamusa-core'); ?></span>
                        </div>
                        <div class="day-item has-menu" data-day="martes">
                            <span class="day-name"><?php _e('MARTES', 'lamusa-core'); ?></span>
                        </div>
                        <div class="day-item has-menu" data-day="miercoles">
                            <span class="day-name"><?php _e('MIÉRCOLES', 'lamusa-core'); ?></span>
                        </div>
                        <div class="day-item has-menu" data-day="jueves">
                            <span class="day-name"><?php _e('JUEVES', 'lamusa-core'); ?></span>
                        </div>
                        <div class="day-item has-menu" data-day="viernes">
                            <span class="day-name"><?php _e('VIERNES', 'lamusa-core'); ?></span>
                        </div>
                    </div>
                </div>
                
                <div class="menu-content-container">
                    <div class="menu-content-display">
                        <div class="menu-sections">
                            <div class="menu-section">
                                <h3><?php _e('PRIMEROS', 'lamusa-core'); ?></h3>
                                <div class="menu-items">
                                    <p><?php _e('Ensalada Malagueña con Bacalao confitado, Naranja en rodajas y Aceitunas Negras', 'lamusa-core'); ?></p>
                                    <p><?php _e('Crema templada de remolacha con queso feta', 'lamusa-core'); ?></p>
                                </div>
                            </div>
                            
                            <div class="menu-section">
                                <h3><?php _e('SEGUNDOS', 'lamusa-core'); ?></h3>
                                <div class="menu-items">
                                    <p><?php _e('Espaguetis Nero Di sepia con Chipirones Estofados', 'lamusa-core'); ?></p>
                                    <p><?php _e('Milanesa de Chuleta de Cerdo a la Napolitana acompañada de Coleslaw', 'lamusa-core'); ?></p>
                                </div>
                            </div>
                            
                            <div class="menu-section">
                                <h3><?php _e('POSTRES', 'lamusa-core'); ?></h3>
                                <div class="menu-items">
                                    <p><?php _e('Panna Cotta de Frutos Rojos, Mousse de chocolate, pudding de croissant, cheesecake, yogur con miel o ensalada de fruta', 'lamusa-core'); ?></p>
                                </div>
                            </div>
                        </div>
                        
                        <div class="menu-allergens-section">
                            <div class="allergens-badge"><?php _e('ALÉRGENOS', 'lamusa-core'); ?></div>
                            <div class="allergens-note">
                                <p><?php _e('Contiene alérgenos', 'lamusa-core'); ?> ●</p>
                                <p><?php _e('Puede contener trazas', 'lamusa-core'); ?> T</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <?php
    }
}
