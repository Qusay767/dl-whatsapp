<?php
/**
 * Plugin Name: DL WhatsApp Replace Checkout
 * Plugin URI: https://github.com/your-username/dl-whatsapp-checkout
 * Description: Replace WooCommerce checkout with WhatsApp ordering system. Includes floating cart, analytics, and modern UX features.
 * Version: 2.1.0
 * Author: Diva Latina
 * Author URI: https://divalatina.com
 * Text Domain: dl-wa-checkout
 * Domain Path: /languages
 * Requires at least: 5.0
 * Tested up to: 6.4
 * Requires PHP: 7.4
 * WC requires at least: 5.0
 * WC tested up to: 8.0
 * License: GPL v2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

// Define plugin constants
define('DLWA_VERSION', '2.1.0');
define('DLWA_FILE', __FILE__);
define('DLWA_DIR', plugin_dir_path(__FILE__));
define('DLWA_URL', plugin_dir_url(__FILE__));
define('DLWA_BASENAME', plugin_basename(__FILE__));

/**
 * Main plugin class
 */
final class DL_WhatsApp_Checkout {
    
    /**
     * Plugin instance
     */
    private static $instance = null;
    
    /**
     * Get plugin instance
     */
    public static function instance() {
        if (null === self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    /**
     * Constructor
     */
    private function __construct() {
        $this->init_hooks();
    }
    
    /**
     * Initialize hooks
     */
    private function init_hooks() {
        add_action('plugins_loaded', [$this, 'init']);
        add_action('init', [$this, 'load_textdomain']);
        
        // Activation/Deactivation hooks
        register_activation_hook(__FILE__, [$this, 'activate']);
        register_deactivation_hook(__FILE__, [$this, 'deactivate']);
    }
    
    /**
     * Initialize plugin
     */
    public function init() {
        // Check if WooCommerce is active
        if (!$this->is_woocommerce_active()) {
            add_action('admin_notices', [$this, 'woocommerce_missing_notice']);
            return;
        }
        
        $this->load_classes();
        $this->init_classes();
    }
    
    /**
     * Load plugin classes
     */
    private function load_classes() {
        $classes = [
            'DLWA_Admin',
            'DLWA_Ajax',
            'DLWA_Assets',
            'DLWA_Floating_Cart',
            'DLWA_Message',
            'DLWA_Plugin',
            'DLWA_Shortcode',
            'DLWA_Analytics',
            'DLWA_Notifications'
        ];
        
        foreach ($classes as $class) {
            $file = DLWA_DIR . 'includes/class-' . strtolower(str_replace('_', '-', $class)) . '.php';
            if (file_exists($file)) {
                require_once $file;
            }
        }
    }
    
    /**
     * Initialize plugin classes
     */
    private function init_classes() {
        if (is_admin()) {
            DLWA_Admin::init();
        }
        
        DLWA_Ajax::init();
        DLWA_Assets::init();
        DLWA_Floating_Cart::init();
        DLWA_Message::init();
        DLWA_Plugin::init();
        DLWA_Shortcode::init();
        DLWA_Analytics::init();
        DLWA_Notifications::init();
    }
    
    /**
     * Load plugin textdomain
     */
    public function load_textdomain() {
        load_plugin_textdomain('dl-wa-checkout', false, dirname(DLWA_BASENAME) . '/languages');
    }
    
    /**
     * Check if WooCommerce is active
     */
    private function is_woocommerce_active() {
        return class_exists('WooCommerce');
    }
    
    /**
     * WooCommerce missing notice
     */
    public function woocommerce_missing_notice() {
        ?>
        <div class="notice notice-error">
            <p><?php esc_html_e('DL WhatsApp Replace Checkout requires WooCommerce to be installed and active.', 'dl-wa-checkout'); ?></p>
        </div>
        <?php
    }
    
    /**
     * Plugin activation
     */
    public function activate() {
        // Set default options
        $defaults = [
            'dlwa_phone_e164' => '',
            'dlwa_enable_floating' => 1,
            'dlwa_safe_mode' => 0,
            'dlwa_shipping_text' => '📦 El envío se acuerda directamente con el proveedor',
            'dlwa_btn_color' => '#F48FB1',
            'dlwa_fab_color' => '#C8A34E',
            'dlwa_enable_analytics' => 1,
            'dlwa_enable_notifications' => 1,
            'dlwa_thank_you_message' => '¡Gracias por tu pedido! Te contactaremos pronto.',
            'dlwa_min_order_amount' => 0,
            'dlwa_business_hours' => ''
        ];
        
        foreach ($defaults as $option => $value) {
            if (get_option($option) === false) {
                add_option($option, $value);
            }
        }
        
        // Create analytics table
        if (class_exists('DLWA_Analytics')) {
            DLWA_Analytics::create_tables();
        }
        
        // Clear any cached data
        wp_cache_flush();
    }
    
    /**
     * Plugin deactivation
     */
    public function deactivate() {
        // Clear any cached data
        wp_cache_flush();
    }
}

/**
 * Initialize the plugin
 */
function dlwa_init() {
    return DL_WhatsApp_Checkout::instance();
}

// Start the plugin
dlwa_init();

/**
 * Helper functions
 */

/**
 * Get plugin option with default
 */
function dlwa_get_option($option, $default = '') {
    return get_option('dlwa_' . $option, $default);
}

/**
 * Check if floating cart is enabled
 */
function dlwa_is_floating_enabled() {
    return dlwa_get_option('enable_floating', 1) && !dlwa_get_option('safe_mode', 0);
}

/**
 * Get WhatsApp phone number
 */
function dlwa_get_phone() {
    return dlwa_get_option('phone_e164', '');
}

/**
 * Format price for display
 */
function dlwa_format_price($amount) {
    if (!function_exists('wc_price')) {
        return '$' . number_format($amount, 2);
    }
    return wp_strip_all_tags(wc_price($amount));
}