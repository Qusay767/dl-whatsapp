<?php
if (!defined('ABSPATH')) exit;
class DLWA_Admin {
  public static function init(){ add_action('admin_menu',[__CLASS__,'menu']); add_action('admin_init',[__CLASS__,'settings']); }
  public static function menu(){ 
    add_menu_page(__('DL WhatsApp Checkout','dl-wa-checkout'), __('DL WA Checkout','dl-wa-checkout'),'manage_options','dlwa-settings',[__CLASS__,'render_page'],'dashicons-whatsapp',56);
    add_submenu_page('dlwa-settings', __('Analytics','dl-wa-checkout'), __('Analytics','dl-wa-checkout'), 'manage_options', 'dlwa-analytics', [__CLASS__,'render_analytics']);
  }
  public static function settings(){
    register_setting('dlwa','dlwa_phone_e164',['type'=>'string','sanitize_callback'=>[__CLASS__,'sanitize_phone']]);
    register_setting('dlwa','dlwa_enable_floating',['type'=>'boolean','sanitize_callback'=>function($v){return $v?1:0;}]);
    register_setting('dlwa','dlwa_safe_mode',['type'=>'boolean','sanitize_callback'=>function($v){return $v?1:0;}]);
    register_setting('dlwa','dlwa_shipping_text',['type'=>'string','sanitize_callback'=>'sanitize_textarea_field']);
    register_setting('dlwa','dlwa_btn_color',['type'=>'string','sanitize_callback'=>[__CLASS__,'sanitize_color']]);
    register_setting('dlwa','dlwa_fab_color',['type'=>'string','sanitize_callback'=>[__CLASS__,'sanitize_color']]);
    register_setting('dlwa','dlwa_enable_analytics',['type'=>'boolean','sanitize_callback'=>function($v){return $v?1:0;}]);
    register_setting('dlwa','dlwa_enable_notifications',['type'=>'boolean','sanitize_callback'=>function($v){return $v?1:0;}]);
    register_setting('dlwa','dlwa_custom_fields',['type'=>'string','sanitize_callback'=>'sanitize_textarea_field']);
    register_setting('dlwa','dlwa_thank_you_message',['type'=>'string','sanitize_callback'=>'sanitize_textarea_field']);
    register_setting('dlwa','dlwa_min_order_amount',['type'=>'number','sanitize_callback'=>'floatval']);
    register_setting('dlwa','dlwa_business_hours',['type'=>'string','sanitize_callback'=>'sanitize_textarea_field']);
  }
  public static function sanitize_phone($v){$v=preg_replace('/\s+/','',(string)$v); if($v==='')return ''; return preg_replace('/[^0-9\+]/','',$v);}
  public static function sanitize_color($v){$v=(string)$v; if(!preg_match('/^#[0-9a-fA-F]{6}$/',$v)) return ''; return strtoupper($v);}
  public static function render_page(){ ?>
    <div class="wrap">
      <h1><?php echo esc_html__('DL WhatsApp Checkout Settings','dl-wa-checkout'); ?></h1>
      <nav class="nav-tab-wrapper">
        <a href="#basic" class="nav-tab nav-tab-active"><?php esc_html_e('Basic Settings','dl-wa-checkout'); ?></a>
        <a href="#appearance" class="nav-tab"><?php esc_html_e('Appearance','dl-wa-checkout'); ?></a>
        <a href="#advanced" class="nav-tab"><?php esc_html_e('Advanced','dl-wa-checkout'); ?></a>
      </nav>
    <form method="post" action="options.php"><?php settings_fields('dlwa'); do_settings_sections('dlwa'); ?>
      
      <div id="basic" class="tab-content">
        <h2><?php esc_html_e('Basic Configuration','dl-wa-checkout'); ?></h2>
        <table class="form-table" role="presentation">
        <tr><th><label for="dlwa_phone_e164"><?php esc_html_e('WhatsApp Phone (E.164)','dl-wa-checkout'); ?></label></th>
          <td><input type="text" name="dlwa_phone_e164" id="dlwa_phone_e164" value="<?php echo esc_attr(get_option('dlwa_phone_e164','')); ?>" class="regular-text" placeholder="+5067XXXXXXX" />
          <p class="description"><?php esc_html_e('Enter your WhatsApp number in international format (e.g., +1234567890)','dl-wa-checkout'); ?></p></td></tr>
        <tr><th><label for="dlwa_shipping_text"><?php esc_html_e('Shipping Information','dl-wa-checkout'); ?></label></th>
          <td><textarea name="dlwa_shipping_text" id="dlwa_shipping_text" class="large-text" rows="3"><?php echo esc_textarea(get_option('dlwa_shipping_text','📦 El envío se acuerda directamente con el proveedor')); ?></textarea>
          <p class="description"><?php esc_html_e('This text will appear in the cart and WhatsApp message','dl-wa-checkout'); ?></p></td></tr>
        <tr><th><label for="dlwa_min_order_amount"><?php esc_html_e('Minimum Order Amount','dl-wa-checkout'); ?></label></th>
          <td><input type="number" name="dlwa_min_order_amount" id="dlwa_min_order_amount" value="<?php echo esc_attr(get_option('dlwa_min_order_amount',0)); ?>" min="0" step="0.01" />
          <p class="description"><?php esc_html_e('Set minimum order amount (0 for no minimum)','dl-wa-checkout'); ?></p></td></tr>
        </table>
      </div>

      <div id="appearance" class="tab-content" style="display:none;">
        <h2><?php esc_html_e('Appearance Settings','dl-wa-checkout'); ?></h2>
        <table class="form-table" role="presentation">
        <tr><th><?php esc_html_e('Enable Floating Cart','dl-wa-checkout'); ?></th>
          <td><label><input type="checkbox" name="dlwa_enable_floating" value="1" <?php checked(get_option('dlwa_enable_floating',1)); ?> /> <?php esc_html_e('Enabled','dl-wa-checkout'); ?></label></td></tr>
        <tr><th><?php esc_html_e('Safe mode (shortcode only)','dl-wa-checkout'); ?></th>
          <td><label><input type="checkbox" name="dlwa_safe_mode" value="1" <?php checked(get_option('dlwa_safe_mode',1)); ?> /> <?php esc_html_e('Do not load floating cart globally.','dl-wa-checkout'); ?></label></td></tr>
        <tr><th><label for="dlwa_btn_color"><?php esc_html_e('WhatsApp button color','dl-wa-checkout'); ?></label></th>
          <td><input type="color" name="dlwa_btn_color" id="dlwa_btn_color" value="<?php echo esc_attr(get_option('dlwa_btn_color','#F48FB1')); ?>" /></td></tr>
        <tr><th><label for="dlwa_fab_color"><?php esc_html_e('Floating cart button color','dl-wa-checkout'); ?></label></th>
          <td><input type="color" name="dlwa_fab_color" id="dlwa_fab_color" value="<?php echo esc_attr(get_option('dlwa_fab_color','#C8A34E')); ?>" /></td></tr>
        </table>
      </div>
      <div id="advanced" class="tab-content" style="display:none;">
        <h2><?php esc_html_e('Advanced Settings','dl-wa-checkout'); ?></h2>
        <table class="form-table" role="presentation">
        <tr><th><?php esc_html_e('Enable Analytics','dl-wa-checkout'); ?></th>
          <td><label><input type="checkbox" name="dlwa_enable_analytics" value="1" <?php checked(get_option('dlwa_enable_analytics',1)); ?> /> <?php esc_html_e('Track cart abandonment and conversion rates','dl-wa-checkout'); ?></label></td></tr>
        <tr><th><?php esc_html_e('Enable Notifications','dl-wa-checkout'); ?></th>
          <td><label><input type="checkbox" name="dlwa_enable_notifications" value="1" <?php checked(get_option('dlwa_enable_notifications',1)); ?> /> <?php esc_html_e('Show success/error notifications','dl-wa-checkout'); ?></label></td></tr>
        <tr><th><label for="dlwa_thank_you_message"><?php esc_html_e('Thank You Message','dl-wa-checkout'); ?></label></th>
          <td><textarea name="dlwa_thank_you_message" id="dlwa_thank_you_message" class="large-text" rows="2"><?php echo esc_textarea(get_option('dlwa_thank_you_message','¡Gracias por tu pedido! Te contactaremos pronto.')); ?></textarea></td></tr>
        <tr><th><label for="dlwa_business_hours"><?php esc_html_e('Business Hours','dl-wa-checkout'); ?></label></th>
          <td><textarea name="dlwa_business_hours" id="dlwa_business_hours" class="large-text" rows="3" placeholder="Monday-Friday: 9:00-18:00&#10;Saturday: 9:00-14:00&#10;Sunday: Closed"><?php echo esc_textarea(get_option('dlwa_business_hours','')); ?></textarea>
          <p class="description"><?php esc_html_e('Display business hours in the checkout form','dl-wa-checkout'); ?></p></td></tr>
        </table>
      </div>
      
      <?php submit_button(); ?>
    </form>
    
    <script>
    jQuery(document).ready(function($) {
      $('.nav-tab').click(function(e) {
        e.preventDefault();
        $('.nav-tab').removeClass('nav-tab-active');
        $(this).addClass('nav-tab-active');
        $('.tab-content').hide();
        $($(this).attr('href')).show();
      });
    });
    </script>
    </div>
  <?php }
  
