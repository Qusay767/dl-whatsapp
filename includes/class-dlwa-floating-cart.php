<?php
if (!defined('ABSPATH')) exit;
class DLWA_Floating_Cart {
  public static function init(){}
  public static function render_shell(){ ?>
  <div id="dlwa-fab" class="dlwa-fab" aria-label="<?php esc_attr_e('Cart','dl-wa-checkout'); ?>">
    <span class="dlwa-fab-badge" id="dlwa-fab-badge">0</span>
    <svg viewBox="0 0 24 24" width="24" height="24" aria-hidden="true"><path d="M7 4h-2l-1 2h2l3.6 7.59-1.35 2.45c-.16.28-.25.61-.25.96 0 1.1.9 2 2 2h9v-2h-8.42c-.14 0-.25-.11-.25-.25l.03-.12L12.1 15h5.45c.75 0 1.41-.41 1.75-1.03l3.58-6.49a1 1 0 0 0-.87-1.48H7.42l-.94-2zM7 20a2 2 0 1 0 0 4 2 2 0 0 0 0-4zm10 0a2 2 0 1 0 .001 3.999A2 2 0 0 0 17 20z"/></svg>
  </div>
  <div id="dlwa-drawer" class="dlwa-drawer">
    <div class="dlwa-drawer-header"><strong><?php esc_html_e('Cart','dl-wa-checkout'); ?></strong><button type="button" class="dlwa-drawer-close" aria-label="<?php esc_attr_e('Close','dl-wa-checkout'); ?>">×</button></div>
    <div class="dlwa-drawer-body" id="dlwa-drawer-body"><div class="dlwa-empty"><?php esc_html_e('Your cart is empty.','dl-wa-checkout'); ?></div></div>
    <div class="dlwa-drawer-footer"><div class="dlwa-totals"><span><?php esc_html_e('Total','dl-wa-checkout'); ?>:</span><strong id="dlwa-drawer-total">—</strong><div class="dlwa-drawer-note" id="dlwa-drawer-shipping"></div></div><div class="dlwa-actions" style="display:flex;gap:8px;"><button type="button" id="dlwa-drawer-wa" class="dlwa-button-primary" style="padding:10px 14px;"><?php esc_html_e('Enviar por WhatsApp','dl-wa-checkout'); ?></button></div></div>
  </div>
  <div id="dlwa-drawer-overlay" class="dlwa-drawer-overlay"></div>
  <?php }
}