<?php
if (!defined('ABSPATH')) exit;
class DLWA_Ajax {
  public static function init(){
    add_action('wp_ajax_dlwa_get_cart',[__CLASS__,'get_cart']);
    add_action('wp_ajax_nopriv_dlwa_get_cart',[__CLASS__,'get_cart']);
    add_action('wp_ajax_dlwa_update_qty',[__CLASS__,'update_qty']);
    add_action('wp_ajax_nopriv_dlwa_update_qty',[__CLASS__,'update_qty']);
    add_action('wp_ajax_dlwa_remove_item',[__CLASS__,'remove_item']);
    add_action('wp_ajax_nopriv_dlwa_remove_item',[__CLASS__,'remove_item']);
    add_action('wp_ajax_dlwa_build_whatsapp',[__CLASS__,'build_whatsapp']);
    add_action('wp_ajax_nopriv_dlwa_build_whatsapp',[__CLASS__,'build_whatsapp']);
  }
  private static function verify(){
    if ( ! isset($_POST['nonce']) || ! wp_verify_nonce($_POST['nonce'],'dlwa_nonce') ){
      wp_send_json_error(['message'=>'Bad nonce'],403);
    }
  }
  private static function ok_wc(){ return function_exists('WC') && WC() && WC()->cart; }
  private static function format_cart(){
    if ( ! self::ok_wc() ) return ['items'=>[],'subtotal_fmt'=>'—','total_fmt'=>'—','subtotal'=>0,'total'=>0,'count'=>0,'currency'=>get_woocommerce_currency(),'currency_symbol'=>get_woocommerce_currency_symbol(),'thousand_sep'=>wc_get_price_thousand_separator(),'decimal_sep'=>wc_get_price_decimal_separator(),'decimals'=>wc_get_price_decimals(),'price_format'=>get_woocommerce_price_format(),'cart_hash'=>''];
    $items=[];
    foreach ( WC()->cart->get_cart() as $key=>$item ){
      $product = isset($item['data']) ? $item['data'] : false; if(!$product) continue;
      $name=$product->get_name();
      if ( $product->is_type('variation') ){ $attrs=wc_get_formatted_variation($product,true); if($attrs){ $name.=' '.wp_strip_all_tags($attrs);} }
      $qty=(int)$item['quantity']; $line=isset($item['line_total'])?(float)$item['line_total']:0.0; $tax=isset($item['line_tax'])?(float)$item['line_tax']:0.0;
      $img=wp_get_attachment_image_url($product->get_image_id(),'thumbnail'); if(!$img){ $img=function_exists('wc_placeholder_img_src')?wc_placeholder_img_src():includes_url('images/media/default.png'); }
      $items[]=['key'=>$key,'name'=>wp_strip_all_tags($name),'qty'=>$qty,'line_total_fmt'=>wp_strip_all_tags(wc_price($line+$tax)),'image'=>esc_url_raw($img)];
    }
    $totals = WC()->cart->get_totals();
    $subtotal_val = ( isset($totals['subtotal']) ? (float)$totals['subtotal'] : 0 ) + ( isset($totals['subtotal_tax']) ? (float)$totals['subtotal_tax'] : 0 );
    $total_val = $subtotal_val; // no shipping cost
    return ['items'=>$items,'subtotal_fmt'=>wp_strip_all_tags(wc_price($subtotal_val)),'total_fmt'=>wp_strip_all_tags(wc_price($total_val)),'subtotal'=>$subtotal_val,'total'=>$total_val,'count'=>WC()->cart->get_cart_contents_count(),'currency'=>get_woocommerce_currency(),'currency_symbol'=>get_woocommerce_currency_symbol(),'thousand_sep'=>wc_get_price_thousand_separator(),'decimal_sep'=>wc_get_price_decimal_separator(),'decimals'=>wc_get_price_decimals(),'price_format'=>get_woocommerce_price_format(),'cart_hash'=>WC()->cart->get_cart_hash()];
  }
  public static function get_cart(){ self::verify(); wp_send_json_success(self::format_cart()); }
  public static function update_qty(){ self::verify(); if(!self::ok_wc()) wp_send_json_error(['message'=>'WooCommerce inactive'],400);
    $key=isset($_POST['cart_item_key'])?wc_clean(wp_unslash($_POST['cart_item_key'])):''; $qty=isset($_POST['qty'])?intval($_POST['qty']):0; if($key===''||$qty<0) wp_send_json_error(['message'=>'Bad params'],400);
    if($qty===0) WC()->cart->remove_cart_item($key); else WC()->cart->set_quantity($key,$qty,true);
    WC()->cart->calculate_totals(); wp_send_json_success(self::format_cart()); }
  public static function remove_item(){ self::verify(); if(!self::ok_wc()) wp_send_json_error(['message'=>'WooCommerce inactive'],400);
    $key=isset($_POST['cart_item_key'])?wc_clean(wp_unslash($_POST['cart_item_key'])):''; if($key==='') wp_send_json_error(['message'=>'Bad params'],400);
    WC()->cart->remove_cart_item($key); WC()->cart->calculate_totals(); wp_send_json_success(self::format_cart()); }
  public static function build_whatsapp(){ self::verify();
    $fields=['name'=>isset($_POST['name'])?sanitize_text_field(wp_unslash($_POST['name'])):'','phone'=>isset($_POST['phone'])?sanitize_text_field(wp_unslash($_POST['phone'])):'','email'=>isset($_POST['email'])?sanitize_email(wp_unslash($_POST['email'])):'','region'=>isset($_POST['region'])?sanitize_text_field(wp_unslash($_POST['region'])):'','address'=>isset($_POST['address'])?sanitize_text_field(wp_unslash($_POST['address'])):'','note'=>isset($_POST['note'])?sanitize_textarea_field(wp_unslash($_POST['note'])):''];
    $phone_dst=get_option('dlwa_phone_e164','+50671332495'); $url=DLWA_Message::build_url($phone_dst,$fields); wp_send_json_success(['url'=>$url]); }
}