  public static function render_analytics(){ ?>
    <div class="wrap">
      <h1><?php echo esc_html__('WhatsApp Checkout Analytics','dl-wa-checkout'); ?></h1>
      <?php
      $stats = DLWA_Analytics::get_stats();
      ?>
      <div class="dlwa-analytics-grid" style="display:grid;grid-template-columns:repeat(auto-fit,minmax(250px,1fr));gap:20px;margin:20px 0;">
        <div class="dlwa-stat-card" style="background:#fff;padding:20px;border-radius:8px;box-shadow:0 2px 4px rgba(0,0,0,0.1);">
          <h3><?php esc_html_e('Total Orders','dl-wa-checkout'); ?></h3>
          <p style="font-size:2em;margin:0;color:#2271b1;"><?php echo esc_html($stats['total_orders']); ?></p>
        </div>
        <div class="dlwa-stat-card" style="background:#fff;padding:20px;border-radius:8px;box-shadow:0 2px 4px rgba(0,0,0,0.1);">
          <h3><?php esc_html_e('Cart Abandonments','dl-wa-checkout'); ?></h3>
          <p style="font-size:2em;margin:0;color:#d63638;"><?php echo esc_html($stats['abandonments']); ?></p>
        </div>
        <div class="dlwa-stat-card" style="background:#fff;padding:20px;border-radius:8px;box-shadow:0 2px 4px rgba(0,0,0,0.1);">
          <h3><?php esc_html_e('Conversion Rate','dl-wa-checkout'); ?></h3>
          <p style="font-size:2em;margin:0;color:#00a32a;"><?php echo esc_html($stats['conversion_rate']); ?>%</p>
        </div>
      </div>
    </div>
  <?php }
}