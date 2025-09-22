<?php
if (!defined('ABSPATH')) exit;

class DLWA_Analytics {
  public static function init(){
    if (!get_option('dlwa_enable_analytics', 1)) return;
    
    add_action('wp_ajax_dlwa_track_event', [__CLASS__, 'track_event']);
    add_action('wp_ajax_nopriv_dlwa_track_event', [__CLASS__, 'track_event']);
    add_action('init', [__CLASS__, 'create_tables']);
  }
  
  public static function create_tables(){
    global $wpdb;
    
    $table_name = $wpdb->prefix . 'dlwa_analytics';
    
    $charset_collate = $wpdb->get_charset_collate();
    
    $sql = "CREATE TABLE $table_name (
      id mediumint(9) NOT NULL AUTO_INCREMENT,
      event_type varchar(50) NOT NULL,
      session_id varchar(100) NOT NULL,
      cart_value decimal(10,2) DEFAULT 0,
      items_count int DEFAULT 0,
      user_agent text,
      ip_address varchar(45),
      created_at datetime DEFAULT CURRENT_TIMESTAMP,
      PRIMARY KEY (id),
      KEY event_type (event_type),
      KEY session_id (session_id),
      KEY created_at (created_at)
    ) $charset_collate;";
    
    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
    dbDelta($sql);
  }
  
  public static function track_event(){
    if (!wp_verify_nonce($_POST['nonce'] ?? '', 'dlwa_nonce')) {
      wp_send_json_error(['message' => 'Invalid nonce']);
    }
    
    global $wpdb;
    $table_name = $wpdb->prefix . 'dlwa_analytics';
    
    $event_type = sanitize_text_field($_POST['event_type'] ?? '');
    $session_id = sanitize_text_field($_POST['session_id'] ?? '');
    $cart_value = floatval($_POST['cart_value'] ?? 0);
    $items_count = intval($_POST['items_count'] ?? 0);
    
    $wpdb->insert(
      $table_name,
      [
        'event_type' => $event_type,
        'session_id' => $session_id,
        'cart_value' => $cart_value,
        'items_count' => $items_count,
        'user_agent' => $_SERVER['HTTP_USER_AGENT'] ?? '',
        'ip_address' => self::get_client_ip()
      ]
    );
    
    wp_send_json_success(['message' => 'Event tracked']);
  }
  
  public static function get_stats(){
    global $wpdb;
    $table_name = $wpdb->prefix . 'dlwa_analytics';
    
    // Check if table exists
    if ($wpdb->get_var("SHOW TABLES LIKE '$table_name'") != $table_name) {
      return [
        'total_orders' => 0,
        'abandonments' => 0,
        'conversion_rate' => 0
      ];
    }
    
    $total_orders = $wpdb->get_var("SELECT COUNT(*) FROM $table_name WHERE event_type = 'whatsapp_sent'");
    $abandonments = $wpdb->get_var("SELECT COUNT(DISTINCT session_id) FROM $table_name WHERE event_type = 'cart_abandoned'");
    $total_sessions = $wpdb->get_var("SELECT COUNT(DISTINCT session_id) FROM $table_name WHERE event_type IN ('cart_viewed', 'whatsapp_sent')");
    
    $conversion_rate = $total_sessions > 0 ? round(($total_orders / $total_sessions) * 100, 2) : 0;
    
    return [
      'total_orders' => intval($total_orders),
      'abandonments' => intval($abandonments),
      'conversion_rate' => $conversion_rate
    ];
  }
  
  private static function get_client_ip(){
    $ip_keys = ['HTTP_CLIENT_IP', 'HTTP_X_FORWARDED_FOR', 'REMOTE_ADDR'];
    foreach ($ip_keys as $key) {
      if (array_key_exists($key, $_SERVER) === true) {
        foreach (explode(',', $_SERVER[$key]) as $ip) {
          $ip = trim($ip);
          if (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE) !== false) {
            return $ip;
          }
        }
      }
    }
    return $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0';
  }
}