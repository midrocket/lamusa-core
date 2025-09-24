<?php
/**
 * Etiqueta Dinámica para Contenido de Menú del Día
 */

if (!defined('ABSPATH')) {
    exit;
}

class LaMusaMenuContentTag extends \Elementor\Core\DynamicTags\Tag {

    public function get_name() {
        return 'lamusa_menu_content';
    }

    public function get_title() {
        return __('Contenido del Menú del Día', 'lamusa-core');
    }

    public function get_group() {
        return 'lamusa-core';
    }

    public function get_categories() {
        return [
            \Elementor\Modules\DynamicTags\Module::TEXT_CATEGORY,
        ];
    }

    protected function _register_controls() {
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

        $this->add_control(
            'restaurant_id',
            [
                'label' => __('Restaurante', 'lamusa-core'),
                'type' => \Elementor\Controls_Manager::SELECT,
                'options' => $restaurant_options,
                'default' => '',
            ]
        );

        $this->add_control(
            'day_mode',
            [
                'label' => __('Modo de Día', 'lamusa-core'),
                'type' => \Elementor\Controls_Manager::SELECT,
                'options' => [
                    'today' => __('Hoy (automático)', 'lamusa-core'),
                    'specific' => __('Día específico', 'lamusa-core'),
                    'all_days' => __('Todos los días de la semana', 'lamusa-core'),
                ],
                'default' => 'today',
            ]
        );

        $this->add_control(
            'specific_day',
            [
                'label' => __('Día Específico', 'lamusa-core'),
                'type' => \Elementor\Controls_Manager::SELECT,
                'options' => [
                    'lunes' => __('Lunes', 'lamusa-core'),
                    'martes' => __('Martes', 'lamusa-core'),
                    'miercoles' => __('Miércoles', 'lamusa-core'),
                    'jueves' => __('Jueves', 'lamusa-core'),
                    'viernes' => __('Viernes', 'lamusa-core'),
                    'sabado' => __('Sábado', 'lamusa-core'),
                    'domingo' => __('Domingo', 'lamusa-core'),
                ],
                'default' => 'lunes',
                'condition' => [
                    'day_mode' => 'specific',
                ],
            ]
        );

        $this->add_control(
            'include_day_title',
            [
                'label' => __('Incluir Título del Día', 'lamusa-core'),
                'type' => \Elementor\Controls_Manager::SWITCHER,
                'label_on' => __('Sí', 'lamusa-core'),
                'label_off' => __('No', 'lamusa-core'),
                'return_value' => 'yes',
                'default' => 'no',
                'condition' => [
                    'day_mode!' => 'all_days',
                ],
            ]
        );

        $this->add_control(
            'title_format',
            [
                'label' => __('Formato del Título', 'lamusa-core'),
                'type' => \Elementor\Controls_Manager::SELECT,
                'options' => [
                    'h3' => 'H3',
                    'h4' => 'H4',
                    'h5' => 'H5',
                    'strong' => 'Strong',
                    'span' => 'Span',
                ],
                'default' => 'h3',
                'condition' => [
                    'include_day_title' => 'yes',
                ],
            ]
        );

        $this->add_control(
            'show_special_notes',
            [
                'label' => __('Mostrar Notas Especiales', 'lamusa-core'),
                'type' => \Elementor\Controls_Manager::SWITCHER,
                'label_on' => __('Sí', 'lamusa-core'),
                'label_off' => __('No', 'lamusa-core'),
                'return_value' => 'yes',
                'default' => 'yes',
                'description' => __('Mostrar notas como "Cerrado" o "Solo cena"', 'lamusa-core'),
            ]
        );

        $this->add_control(
            'fallback_text',
            [
                'label' => __('Texto de Respaldo', 'lamusa-core'),
                'type' => \Elementor\Controls_Manager::TEXT,
                'default' => __('No hay menú disponible', 'lamusa-core'),
                'description' => __('Texto a mostrar si no hay menú para el día seleccionado', 'lamusa-core'),
            ]
        );
    }

    public function render() {
        $settings = $this->get_settings();
        $restaurant_id = $settings['restaurant_id'];
        $day_mode = $settings['day_mode'];
        $specific_day = $settings['specific_day'];
        $include_day_title = $settings['include_day_title'] === 'yes';
        $title_format = $settings['title_format'] ?: 'h3';
        $show_special_notes = $settings['show_special_notes'] === 'yes';
        $fallback_text = $settings['fallback_text'] ?: __('No hay menú disponible', 'lamusa-core');

        if (!$restaurant_id) {
            echo $fallback_text;
            return;
        }

        if (!function_exists('lamusa_get_active_weekly_menu')) {
            echo $fallback_text;
            return;
        }
        
        $menu = lamusa_get_active_weekly_menu($restaurant_id);
        
        if (!$menu) {
            echo $fallback_text;
            return;
        }

        $output = '';

        switch ($day_mode) {
            case 'today':
                $output .= $this->render_single_day($menu->ID, null, $include_day_title, $title_format, $show_special_notes, $fallback_text);
                break;

            case 'specific':
                $output .= $this->render_single_day($menu->ID, $specific_day, $include_day_title, $title_format, $show_special_notes, $fallback_text);
                break;

            case 'all_days':
                $output .= $this->render_all_days($menu->ID, $show_special_notes);
                break;
        }

        echo $output;
    }

    private function render_single_day($menu_id, $day_name, $include_title, $title_format, $show_special_notes, $fallback) {
        $day_menu = function_exists('lamusa_get_menu_day') ? lamusa_get_menu_day($menu_id, $day_name) : null;
        
        if (!$day_menu || !$day_menu['day_active']) {
            return $fallback;
        }

        $output = '';

        if ($include_title) {
            $day_title = ucfirst($day_menu['day_name']);
            if (!empty($day_menu['day_date'])) {
                $day_title .= ' (' . date('d/m/Y', strtotime($day_menu['day_date'])) . ')';
            }
            $output .= '<' . $title_format . ' class="menu-day-title">' . esc_html($day_title) . '</' . $title_format . '>';
        }

        if (!empty($day_menu['special_note']) && $show_special_notes) {
            $output .= '<div class="menu-special-note">' . esc_html($day_menu['special_note']) . '</div>';
        } else if (!empty($day_menu['menu_content'])) {
            $output .= '<div class="menu-day-content">' . wp_kses_post($day_menu['menu_content']) . '</div>';
        }

        return $output;
    }

    private function render_all_days($menu_id, $show_special_notes) {
        $menu_days = get_field('menu_days', $menu_id);
        
        if (!$menu_days) {
            return __('No hay días configurados para este menú.', 'lamusa-core');
        }

        $output = '<div class="menu-all-days">';

        foreach ($menu_days as $day) {
            if (!$day['day_active']) {
                continue;
            }

            $output .= '<div class="menu-day-item">';
            
            // Título del día
            $day_title = ucfirst($day['day_name']);
            if (!empty($day['day_date'])) {
                $day_title .= ' (' . date('d/m/Y', strtotime($day['day_date'])) . ')';
            }
            $output .= '<h4 class="menu-day-title">' . esc_html($day_title) . '</h4>';

            // Contenido
            if (!empty($day['special_note']) && $show_special_notes) {
                $output .= '<div class="menu-special-note">' . esc_html($day['special_note']) . '</div>';
            } else if (!empty($day['menu_content'])) {
                $output .= '<div class="menu-day-content">' . wp_kses_post($day['menu_content']) . '</div>';
            }

            $output .= '</div>';
        }

        $output .= '</div>';

        return $output;
    }
}
