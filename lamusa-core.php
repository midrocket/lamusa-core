<?php
/**
 * Plugin Name: La Musa Core
 * Plugin URI: https://lamusa.es
 * Description: Plugin para gestión de restaurantes, menús semanales y alérgenos para el grupo La Musa
 * Version: 1.1.0
 * Author: Eduardo MC
 * Text Domain: lamusa-core
 * Domain Path: /languages
 */

// Prevenir acceso directo
if (!defined('ABSPATH')) {
    exit;
}

// Definir constantes del plugin
define('LAMUSA_CORE_VERSION', '1.1.0');
define('LAMUSA_CORE_PLUGIN_DIR', plugin_dir_path(__FILE__));
define('LAMUSA_CORE_PLUGIN_URL', plugin_dir_url(__FILE__));
define('LAMUSA_CORE_PLUGIN_FILE', __FILE__);

/**
 * Clase principal del plugin La Musa Core
 */
class LaMusaCore {
    
    /**
     * Instancia única del plugin
     */
    private static $instance = null;
    
    /**
     * Obtener instancia única
     */
    public static function get_instance() {
        if (null === self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    /**
     * Constructor privado
     */
    private function __construct() {
        add_action('plugins_loaded', array($this, 'init'));
        register_activation_hook(__FILE__, array($this, 'activate'));
        register_deactivation_hook(__FILE__, array($this, 'deactivate'));
    }
    
    /**
     * Inicializar el plugin
     */
    public function init() {
        // Verificar que ACF esté activo
        if (!class_exists('ACF')) {
            add_action('admin_notices', array($this, 'acf_missing_notice'));
            return;
        }
        
        // Cargar archivos del plugin
        $this->load_includes();
        
        // Inicializar componentes
        add_action('init', array($this, 'init_post_types'));
        add_action('init', array($this, 'init_taxonomies'));
        add_action('acf/include_fields', array($this, 'init_acf_fields'));
        
        // Cargar scripts y estilos
        add_action('wp_enqueue_scripts', array($this, 'enqueue_scripts'));
        add_action('admin_enqueue_scripts', array($this, 'enqueue_admin_scripts'));
        
    }
    
    /**
     * Cargar archivos del plugin
     */
    private function load_includes() {
        $includes = array(
            'includes/post-types.php',
            'includes/taxonomies.php',
            'includes/acf-fields.php',
            'includes/elementor-integration.php',
            'includes/admin-functions.php',
            'includes/helper-functions.php',
            'includes/ajax-functions.php',
            'includes/rewrite-rules.php',
            'includes/shortcodes.php',
            'includes/admin-help-page.php',
            'includes/admin-settings-page.php'
        );
        
        foreach ($includes as $file) {
            $filepath = LAMUSA_CORE_PLUGIN_DIR . $file;
            if (file_exists($filepath)) {
                require_once $filepath;
            }
        }
    }
    
    /**
     * Inicializar post types
     */
    public function init_post_types() {
        if (function_exists('lamusa_register_post_types')) {
            lamusa_register_post_types();
        }
    }
    
    /**
     * Inicializar taxonomías
     */
    public function init_taxonomies() {
        if (function_exists('lamusa_register_taxonomies')) {
            lamusa_register_taxonomies();
        }
    }
    
    /**
     * Inicializar campos ACF
     */
    public function init_acf_fields() {
        if (function_exists('lamusa_register_acf_fields')) {
            lamusa_register_acf_fields();
        }
    }
    
    /**
     * Cargar scripts frontend
     */
    public function enqueue_scripts() {
        wp_enqueue_style(
            'lamusa-core-frontend',
            LAMUSA_CORE_PLUGIN_URL . 'assets/css/frontend.css',
            array(),
            LAMUSA_CORE_VERSION
        );
        
        wp_enqueue_style(
            'lamusa-menu-navigator',
            LAMUSA_CORE_PLUGIN_URL . 'assets/css/menu-navigator.css',
            array(),
            LAMUSA_CORE_VERSION
        );
        
        wp_enqueue_style(
            'lamusa-shortcodes',
            LAMUSA_CORE_PLUGIN_URL . 'assets/css/shortcodes.css',
            array(),
            LAMUSA_CORE_VERSION
        );
        
        wp_enqueue_script(
            'lamusa-core-frontend',
            LAMUSA_CORE_PLUGIN_URL . 'assets/js/frontend.js',
            array('jquery'),
            LAMUSA_CORE_VERSION,
            true
        );
        
        wp_enqueue_script(
            'lamusa-menu-navigator',
            LAMUSA_CORE_PLUGIN_URL . 'assets/js/menu-navigator.js',
            array('jquery'),
            LAMUSA_CORE_VERSION,
            true
        );
        
        wp_enqueue_script(
            'lamusa-mobile-menu',
            LAMUSA_CORE_PLUGIN_URL . 'assets/js/mobile-menu.js',
            array('jquery'),
            LAMUSA_CORE_VERSION,
            true
        );
    }
    
    /**
     * Cargar scripts admin
     */
    public function enqueue_admin_scripts() {
        wp_enqueue_style(
            'lamusa-core-admin',
            LAMUSA_CORE_PLUGIN_URL . 'assets/css/admin.css',
            array(),
            LAMUSA_CORE_VERSION
        );
        
        wp_enqueue_script(
            'lamusa-core-admin',
            LAMUSA_CORE_PLUGIN_URL . 'assets/js/admin.js',
            array('jquery'),
            LAMUSA_CORE_VERSION,
            true
        );
    }
    
    /**
     * Activación del plugin
     */
    public function activate() {
        // Flush rewrite rules
        flush_rewrite_rules();
    }
    
    /**
     * Desactivación del plugin
     */
    public function deactivate() {
        // Flush rewrite rules
        flush_rewrite_rules();
    }
    
    
    /**
     * Aviso si ACF no está disponible
     */
    public function acf_missing_notice() {
        ?>
        <div class="notice notice-error">
            <p><?php _e('El plugin La Musa Core requiere Advanced Custom Fields (ACF) para funcionar correctamente.', 'lamusa-core'); ?></p>
        </div>
        <?php
    }
}

// Inicializar el plugin
LaMusaCore::get_instance();