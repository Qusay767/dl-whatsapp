(function($){
'use strict';

// Session management
var sessionId = 'dlwa_' + Date.now() + '_' + Math.random().toString(36).substr(2, 9);
var cartViewed = false;
var lastCartValue = 0;
var abandonmentTimer = null;

// Analytics tracking
function trackEvent(eventType, data = {}) {
  if (typeof DLWA === 'undefined' || !DLWA.enable_analytics) return;
  
  $.post(DLWA.ajax_url, {
    action: 'dlwa_track_event',
    nonce: DLWA.nonce,
    event_type: eventType,
    session_id: sessionId,
    cart_value: data.cart_value || 0,
    items_count: data.items_count || 0
  });
}

// Notification system
function showNotification(message, type = 'success') {
  if (!DLWA.enable_notifications) return;
  
  var notification = $('<div class="dlwa-notification ' + type + '">' +
    '<button class="dlwa-notification-close">&times;</button>' +
    '<div>' + message + '</div>' +
  '</div>');
  
  $('#dlwa-notifications').append(notification);
  
  notification.find('.dlwa-notification-close').on('click', function() {
    notification.css('animation', 'dlwaSlideOut 0.3s ease-in');
    setTimeout(function() { notification.remove(); }, 300);
  });
  
  setTimeout(function() {
    if (notification.length) {
      notification.css('animation', 'dlwaSlideOut 0.3s ease-in');
      setTimeout(function() { notification.remove(); }, 300);
    }
  }, 5000);
}

// Cart abandonment tracking
function resetAbandonmentTimer() {
  if (abandonmentTimer) clearTimeout(abandonmentTimer);
  abandonmentTimer = setTimeout(function() {
    trackEvent('cart_abandoned', { cart_value: lastCartValue });
  }, 300000); // 5 minutes
}

function dlwaSetBadge(n){ var el=jQuery('#dlwa-fab-badge'); if(!el.length) return; n=parseInt(n||0,10); if(n>0){ el.text(n).show(); } else { el.text('0').hide(); }}

function formatPrice(n){
  n = Number(n||0);
  var dec = parseInt(DLWA.decimals||0,10);
  var thou = DLWA.thousand_sep || ',';
  var dsep = DLWA.decimal_sep || '.';
  var s = n.toFixed(dec).split('.');
  var i = s[0], f = s[1] || '';
  var rgx = /\B(?=(\d{3})+(?!\d))/g;
  i = i.replace(rgx, thou);
  var num = dec>0 ? (i + dsep + f) : i;
  var out = DLWA.price_format || '%1$s%2$s';
  out = out.replace('%1$s', DLWA.currency_symbol || '').replace('%2$s', num);
  return out;
}

function validateMinOrder(total) {
  var minAmount = parseFloat(DLWA.min_order_amount || 0);
  return minAmount === 0 || total >= minAmount;
}

function renderCart($list, data){
  $list.empty();
  if (!data.items || !data.items.length){
    $list.append('<div class="dlwa-empty">' + (DLWA.empty_cart_text || 'Tu carrito está vacío.') + '</div>');
    $('#dlwa-total').text('—'); return;
  }
  
  // Track cart view
  if (!cartViewed) {
    trackEvent('cart_viewed', { cart_value: data.total, items_count: data.count });
    cartViewed = true;
  }
  
  lastCartValue = data.total;
  resetAbandonmentTimer();
  
  data.items.forEach(function(it){
    var card=$('<div class="dlwa-card"></div>');
    card.append('<div class="dlwa-card-img"><img src="'+ it.image +'" alt=""></div>');
    var mid=$('<div></div>');
    mid.append('<div class="dlwa-card-title">'+ it.name +'</div>');
    mid.append('<div class="dlwa-card-price">'+ it.line_total_fmt +'</div>');
    var ctr=$('<div class="dlwa-controls"></div>');
    ctr.append('<button class="dlwa-btn dlwa-minus" data-key="'+it.key+'">−</button>');
    ctr.append('<span class="dlwa-qty" id="qty-'+it.key+'">'+ it.qty +'</span>');
    ctr.append('<button class="dlwa-btn dlwa-plus" data-key="'+it.key+'">+</button>');
    ctr.append('<button class="dlwa-btn dlwa-remove" data-key="'+it.key+'">×</button>');
    mid.append(ctr); card.append(mid);
    card.append('<div class="dlwa-line-total">'+ it.line_total_fmt +'</div>');
    $list.append(card);
  });
  $('#dlwa-total').text(formatPrice(data.total));
  dlwaSetBadge(data.count);
  $('#dlwa-shipping-text').text(DLWA.shipping_text || '');
  
  // Show minimum order warning
  if (!validateMinOrder(data.total) && parseFloat(DLWA.min_order_amount || 0) > 0) {
    var minOrderWarning = $('<div class="dlwa-min-order-warning" style="background:#fff3cd;border:1px solid #ffeaa7;color:#856404;padding:12px;border-radius:8px;margin:12px 0;">' +
      '<strong>⚠️ Pedido mínimo:</strong> ' + formatPrice(DLWA.min_order_amount) +
    '</div>');
    $list.after(minOrderWarning);
  } else {
    $('.dlwa-min-order-warning').remove();
  }
  
  // Show business hours if available
  if (DLWA.business_hours && DLWA.business_hours.trim()) {
    if (!$('.dlwa-business-hours').length) {
      var businessHours = $('<div class="dlwa-business-hours" style="background:#f8f9fa;border:1px solid #dee2e6;padding:12px;border-radius:8px;margin:12px 0;font-size:14px;">' +
        '<strong>🕒 Horarios de atención:</strong><br>' +
        DLWA.business_hours.replace(/\n/g, '<br>') +
      '</div>');
      $('#dlwa-shipping-text').after(businessHours);
    }
  }
}

function refreshCart($list){ return $.post(DLWA.ajax_url, { action:'dlwa_get_cart', nonce:DLWA.nonce }).done(function(res){ if (res && res.success){ renderCart($list, res.data); }}); }

function updateQty(key, qty, $list){ 
  return $.post(DLWA.ajax_url, { action:'dlwa_update_qty', nonce:DLWA.nonce, cart_item_key:key, qty:qty })
    .done(function(res){ 
      if (res && res.success){ 
        renderCart($list, res.data);
        trackEvent('cart_updated', { cart_value: res.data.total, items_count: res.data.count });
      }
    })
    .fail(function() {
      showNotification('Error al actualizar el carrito', 'error');
    });
}

function removeItem(key, $list){ 
  return $.post(DLWA.ajax_url, { action:'dlwa_remove_item', nonce:DLWA.nonce, cart_item_key:key })
    .done(function(res){ 
      if (res && res.success){ 
        renderCart($list, res.data);
        showNotification('Producto eliminado del carrito');
        trackEvent('item_removed', { cart_value: res.data.total, items_count: res.data.count });
      }
    })
    .fail(function() {
      showNotification('Error al eliminar el producto', 'error');
    });
}

$(function(){
  if (typeof DLWA === 'undefined') return;
  var $list = $('#dlwa-cart-list'); if (!$list.length) return;
  
  refreshCart($list);
  
  $(document).on('click','.dlwa-plus',function(){ var key=$(this).data('key'); var cur=parseInt($('#qty-'+key).text(),10)||0; updateQty(key,cur+1,$list); });
  $(document).on('click','.dlwa-minus',function(){ var key=$(this).data('key'); var cur=parseInt($('#qty-'+key).text(),10)||0; if(cur>0) updateQty(key,cur-1,$list); });
  $(document).on('click','.dlwa-remove',function(){ var key=$(this).data('key'); removeItem(key,$list); });
  
  $('#dlwa-whatsapp').on('click', function(){
    // Validate minimum order
    if (!validateMinOrder(lastCartValue)) {
      showNotification('El pedido mínimo es ' + formatPrice(DLWA.min_order_amount), 'warning');
      return;
    }
    
    // Show loading state
    var $btn = $(this);
    var originalText = $btn.html();
    $btn.html('<span>Generando enlace...</span>').prop('disabled', true);
    
    var payload={ action:'dlwa_build_whatsapp', nonce:DLWA.nonce };
    $('#dlwa-form').serializeArray().forEach(function(f){ payload[f.name]=f.value; });
    
    $.post(DLWA.ajax_url, payload).done(function(res){
      $btn.html(originalText).prop('disabled', false);
      
      if (res && res.success && res.data && res.data.url){ 
        window.open(res.data.url, '_blank');
        trackEvent('whatsapp_sent', { cart_value: lastCartValue });
        showNotification(DLWA.thank_you_message || '¡Gracias por tu pedido! Te contactaremos pronto.');
        
        // Clear abandonment timer
        if (abandonmentTimer) clearTimeout(abandonmentTimer);
      } else { 
        showNotification('No se pudo generar el enlace de WhatsApp.', 'error');
      }
    }).fail(function() {
      $btn.html(originalText).prop('disabled', false);
      showNotification('Error de conexión. Inténtalo de nuevo.', 'error');
    });
  });
  
  // Track page unload for abandonment
  $(window).on('beforeunload', function() {
    if (lastCartValue > 0) {
      trackEvent('page_exit', { cart_value: lastCartValue });
    }
  });
});
})(jQuery);