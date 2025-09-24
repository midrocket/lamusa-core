<?php
/**
 * Etiqueta Dinámica para Lista de Alérgenos
 */

if (!defined('ABSPATH')) {
    exit;
}

class LaMusaAllergenListTag extends \Elementor\Core\DynamicTags\Tag {

    public function get_name() {
        return 'lamusa_allergen_list';
    }

    public function get_title() {
        return __('Lista de Alérgenos del Menú', 'lamusa-core');
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
            'allergen_type',
            [
                'label' => __('Tipo de Alérgenos', 'lamusa-core'),
                'type' => \Elementor\Controls_Manager::SELECT,
                'options' => [
                    'contains' => __('Contiene (definitivamente)', 'lamusa-core'),
                    'traces' => __('Puede contener trazas', 'lamusa-core'),
                    'both' => __('Ambos tipos', 'lamusa-core'),
                ],
                'default' => 'both',
            ]
        );

        $this->add_control(
            'display_format',
            [
                'label' => __('Formato de Visualización', 'lamusa-core'),
                'type' => \Elementor\Controls_Manager::SELECT,
                'options' => [
                    'list' => __('Lista separada por comas', 'lamusa-core'),
                    'icons' => __('Solo iconos', 'lamusa-core'),
                    'icons_names' => __('Iconos + nombres', 'lamusa-core'),
                    'html_list' => __('Lista HTML', 'lamusa-core'),
                ],
                'default' => 'icons_names',
            ]
        );

        $this->add_control(
            'show_prefix',
            [
                'label' => __('Mostrar Prefijo', 'lamusa-core'),
                'type' => \Elementor\Controls_Manager::SWITCHER,
                'label_on' => __('Sí', 'lamusa-core'),
                'label_off' => __('No', 'lamusa-core'),
                'return_value' => 'yes',
                'default' => 'yes',
                'description' => __('Mostrar texto como "Contiene:" o "Puede contener trazas de:"', 'lamusa-core'),
            ]
        );

        $this->add_control(
            'separator',
            [
                'label' => __('Separador', 'lamusa-core'),
                'type' => \Elementor\Controls_Manager::TEXT,
                'default' => ', ',
                'condition' => [
                    'display_format' => 'list',
                ],
            ]
        );
    }

    public function render() {
        $settings = $this->get_settings();
        $restaurant_id = $settings['restaurant_id'];
        $allergen_type = $settings['allergen_type'];
        $display_format = $settings['display_format'];
        $show_prefix = $settings['show_prefix'] === 'yes';
        $separator = $settings['separator'] ?: ', ';

        if (!$restaurant_id) {
            echo '';
            return;
        }

        if (!function_exists('lamusa_get_active_weekly_menu') || !function_exists('lamusa_get_menu_allergens_summary')) {
            echo '';
            return;
        }
        
        $menu = lamusa_get_active_weekly_menu($restaurant_id);
        
        if (!$menu) {
            echo '';
            return;
        }

        $allergen_summary = lamusa_get_menu_allergens_summary($menu->ID);
        
        $output = '';
        
        if ($allergen_type === 'contains' || $allergen_type === 'both') {
            if (!empty($allergen_summary['contains'])) {
                if ($show_prefix) {
                    $output .= '<span class="allergen-prefix-contains">' . __('Contiene:', 'lamusa-core') . ' </span>';
                }
                $output .= $this->format_allergen_list($allergen_summary['contains'], $display_format, $separator, 'contains');
            }
        }
        
        if ($allergen_type === 'traces' || $allergen_type === 'both') {
            if (!empty($allergen_summary['traces'])) {
                if ($output && $allergen_type === 'both') {
                    $output .= '<br>';
                }
                if ($show_prefix) {
                    $output .= '<span class="allergen-prefix-traces">' . __('Puede contener trazas de:', 'lamusa-core') . ' </span>';
                }
                $output .= $this->format_allergen_list($allergen_summary['traces'], $display_format, $separator, 'traces');
            }
        }

        echo $output;
    }

    private function format_allergen_list($allergens, $format, $separator, $type) {
        if (empty($allergens)) {
            return '';
        }

        $output = '';
        $items = array();

        foreach ($allergens as $allergen) {
            $icon = get_term_meta($allergen->term_id, 'allergen_icon', true);
            $color = get_term_meta($allergen->term_id, 'allergen_color', true);

            switch ($format) {
                case 'list':
                    $items[] = esc_html($allergen->name);
                    break;

                case 'icons':
                    if ($icon) {
                        $items[] = '<i class="' . esc_attr($icon) . '" style="color: ' . esc_attr($color) . ';" title="' . esc_attr($allergen->name) . '"></i>';
                    }
                    break;

                case 'icons_names':
                    $item = '<span class="allergen-item allergen-' . $type . '" style="color: ' . esc_attr($color) . ';">';
                    if ($icon) {
                        $item .= '<i class="' . esc_attr($icon) . '"></i> ';
                    }
                    $item .= esc_html($allergen->name);
                    $item .= '</span>';
                    $items[] = $item;
                    break;

                case 'html_list':
                    $item = '<li class="allergen-item allergen-' . $type . '" style="color: ' . esc_attr($color) . ';">';
                    if ($icon) {
                        $item .= '<i class="' . esc_attr($icon) . '"></i> ';
                    }
                    $item .= esc_html($allergen->name);
                    $item .= '</li>';
                    $items[] = $item;
                    break;
            }
        }

        if ($format === 'html_list') {
            $output = '<ul class="allergen-list allergen-list-' . $type . '">' . implode('', $items) . '</ul>';
        } else {
            $output = implode($separator, $items);
        }

        return $output;
    }
}
