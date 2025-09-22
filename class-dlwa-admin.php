<?php
if (!defined('ABSPATH')) exit;
class DLWA_Admin {
  public static function init(){ add_action('admin_menu',[__CLASS__,'menu']); add_action('admin_init',[__CLASS__,'settings']); }
  public static function menu(){ add_menu_page(__('DL WhatsApp Checkout','dl-wa-checkout'), __('DL WA Checkout','dl-wa-checkout'),'manage_options','dlwa-settings',[__CLASS__,'render_page'],'dashicons-whatsapp',56); }
  public static function settings(){
    register_setting('dlwa','dlwa_phone_e164',['type'=>'string','sanitize_callback'=>[__CLASS__,'sanitize_phone']]);
    register_setting('dlwa','dlwa_enable_floating',['type'=>'boolean','sanitize_callback'=>function($v){return $v?1:0;}]);
    register_setting('dlwa','dlwa_safe_mode',['type'=>'boolean','sanitize_callback'=>function($v){return $v?1:0;}]);
    register_setting('dlwa','dlwa_shipping_text',['type'=>'string','sanitize_callback'=>'sanitize_textarea_field']);
    register_setting('dlwa','dlwa_btn_color',['type'=>'string','sanitize_callback'=>[__CLASS__,'sanitize_color']]);
    register_setting('dlwa','dlwa_fab_color',['type'=>'string','sanitize_callback'=>[__CLASS__,'sanitize_color']]);
  }
  public static function sanitize_phone($v){$v=preg_replace('/\s+/','',(string)$v); if($v==='')return ''; return preg_replace('/[^0-9\+]/','',$v);}
  public static function sanitize_color($v){$v=(string)$v; if(!preg_match('/^#[0-9a-fA-F]{6}$/',$v)) return ''; return strtoupper($v);}
  public static function render_page(){ ?>
    <div class="wrap"><h1><?php echo esc_html__('DL WhatsApp Checkout','dl-wa-checkout'); ?></h1>
    <form method="post" action="options.php"><?php settings_fields('dlwa'); do_settings_sections('dlwa'); ?>
      <table class="form-table" role="presentation">
        <tr><th><label for="dlwa_phone_e164"><?php esc_html_e('WhatsApp Phone (E.164)','dl-wa-checkout'); ?></label></th>
          <td><input type="text" name="dlwa_phone_e164" id="dlwa_phone_e164" value="<?php echo esc_attr(get_option('dlwa_phone_e164','')); ?>" class="regular-text" placeholder="+5067XXXXXXX" /></td></tr>
        <tr><th><?php esc_html_e('Enable Floating Cart','dl-wa-checkout'); ?></th>
          <td><label><input type="checkbox" name="dlwa_enable_floating" value="1" <?php checked(get_option('dlwa_enable_floating',1)); ?> /> <?php esc_html_e('Enabled','dl-wa-checkout'); ?></label></td></tr>
        <tr><th><?php esc_html_e('Safe mode (shortcode only)','dl-wa-checkout'); ?></th>
          <td><label><input type="checkbox" name="dlwa_safe_mode" value="1" <?php checked(get_option('dlwa_safe_mode',1)); ?> /> <?php esc_html_e('Do not load floating cart globally.','dl-wa-checkout'); ?></label></td></tr>
        <tr><th><label for="dlwa_shipping_text"><?php esc_html_e('Shipping line (info only)','dl-wa-checkout'); ?></label></th>
          <td><textarea name="dlwa_shipping_text" id="dlwa_shipping_text" class="large-text" rows="3"><?php echo esc_textarea(get_option('dlwa_shipping_text','📦 El envío se acuerda directamente con el proveedor')); ?></textarea></td></tr>
        <tr><th><label for="dlwa_btn_color"><?php esc_html_e('WhatsApp button color','dl-wa-checkout'); ?></label></th>
          <td><input type="color" name="dlwa_btn_color" id="dlwa_btn_color" value="<?php echo esc_attr(get_option('dlwa_btn_color','#F48FB1')); ?>" /></td></tr>
        <tr><th><label for="dlwa_fab_color"><?php esc_html_e('Floating cart button color','dl-wa-checkout'); ?></label></th>
          <td><input type="color" name="dlwa_fab_color" id="dlwa_fab_color" value="<?php echo esc_attr(get_option('dlwa_fab_color','#C8A34E')); ?>" /></td></tr>
      </table>
      <?php submit_button(); ?>
    </form></div>
  <?php }
}
