<?php
/**
 * Widget de Información de Alérgenos para Elementor
 */

if (!defined('ABSPATH')) {
    exit;
}

class LaMusaAllergenInfoWidget extends \Elementor\Widget_Base {

    public function get_name() {
        return 'lamusa_allergen_info';
    }

    public function get_title() {
        return __('Información de Alérgenos', 'lamusa-core');
    }

    public function get_icon() {
        return 'eicon-alert';
    }

    public function get_categories() {
        return ['lamusa-core'];
    }

    public function get_keywords() {
        return ['allergen', 'alergeno', 'menu', 'lamusa', 'food'];
    }

    protected function _register_controls() {
        
        // Sección de contenido
        $this->start_controls_section(
            'content_section',
            [
                'label' => __('Configuración', 'lamusa-core'),
                'tab' => \Elementor\Controls_Manager::TAB_CONTENT,
            ]
        );

        $restaurants = lamusa_get_restaurants();
        $restaurant_options = array('' => __('Seleccionar restaurante...', 'lamusa-core'));
        
        foreach ($restaurants as $restaurant) {
            $restaurant_options[$restaurant->ID] = $restaurant->post_title;
        }

        $this->add_control(
            'restaurant_id',
            [
                'label' => __('Seleccionar Restaurante', 'lamusa-core'),
                'type' => \Elementor\Controls_Manager::SELECT,
                'options' => $restaurant_options,
                'default' => '',
                'description' => __('Selecciona el restaurante del cual mostrar los alérgenos', 'lamusa-core'),
            ]
        );

        $this->add_control(
            'display_mode',
            [
                'label' => __('Modo de Visualización', 'lamusa-core'),
                'type' => \Elementor\Controls_Manager::SELECT,
                'options' => [
                    'summary' => __('Resumen de Alérgenos del Menú', 'lamusa-core'),
                    'detailed' => __('Detalles por Platos', 'lamusa-core'),
                    'legend' => __('Leyenda de Alérgenos', 'lamusa-core'),
                ],
                'default' => 'summary',
            ]
        );

        $this->add_control(
            'show_icons',
            [
                'label' => __('Mostrar Iconos', 'lamusa-core'),
                'type' => \Elementor\Controls_Manager::SWITCHER,
                'label_on' => __('Sí', 'lamusa-core'),
                'label_off' => __('No', 'lamusa-core'),
                'return_value' => 'yes',
                'default' => 'yes',
            ]
        );

        $this->add_control(
            'show_descriptions',
            [
                'label' => __('Mostrar Descripciones', 'lamusa-core'),
                'type' => \Elementor\Controls_Manager::SWITCHER,
                'label_on' => __('Sí', 'lamusa-core'),
                'label_off' => __('No', 'lamusa-core'),
                'return_value' => 'yes',
                'default' => 'no',
                'condition' => [
                    'display_mode' => 'legend',
                ],
            ]
        );

        $this->add_control(
            'columns',
            [
                'label' => __('Columnas', 'lamusa-core'),
                'type' => \Elementor\Controls_Manager::SELECT,
                'options' => [
                    '1' => '1',
                    '2' => '2',
                    '3' => '3',
                    '4' => '4',
                ],
                'default' => '2',
                'condition' => [
                    'display_mode' => ['summary', 'legend'],
                ],
            ]
        );

        $this->end_controls_section();

        // Sección de estilo
        $this->start_controls_section(
            'style_section',
            [
                'label' => __('Estilo', 'lamusa-core'),
                'tab' => \Elementor\Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_control(
            'title_color',
            [
                'label' => __('Color del Título', 'lamusa-core'),
                'type' => \Elementor\Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .allergen-title' => 'color: {{VALUE}}',
                ],
            ]
        );

        $this->add_group_control(
            \Elementor\Group_Control_Typography::get_type(),
            [
                'name' => 'title_typography',
                'label' => __('Tipografía del Título', 'lamusa-core'),
                'selector' => '{{WRAPPER}} .allergen-title',
            ]
        );

        $this->add_control(
            'allergen_item_background',
            [
                'label' => __('Fondo del Elemento', 'lamusa-core'),
                'type' => \Elementor\Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .allergen-item' => 'background-color: {{VALUE}}',
                ],
            ]
        );

        $this->add_control(
            'allergen_item_border',
            [
                'label' => __('Color del Borde', 'lamusa-core'),
                'type' => \Elementor\Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .allergen-item' => 'border-color: {{VALUE}}',
                ],
            ]
        );

