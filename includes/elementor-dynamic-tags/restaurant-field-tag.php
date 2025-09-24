<?php
/**
 * Etiqueta Dinámica para Campos de Restaurante
 */

if (!defined('ABSPATH')) {
    exit;
}

class LaMusaRestaurantFieldTag extends \Elementor\Core\DynamicTags\Tag {

    public function get_name() {
        return 'lamusa_restaurant_field';
    }

    public function get_title() {
        return __('Campo de Restaurante', 'lamusa-core');
    }

    public function get_group() {
        return 'lamusa-core';
    }

    public function get_categories() {
        return [
            \Elementor\Modules\DynamicTags\Module::TEXT_CATEGORY,
            \Elementor\Modules\DynamicTags\Module::URL_CATEGORY,
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
                'label' => __('Campo', 'lamusa-core'),
                'type' => \Elementor\Controls_Manager::SELECT,
                'options' => [
                    'title' => __('Título del Post (gestión interna)', 'lamusa-core'),
                    'restaurant_title' => __('Título Principal (ej: Limbo)', 'lamusa-core'),
                    'restaurant_title_secondary' => __('Título Secundario (ej: Tribunal)', 'lamusa-core'),
                    'restaurant_subtitle' => __('Subtítulo Principal (ej: Pollo al fuego y buen)', 'lamusa-core'),
                    'restaurant_subtitle_secondary' => __('Subtítulo Secundario (ej: HUMO)', 'lamusa-core'),
                    'address' => __('Dirección', 'lamusa-core'),
                    'phone' => __('Teléfono', 'lamusa-core'),
                    'opening_hours' => __('Horario', 'lamusa-core'),
                    'description' => __('Descripción', 'lamusa-core'),
                    'url_breakfast' => __('URL Desayunos', 'lamusa-core'),
                    'url_menu' => __('URL Carta', 'lamusa-core'),
                    'url_drinks' => __('URL Bebidas', 'lamusa-core'),
                    'url_cocktails' => __('URL Cócteles', 'lamusa-core'),
                    'url_dessert' => __('URL Postres', 'lamusa-core'),
                    'url_dish_week' => __('URL Plato de la semana', 'lamusa-core'),
                    'url_take_away' => __('URL Take away', 'lamusa-core'),
                    'url_booking' => __('URL Reserva', 'lamusa-core'),
                    'url_nights' => __('URL Noches/Eventos', 'lamusa-core'),
                    'url_djs_program' => __('URL DJ\'s & Program', 'lamusa-core'),
                    'url_about' => __('URL About', 'lamusa-core'),
                ],
                'default' => 'restaurant_title',
            ]
        );

        $this->add_control(
            'fallback',
            [
                'label' => __('Texto de Respaldo', 'lamusa-core'),
                'type' => \Elementor\Controls_Manager::TEXT,
                'description' => __('Texto a mostrar si el campo está vacío', 'lamusa-core'),
                'default' => '',
            ]
        );
    }

    public function render() {
        $settings = $this->get_settings();
        $restaurant_id = $settings['restaurant_id'];
        $field = $settings['field'];
        $fallback = $settings['fallback'];

        if (!$restaurant_id) {
            echo $fallback;
            return;
        }

        $restaurant = get_post($restaurant_id);
        if (!$restaurant) {
            echo $fallback;
            return;
        }

        $value = '';

        switch ($field) {
            case 'title':
                $value = $restaurant->post_title;
                break;
                
            case 'restaurant_title':
            case 'restaurant_title_secondary':
            case 'restaurant_subtitle':
            case 'restaurant_subtitle_secondary':
            case 'address':
            case 'phone':
            case 'opening_hours':
            case 'description':
                $value = get_field($field, $restaurant_id);
                break;
                
            default:
                // Para URLs y otros campos del grupo restaurant_urls
                if (strpos($field, 'url_') === 0) {
                    $restaurant_urls = get_field('restaurant_urls', $restaurant_id);
                    $value = $restaurant_urls[$field] ?? '';
                }
                break;
        }

        if (empty($value)) {
            $value = $fallback;
        }

        // Para campos de URL, aplicar esc_url
        if (strpos($field, 'url_') === 0) {
            echo esc_url($value);
        } else {
            // Para otros campos, aplicar el escape apropiado
            if ($field === 'opening_hours' || $field === 'description') {
                echo wp_kses_post($value);
            } else {
                echo esc_html($value);
            }
        }
    }
}
