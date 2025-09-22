<?php
if (!defined('ABSPATH')) exit;
class DLWA_Shortcode {
  public static function init(){
    add_shortcode('dlwa_checkout_whatsapp',[__CLASS__,'render']);
    add_shortcode('dl_wa_checkout',[__CLASS__,'render']);
    add_shortcode('dl_wa-checkout',[__CLASS__,'render']);
  }
  public static function render($atts=[],$content=''){
    wp_enqueue_style('dlwa-style');
    $btn=get_option('dlwa_btn_color','#F48FB1'); $fab=get_option('dlwa_fab_color','#C8A34E');
    wp_add_inline_style('dlwa-style',':root{--dlwa-btn:'.$btn.';--dlwa-fab:'.$fab.';}');
    wp_enqueue_script('dlwa-checkout');
    $local=[ 'ajax_url'=>admin_url('admin-ajax.php'), 'nonce'=>wp_create_nonce('dlwa_nonce'), 'phone'=>get_option('dlwa_phone_e164',''), 'shipping_text'=>get_option('dlwa_shipping_text',''), 'currency_symbol'=>get_woocommerce_currency_symbol(), 'thousand_sep'=>wc_get_price_thousand_separator(), 'decimal_sep'=>wc_get_price_decimal_separator(), 'decimals'=>wc_get_price_decimals(), 'price_format'=>get_woocommerce_price_format(), ];
    wp_localize_script('dlwa-checkout','DLWA',$local);
    ob_start(); include DLWA_DIR.'templates/shortcode-checkout.php'; return ob_get_clean();
  }
}