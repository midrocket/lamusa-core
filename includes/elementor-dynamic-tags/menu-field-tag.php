<?php
/**
 * Etiqueta Dinámica para Campos de Menú Semanal
 */

if (!defined('ABSPATH')) {
    exit;
}

class LaMusaMenuFieldTag extends \Elementor\Core\DynamicTags\Tag {

    public function get_name() {
        return 'lamusa_menu_field';
    }

    public function get_title() {
        return __('Campo de Menú Semanal', 'lamusa-core');
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
            'field',
            [
                'label' => __('Campo del Menú', 'lamusa-core'),
                'type' => \Elementor\Controls_Manager::SELECT,
                'options' => [
                    'menu_title' => __('Título del Menú', 'lamusa-core'),
                    'menu_price' => __('Precio del Menú', 'lamusa-core'),
                    'menu_introduction' => __('Introducción del Menú', 'lamusa-core'),
                    'start_date' => __('Fecha de Inicio', 'lamusa-core'),
                    'end_date' => __('Fecha de Fin', 'lamusa-core'),
                    'date_range' => __('Rango de Fechas', 'lamusa-core'),
                    'today_menu' => __('Menú de Hoy', 'lamusa-core'),
                    'today_day_name' => __('Nombre del Día de Hoy', 'lamusa-core'),
                ],
                'default' => 'menu_title',
            ]
        );

        $this->add_control(
            'day_specific',
            [
                'label' => __('Día Específico', 'lamusa-core'),
                'type' => \Elementor\Controls_Manager::SELECT,
                'options' => [
                    '' => __('Automático (hoy)', 'lamusa-core'),
                    'lunes' => __('Lunes', 'lamusa-core'),
                    'martes' => __('Martes', 'lamusa-core'),
                    'miercoles' => __('Miércoles', 'lamusa-core'),
                    'jueves' => __('Jueves', 'lamusa-core'),
                    'viernes' => __('Viernes', 'lamusa-core'),
                    'sabado' => __('Sábado', 'lamusa-core'),
                    'domingo' => __('Domingo', 'lamusa-core'),
                ],
                'default' => '',
                'condition' => [
                    'field' => ['today_menu', 'today_day_name'],
                ],
            ]
        );

        $this->add_control(
            'date_format',
            [
                'label' => __('Formato de Fecha', 'lamusa-core'),
                'type' => \Elementor\Controls_Manager::TEXT,
                'default' => 'd/m/Y',
                'description' => __('Formato PHP para fechas (ej: d/m/Y, j F Y)', 'lamusa-core'),
                'condition' => [
                    'field' => ['start_date', 'end_date', 'date_range'],
                ],
            ]
        );

        $this->add_control(
            'menu_fallback',
            [
                'label' => __('Texto de Respaldo', 'lamusa-core'),
                'type' => \Elementor\Controls_Manager::TEXT,
                'description' => __('Texto a mostrar si no hay datos disponibles', 'lamusa-core'),
                'default' => '',
            ]
        );
    }

    public function render() {
        $settings = $this->get_settings();
        $restaurant_id = $settings['restaurant_id'];
        $field = $settings['field'];
        $day_specific = $settings['day_specific'];
        $date_format = $settings['date_format'] ?: 'd/m/Y';
        $fallback = $settings['menu_fallback'];

        if (!$restaurant_id) {
            echo $fallback;
            return;
        }

        if (!function_exists('lamusa_get_active_weekly_menu')) {
            echo $fallback;
            return;
        }
        
        $menu = lamusa_get_active_weekly_menu($restaurant_id);
        
        if (!$menu) {
            echo $fallback;
            return;
        }

        $value = '';

        switch ($field) {
            case 'menu_title':
                $value = $menu->post_title;
                break;
                
            case 'menu_price':
                $value = get_field('menu_price', $menu->ID);
                break;
                
            case 'menu_introduction':
                $value = get_field('menu_introduction', $menu->ID);
                break;
                
            case 'start_date':
                $start_date = get_field('start_date', $menu->ID);
                if ($start_date) {
                    $value = date($date_format, strtotime($start_date));
                }
                break;
                
            case 'end_date':
                $end_date = get_field('end_date', $menu->ID);
                if ($end_date) {
                    $value = date($date_format, strtotime($end_date));
                }
                break;
                
            case 'date_range':
                $start_date = get_field('start_date', $menu->ID);
                $end_date = get_field('end_date', $menu->ID);
                if ($start_date && $end_date) {
                    $value = sprintf(
                        __('Del %s al %s', 'lamusa-core'),
                        date($date_format, strtotime($start_date)),
                        date($date_format, strtotime($end_date))
                    );
                }
                break;
                
            case 'today_menu':
                $day_name = $day_specific ?: null;
                $day_menu = function_exists('lamusa_get_menu_day') ? lamusa_get_menu_day($menu->ID, $day_name) : null;
                
                if ($day_menu && $day_menu['day_active']) {
                    if (!empty($day_menu['special_note'])) {
                        $value = $day_menu['special_note'];
                    } else {
                        $value = wp_strip_all_tags($day_menu['menu_content']);
                    }
                }
                break;
                
            case 'today_day_name':
                $day_name = $day_specific ?: null;
                $day_menu = function_exists('lamusa_get_menu_day') ? lamusa_get_menu_day($menu->ID, $day_name) : null;
                
                if ($day_menu) {
                    $value = ucfirst($day_menu['day_name']);
                }
                break;
        }

        if (empty($value)) {
            $value = $fallback;
        }

        // Aplicar escape apropiado según el tipo de campo
        if ($field === 'menu_introduction') {
            echo wp_kses_post($value);
        } else {
            echo esc_html($value);
        }
    }
}
