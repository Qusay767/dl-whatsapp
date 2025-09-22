<?php
if (!defined('ABSPATH')) exit;
class DLWA_Assets {
  public static function init(){ add_action('wp_enqueue_scripts',[__CLASS__,'enqueue_public']); }
  public static function enqueue_public(){
    wp_register_style('dlwa-style', DLWA_URL.'assets/wa-checkout.css', [], DLWA_VERSION);
    wp_register_script('dlwa-checkout', DLWA_URL.'assets/wa-checkout.js', ['jquery'], DLWA_VERSION, true);
    wp_register_script('dlwa-floating', DLWA_URL.'assets/floating-cart.js', ['jquery'], DLWA_VERSION, true);

    $local = [
      'ajax_url'=>admin_url('admin-ajax.php'),
      'nonce'=>wp_create_nonce('dlwa_nonce'),
      'phone'=>get_option('dlwa_phone_e164',''),
      'shipping_text'=>get_option('dlwa_shipping_text',''),
      'thank_you_message'=>get_option('dlwa_thank_you_message','¡Gracias por tu pedido! Te contactaremos pronto.'),
      'min_order_amount'=>get_option('dlwa_min_order_amount',0),
      'business_hours'=>get_option('dlwa_business_hours',''),
      'enable_analytics'=>get_option('dlwa_enable_analytics',1),
      'enable_notifications'=>get_option('dlwa_enable_notifications',1),
      'empty_cart_text'=>__('Tu carrito está vacío.','dl-wa-checkout'),
      'whatsapp_button_text'=>__('Enviar por WhatsApp','dl-wa-checkout'),
      'currency_symbol'=>get_woocommerce_currency_symbol(),
      'thousand_sep'=>wc_get_price_thousand_separator(),
      'decimal_sep'=>wc_get_price_decimal_separator(),
      'decimals'=>wc_get_price_decimals(),
      'price_format'=>get_woocommerce_price_format(),
    ];

    global $post;
    $has_sc = is_a($post,'WP_Post') && ( has_shortcode($post->post_content,'dlwa_checkout_whatsapp') || has_shortcode($post->post_content,'dl_wa_checkout') || has_shortcode($post->post_content,'dl_wa-checkout') );
    if ( $has_sc ) {
      wp_enqueue_style('dlwa-style');
      $btn = get_option('dlwa_btn_color','#F48FB1'); $fab = get_option('dlwa_fab_color','#C8A34E');
      wp_add_inline_style('dlwa-style', ':root{--dlwa-btn:'.$btn.';--dlwa-fab:'.$fab.';}');
      wp_enqueue_script('dlwa-checkout'); wp_localize_script('dlwa-checkout','DLWA',$local);
    }

    if ( get_option('dlwa_enable_floating',1) && ! get_option('dlwa_safe_mode',1) ) {
      wp_enqueue_style('dlwa-style');
      $btn = get_option('dlwa_btn_color','#F48FB1'); $fab = get_option('dlwa_fab_color','#C8A34E');
      wp_add_inline_style('dlwa-style', ':root{--dlwa-btn:'.$btn.';--dlwa-fab:'.$fab.';}');
      wp_enqueue_script('dlwa-floating'); wp_localize_script('dlwa-floating','DLWA',$local);
      add_action('wp_footer',[ 'DLWA_Floating_Cart','render_shell' ] );
    }
  }
}