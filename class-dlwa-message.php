<?php
if (!defined('ABSPATH')) exit;
class DLWA_Message {
  public static function init(){}
  public static function build_url($phone_e164,$fields=[]) {
    $msg=self::build_message($fields);
    return esc_url_raw('https://wa.me/'.rawurlencode($phone_e164).'?text='.rawurlencode($msg));
  }
  private static function cr_price($amount){
    $amount = (float)$amount;
    $formatted = number_format($amount, 0, '.', ',');
    return '₡'.$formatted;
  }
  public static function build_message($fields=[]) {
    $lines=[];
    if(!empty($fields['name']))   $lines[]='👤 *Cliente*: '.$fields['name'];
    if(!empty($fields['phone']))  $lines[]='📞 *Teléfono*: '.$fields['phone'];
    if(!empty($fields['email']))  $lines[]='✉️ *Email*: '.$fields['email'];
    if(!empty($fields['region'])) $lines[]='📍 *Región*: '.$fields['region'];
    if(!empty($fields['address']))$lines[]='🏠 *Dirección*: '.$fields['address'];
    $lines[]='';
    $lines[]='🧾 *Detalle del pedido*:';
    $lines[]='';
    if ( function_exists('WC') && WC() && WC()->cart ){
      foreach ( WC()->cart->get_cart() as $item ){
        $product = isset($item['data']) ? $item['data'] : false; if(!$product) continue;
        $name=$product->get_name();
        if ( $product->is_type('variation') ){ $attrs=wc_get_formatted_variation($product,true); if($attrs){ $name.=' '.wp_strip_all_tags($attrs);} }
        $qty=(int)$item['quantity']; $line=isset($item['line_total'])?(float)$item['line_total']:0.0; $tax=isset($item['line_tax'])?(float)$item['line_tax']:0.0;
        $lines[]='• '.wp_strip_all_tags($name).' ×'.$qty.' — '.self::cr_price($line+$tax);
      }
      $totals=WC()->cart->get_totals();
      $subtotal=( isset($totals['subtotal']) ? (float)$totals['subtotal'] : 0 ) + ( isset($totals['subtotal_tax']) ? (float)$totals['subtotal_tax'] : 0 );
      $total=$subtotal;
      $lines[]='';
      $lines[]='*Subtotal*: '.self::cr_price($subtotal);
      $shipping_text=get_option('dlwa_shipping_text','📦 El envío se acuerda directamente con el proveedor'); if($shipping_text!=='') $lines[]=$shipping_text;
      $lines[]='*Total*: '.self::cr_price($total);
    }
    if(!empty($fields['note'])){ $lines[]=''; $lines[]='📝 *Nota*: '.$fields['note']; }
    $lines[]=''; $lines[]='✨ Gracias por comprar en Diva Latina. ¡Tu estilo, tu esencia! 💖';
    return implode("\n",$lines);
  }
}