        $this->add_responsive_control(
            'allergen_item_padding',
            [
                'label' => __('Padding del Elemento', 'lamusa-core'),
                'type' => \Elementor\Controls_Manager::DIMENSIONS,
                'size_units' => ['px', 'em', '%'],
                'selectors' => [
                    '{{WRAPPER}} .allergen-item' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->add_responsive_control(
            'allergen_item_margin',
            [
                'label' => __('Margin del Elemento', 'lamusa-core'),
                'type' => \Elementor\Controls_Manager::DIMENSIONS,
                'size_units' => ['px', 'em', '%'],
                'selectors' => [
                    '{{WRAPPER}} .allergen-item' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->add_control(
            'allergen_item_border_radius',
            [
                'label' => __('Border Radius', 'lamusa-core'),
                'type' => \Elementor\Controls_Manager::SLIDER,
                'size_units' => ['px', '%'],
                'range' => [
                    'px' => [
                        'min' => 0,
                        'max' => 50,
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .allergen-item' => 'border-radius: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        $this->end_controls_section();
    }

    protected function render() {
        $settings = $this->get_settings_for_display();
        $restaurant_id = $settings['restaurant_id'];

        if (!$restaurant_id) {
            if (\Elementor\Plugin::$instance->editor->is_edit_mode()) {
                echo '<div class="elementor-alert elementor-alert-warning">' . __('Por favor selecciona un restaurante en la configuración del widget.', 'lamusa-core') . '</div>';
            }
            return;
        }

        $display_mode = $settings['display_mode'];
        $show_icons = $settings['show_icons'] === 'yes';
        $show_descriptions = $settings['show_descriptions'] === 'yes';
        $columns = $settings['columns'];

        echo '<div class="allergen-info-container">';

        switch ($display_mode) {
            case 'summary':
                $this->render_allergen_summary($restaurant_id, $show_icons, $columns);
                break;
                
            case 'detailed':
                $this->render_detailed_allergens($restaurant_id, $show_icons);
                break;
                
            case 'legend':
                $this->render_allergen_legend($show_icons, $show_descriptions, $columns);
                break;
        }

        echo '</div>';
    }

    private function render_allergen_summary($restaurant_id, $show_icons, $columns) {
        $menu = lamusa_get_active_weekly_menu($restaurant_id);
        
        if (!$menu) {
            if (\Elementor\Plugin::$instance->editor->is_edit_mode()) {
                echo '<div class="elementor-alert elementor-alert-info">' . __('No hay menú semanal activo para este restaurante.', 'lamusa-core') . '</div>';
            }
            return;
        }

        $allergen_summary = lamusa_get_menu_allergens_summary($menu->ID);
        
        echo '<h3 class="allergen-title">' . __('Alérgenos del Menú', 'lamusa-core') . '</h3>';

        if (!empty($allergen_summary['contains'])) {
            echo '<div class="allergen-section allergen-contains">';
            echo '<h4 class="allergen-section-title">' . __('Contiene:', 'lamusa-core') . '</h4>';
            echo '<div class="allergen-grid allergen-grid-' . esc_attr($columns) . '">';
            
            foreach ($allergen_summary['contains'] as $allergen) {
                $this->render_allergen_item($allergen, $show_icons, 'contains');
            }
            
            echo '</div></div>';
        }

        if (!empty($allergen_summary['traces'])) {
            echo '<div class="allergen-section allergen-traces">';
            echo '<h4 class="allergen-section-title">' . __('Puede contener trazas de:', 'lamusa-core') . '</h4>';
            echo '<div class="allergen-grid allergen-grid-' . esc_attr($columns) . '">';
            
            foreach ($allergen_summary['traces'] as $allergen) {
                $this->render_allergen_item($allergen, $show_icons, 'traces');
            }
            
            echo '</div></div>';
        }

        if (empty($allergen_summary['contains']) && empty($allergen_summary['traces'])) {
            echo '<p class="no-allergens">' . __('No hay información de alérgenos disponible para este menú.', 'lamusa-core') . '</p>';
        }
    }

    private function render_detailed_allergens($restaurant_id, $show_icons) {
        $menu = lamusa_get_active_weekly_menu($restaurant_id);
        
        if (!$menu) {
            if (\Elementor\Plugin::$instance->editor->is_edit_mode()) {
                echo '<div class="elementor-alert elementor-alert-info">' . __('No hay menú semanal activo para este restaurante.', 'lamusa-core') . '</div>';
            }
            return;
        }

        $allergen_groups = get_field('allergen_groups', $menu->ID);
        
        if (!$allergen_groups) {
            echo '<p class="no-allergens">' . __('No hay información detallada de alérgenos disponible.', 'lamusa-core') . '</p>';
            return;
        }

        echo '<h3 class="allergen-title">' . __('Alérgenos por Platos', 'lamusa-core') . '</h3>';

        foreach ($allergen_groups as $group) {
            if (!empty($group['dishes'])) {
                echo '<div class="allergen-group">';
                echo '<h4 class="allergen-group-title">' . esc_html($group['group_title']) . '</h4>';
                
                foreach ($group['dishes'] as $dish) {
                    $dish_allergens = lamusa_get_dish_allergens($dish);
                    
                    if (!empty($dish_allergens['contains']) || !empty($dish_allergens['traces'])) {
                        echo '<div class="dish-allergens">';
                        echo '<h5 class="dish-name">' . esc_html($dish['dish_name']) . '</h5>';
                        
                        if (!empty($dish['dish_description'])) {
                            echo '<p class="dish-description">' . esc_html($dish['dish_description']) . '</p>';
                        }
                        
                        if (!empty($dish_allergens['contains'])) {
                            echo '<div class="dish-allergen-section">';
                            echo '<span class="allergen-prefix">' . __('Contiene:', 'lamusa-core') . '</span>';
                            foreach ($dish_allergens['contains'] as $allergen) {
                                $this->render_allergen_item($allergen, $show_icons, 'contains');
                            }
                            echo '</div>';
                        }
                        
                        if (!empty($dish_allergens['traces'])) {
                            echo '<div class="dish-allergen-section">';
                            echo '<span class="allergen-prefix">' . __('Puede contener trazas de:', 'lamusa-core') . '</span>';
                            foreach ($dish_allergens['traces'] as $allergen) {
                                $this->render_allergen_item($allergen, $show_icons, 'traces');
                            }
                            echo '</div>';
                        }
                        
                        echo '</div>';
                    }
                }
                
                echo '</div>';
            }
        }
    }

    private function render_allergen_legend($show_icons, $show_descriptions, $columns) {
        $allergens = lamusa_get_active_allergens();
        
        if (empty($allergens)) {
            echo '<p class="no-allergens">' . __('No hay alérgenos configurados.', 'lamusa-core') . '</p>';
            return;
        }

        echo '<h3 class="allergen-title">' . __('Leyenda de Alérgenos', 'lamusa-core') . '</h3>';
        echo '<div class="allergen-legend allergen-grid allergen-grid-' . esc_attr($columns) . '">';

        foreach ($allergens as $allergen) {
            echo '<div class="allergen-legend-item">';
            $this->render_allergen_item($allergen, $show_icons, 'legend');
            
            if ($show_descriptions && !empty($allergen->description)) {
                echo '<div class="allergen-description">' . esc_html($allergen->description) . '</div>';
            }
            
            echo '</div>';
        }

        echo '</div>';
    }

    private function render_allergen_item($allergen, $show_icons, $type) {
        $icon = get_term_meta($allergen->term_id, 'allergen_icon', true);
        $color = get_term_meta($allergen->term_id, 'allergen_color', true);
        
        $class = 'allergen-item allergen-' . $type;
        $style = $color ? 'color: ' . esc_attr($color) . ';' : '';
        
        echo '<span class="' . esc_attr($class) . '" style="' . $style . '">';
        
        if ($show_icons && $icon) {
            echo '<i class="' . esc_attr($icon) . '"></i> ';
        }
        
        echo esc_html($allergen->name);
        echo '</span>';
    }

    protected function _content_template() {
        ?>
        <#
        var restaurant_id = settings.restaurant_id;
        var display_mode = settings.display_mode;
        var show_icons = settings.show_icons === 'yes';
        var show_descriptions = settings.show_descriptions === 'yes';
        var columns = settings.columns;
        
        if (!restaurant_id && display_mode !== 'legend') {
            #>
            <div class="elementor-alert elementor-alert-warning">
                <?php _e('Por favor selecciona un restaurante en la configuración del widget.', 'lamusa-core'); ?>
            </div>
            <#
            return;
        }
        #>
        
        <div class="allergen-info-container">
            <# if (display_mode === 'summary') { #>
                <h3 class="allergen-title"><?php _e('Alérgenos del Menú', 'lamusa-core'); ?></h3>
                <div class="allergen-section allergen-contains">
                    <h4 class="allergen-section-title"><?php _e('Contiene:', 'lamusa-core'); ?></h4>
                    <div class="allergen-grid allergen-grid-{{ columns }}">
                        <span class="allergen-item allergen-contains">
                            <# if (show_icons) { #><i class="dashicons-warning"></i> <# } #>
                            <?php _e('Gluten', 'lamusa-core'); ?>
                        </span>
                        <span class="allergen-item allergen-contains">
                            <# if (show_icons) { #><i class="dashicons-warning"></i> <# } #>
                            <?php _e('Huevos', 'lamusa-core'); ?>
                        </span>
                    </div>
                </div>
            <# } else if (display_mode === 'detailed') { #>
                <h3 class="allergen-title"><?php _e('Alérgenos por Platos', 'lamusa-core'); ?></h3>
                <div class="allergen-group">
                    <h4 class="allergen-group-title"><?php _e('Primeros Platos', 'lamusa-core'); ?></h4>
                    <div class="dish-allergens">
                        <h5 class="dish-name"><?php _e('Ensalada Malagueña', 'lamusa-core'); ?></h5>
                        <div class="dish-allergen-section">
                            <span class="allergen-prefix"><?php _e('Contiene:', 'lamusa-core'); ?></span>
                            <span class="allergen-item allergen-contains">
                                <# if (show_icons) { #><i class="dashicons-warning"></i> <# } #>
                                <?php _e('Pescado', 'lamusa-core'); ?>
                            </span>
                        </div>
                    </div>
                </div>
            <# } else { #>
                <h3 class="allergen-title"><?php _e('Leyenda de Alérgenos', 'lamusa-core'); ?></h3>
                <div class="allergen-legend allergen-grid allergen-grid-{{ columns }}">
                    <div class="allergen-legend-item">
                        <span class="allergen-item allergen-legend">
                            <# if (show_icons) { #><i class="dashicons-warning"></i> <# } #>
                            <?php _e('Gluten', 'lamusa-core'); ?>
                        </span>
                        <# if (show_descriptions) { #>
                            <div class="allergen-description"><?php _e('Cereales que contengan gluten', 'lamusa-core'); ?></div>
                        <# } #>
                    </div>
                    <div class="allergen-legend-item">
                        <span class="allergen-item allergen-legend">
                            <# if (show_icons) { #><i class="dashicons-warning"></i> <# } #>
                            <?php _e('Huevos', 'lamusa-core'); ?>
                        </span>
                        <# if (show_descriptions) { #>
                            <div class="allergen-description"><?php _e('Huevos y productos a base de huevo', 'lamusa-core'); ?></div>
                        <# } #>
                    </div>
                </div>
            <# } #>
        </div>
        <?php
    }
}
