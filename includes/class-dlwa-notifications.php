<?php
if (!defined('ABSPATH')) exit;

class DLWA_Notifications {
  public static function init(){
    if (!get_option('dlwa_enable_notifications', 1)) return;
    
    add_action('wp_footer', [__CLASS__, 'render_notification_container']);
  }
  
  public static function render_notification_container(){ ?>
    <div id="dlwa-notifications" class="dlwa-notifications"></div>
    <style>
    .dlwa-notifications {
      position: fixed;
      top: 20px;
      right: 20px;
      z-index: 10000;
      max-width: 400px;
    }
    .dlwa-notification {
      background: #fff;
      border-radius: 8px;
      padding: 16px;
      margin-bottom: 12px;
      box-shadow: 0 4px 12px rgba(0,0,0,0.15);
      border-left: 4px solid #00a32a;
      animation: dlwaSlideIn 0.3s ease-out;
      position: relative;
    }
    .dlwa-notification.error {
      border-left-color: #d63638;
    }
    .dlwa-notification.warning {
      border-left-color: #dba617;
    }
    .dlwa-notification-close {
      position: absolute;
      top: 8px;
      right: 12px;
      background: none;
      border: none;
      font-size: 18px;
      cursor: pointer;
      color: #666;
    }
    @keyframes dlwaSlideIn {
      from { transform: translateX(100%); opacity: 0; }
      to { transform: translateX(0); opacity: 1; }
    }
    @keyframes dlwaSlideOut {
      from { transform: translateX(0); opacity: 1; }
      to { transform: translateX(100%); opacity: 0; }
    }
    </style>
  <?php }
}