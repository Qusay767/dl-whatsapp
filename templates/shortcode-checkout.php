<?php if ( ! defined( 'ABSPATH' ) ) exit; ?>
<div class="dlwa-wrap">
  <h2 class="dlwa-title"><?php echo esc_html__('Finalizar pedido (WhatsApp)','dl-wa-checkout'); ?></h2>
  <div id="dlwa-cart-list" class="dlwa-cart-list"></div>
  <div class="dlwa-summary">
    <div class="dlwa-row dlwa-shipping-note" id="dlwa-shipping-text"></div>
    <div class="dlwa-row dlwa-total"><span><?php esc_html_e('Total','dl-wa-checkout'); ?></span><strong id="dlwa-total">—</strong></div>
  </div>
  <form id="dlwa-form" class="dlwa-form">
    <div class="dlwa-grid">
      <label><span><?php esc_html_e('Nombre (opcional)','dl-wa-checkout'); ?></span><input type="text" name="name" /></label>
      <label><span><?php esc_html_e('Teléfono (opcional)','dl-wa-checkout'); ?></span><input type="text" name="phone" /></label>
      <label><span><?php esc_html_e('Email (opcional)','dl-wa-checkout'); ?></span><input type="email" name="email" /></label>
      <label><span><?php esc_html_e('Región (opcional)','dl-wa-checkout'); ?></span><input type="text" name="region" /></label>
      <label class="dlwa-grid-colspan"><span><?php esc_html_e('Dirección (opcional)','dl-wa-checkout'); ?></span><input type="text" name="address" /></label>
      <label class="dlwa-grid-colspan"><span><?php esc_html_e('Nota (opcional)','dl-wa-checkout'); ?></span><textarea name="note" rows="3"></textarea></label>
    </div>
    <button type="button" id="dlwa-whatsapp" class="dlwa-button-primary"><span><?php esc_html_e('Enviar por WhatsApp','dl-wa-checkout'); ?></span></button>
  </form>
</div>