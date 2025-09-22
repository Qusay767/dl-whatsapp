<?php
/**
 * Plugin Name:       DL WhatsApp Replace Checkout
 * Description:       Advanced WhatsApp checkout with floating cart, analytics, and enhanced UX. Safe Mode by default.
 * Version:           2.0.0
 * Author:            Diva Latina
 * Text Domain:       dl-wa-checkout
 * Domain Path:       /languages
 * Requires at least: 5.0
 * Tested up to:      6.4
 * Requires PHP:      7.4
 * WC requires at least: 4.0
 * WC tested up to:   8.0
 */
if ( ! defined( 'ABSPATH' ) ) { exit; }
define( 'DLWA_VERSION', '2.0.0' );
define( 'DLWA_FILE', __FILE__ );
define( 'DLWA_DIR', plugin_dir_path( __FILE__ ) );
define( 'DLWA_URL', plugin_dir_url( __FILE__ ) );

add_action( 'plugins_loaded', function(){
  load_plugin_textdomain( 'dl-wa-checkout', false, dirname( plugin_basename( __FILE__ ) ) . '/languages' );
});

require_once DLWA_DIR . 'includes/class-dlwa-plugin.php';
require_once DLWA_DIR . 'includes/class-dlwa-assets.php';
require_once DLWA_DIR . 'includes/class-dlwa-admin.php';
require_once DLWA_DIR . 'includes/class-dlwa-ajax.php';
require_once DLWA_DIR . 'includes/class-dlwa-message.php';
require_once DLWA_DIR . 'includes/class-dlwa-shortcode.php';
require_once DLWA_DIR . 'includes/class-dlwa-floating-cart.php';
require_once DLWA_DIR . 'includes/class-dlwa-analytics.php';
require_once DLWA_DIR . 'includes/class-dlwa-notifications.php';

DLWA_Plugin::init();
DLWA_Assets::init();
DLWA_Admin::init();
DLWA_Ajax::init();
DLWA_Message::init();
DLWA_Shortcode::init();
DLWA_Floating_Cart::init();
DLWA_Analytics::init();
DLWA_Notifications::init();

register_activation_hook( __FILE__, function(){
  add_option( 'dlwa_phone_e164', '+50671332495' );
  add_option( 'dlwa_enable_floating', 1 );
  add_option( 'dlwa_safe_mode', 1 );
  add_option( 'dlwa_shipping_text', '📦 El envío se acuerda directamente con el proveedor' );
  add_option( 'dlwa_btn_color', '#F48FB1' );
  add_option( 'dlwa_fab_color', '#C8A34E' );
  add_option( 'dlwa_enable_analytics', 1 );
  add_option( 'dlwa_enable_notifications', 1 );
  add_option( 'dlwa_custom_fields', '' );
  add_option( 'dlwa_thank_you_message', '¡Gracias por tu pedido! Te contactaremos pronto.' );
  add_option( 'dlwa_min_order_amount', 0 );
  add_option( 'dlwa_business_hours', '' );
});

// Keep customer on same page after add-to-cart (no redirect)
add_filter('woocommerce_add_to_cart_redirect', '__return_false', 99);
add_filter('woocommerce_cart_redirect_after_add', '__return_false', 99);

// Add custom body class when plugin is active
add_filter('body_class', function($classes) {
  if (get_option('dlwa_enable_floating', 1) && !get_option('dlwa_safe_mode', 1)) {
    $classes[] = 'dlwa-floating-enabled';
  }
  return $classes;
});
