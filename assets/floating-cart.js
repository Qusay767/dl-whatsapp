(function($){
'use strict';

// Session management for floating cart
var sessionId = 'dlwa_' + Date.now() + '_' + Math.random().toString(36).substr(2, 9);
var lastCartValue = 0;

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

var dlwaBadgeEl=null; function dlwaSetBadge(n){ n=parseInt(n||0,10); if(!dlwaBadgeEl) dlwaBadgeEl=jQuery('#dlwa-fab-badge'); if(!dlwaBadgeEl.length) return; if(n>0){ dlwaBadgeEl.text(n).show(); } else { dlwaBadgeEl.text('0').hide(); }} function dlwaRefreshBadge(){ return jQuery.post(DLWA.ajax_url,{action:'dlwa_get_cart',nonce:DLWA.nonce}).done(function(r){ if(r&&r.success){ dlwaSetBadge(r.data.count); }}); }

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

function openDrawer(){ $('#dlwa-drawer').addClass('open'); $('#dlwa-drawer-overlay').addClass('open'); }
function closeDrawer(){ $('#dlwa-drawer').removeClass('open'); $('#dlwa-drawer-overlay').removeClass('open'); }

function renderDrawer(body, data){
  body.empty();
  if (!data.items || !data.items.length){
    body.append('<div class="dlwa-empty">' + (DLWA.empty_cart_text || 'Tu carrito está vacío.') + '</div>');
    $('#dlwa-drawer-total').text('—'); $('#dlwa-drawer-shipping').text(DLWA.shipping_text || '📦 El envío se acuerda directamente con el proveedor'); return;
  }
  
  lastCartValue = data.total;
  
  data.items.forEach(function(it){
    var row=$('<div class="dlwa-card"></div>');
    row.append('<div class="dlwa-card-img"><img src="'+ it.image +'" alt=""></div>');
    var mid=$('<div></div>');
    mid.append('<div class="dlwa-card-title">'+ it.name +'</div>');
    mid.append('<div class="dlwa-card-price">'+ it.line_total_fmt +'</div>');
    var ctr=$('<div class="dlwa-controls"></div>');
    ctr.append('<button class="dlwa-btn dlwa-minus" data-key="'+it.key+'">−</button>');
    ctr.append('<span class="dlwa-qty" id="qty-d-'+it.key+'">'+ it.qty +'</span>');
    ctr.append('<button class="dlwa-btn dlwa-plus" data-key="'+it.key+'">+</button>');
    ctr.append('<button class="dlwa-btn dlwa-remove" data-key="'+it.key+'">×</button>');
    mid.append(ctr); row.append(mid);
    row.append('<div class="dlwa-line-total">'+ it.line_total_fmt +'</div>');
    body.append(row);
  });
  $('#dlwa-drawer-total').text(formatPrice(data.total));
  dlwaSetBadge(data.count);
  $('#dlwa-drawer-shipping').text(DLWA.shipping_text || '📦 El envío se acuerda directamente con el proveedor');
  
  // Update WhatsApp button state based on minimum order
  var $waBtn = $('#dlwa-drawer-wa');
  if (!validateMinOrder(data.total) && parseFloat(DLWA.min_order_amount || 0) > 0) {
    $waBtn.prop('disabled', true).text('Pedido mínimo: ' + formatPrice(DLWA.min_order_amount));
  } else {
    $waBtn.prop('disabled', false).text(DLWA.whatsapp_button_text || 'Enviar por WhatsApp');
  }
}

function refreshDrawer(){ return $.post(DLWA.ajax_url, { action:'dlwa_get_cart', nonce:DLWA.nonce }); }

function updateQty(key, qty){ 
  return $.post(DLWA.ajax_url, { action:'dlwa_update_qty', nonce:DLWA.nonce, cart_item_key:key, qty:qty })
    .fail(function() {
      showNotification('Error al actualizar el carrito', 'error');
    });
}

function removeItem(key){ 
  return $.post(DLWA.ajax_url, { action:'dlwa_remove_item', nonce:DLWA.nonce, cart_item_key:key })
    .done(function(res) {
      if (res && res.success) {
        showNotification('Producto eliminado del carrito');
      }
    })
    .fail(function() {
      showNotification('Error al eliminar el producto', 'error');
    });
}

$(function(){

  // Initialize badge on load
  dlwaRefreshBadge();
  // WooCommerce events that indicate cart changed
  $('body').on('added_to_cart removed_from_cart updated_wc_div cart_totals_refreshed cart_page_refreshed wc_fragments_refreshed wc_fragments_loaded', function(){ dlwaRefreshBadge(); });

  if (typeof DLWA === 'undefined') return;
  var $fab=$('#dlwa-fab'), $overlay=$('#dlwa-drawer-overlay'), $body=$('#dlwa-drawer-body'); if(!$fab.length) { dlwaRefreshBadge(); return; }
  
  $fab.on('click', function(){ 
    openDrawer(); 
    trackEvent('floating_cart_opened');
    refreshDrawer().done(function(res){ if(res&&res.success){ renderDrawer($body,res.data); } }); 
  });
  
  $overlay.on('click', closeDrawer); $(document).on('click','.dlwa-drawer-close', closeDrawer);
  
  $(document).on('click','.dlwa-plus',function(){ var key=$(this).data('key'); var cur=parseInt($('#qty-d-'+key).text(),10)||0; updateQty(key,cur+1).done(function(r){ if(r&&r.success){ renderDrawer($body,r.data); } }); });
  $(document).on('click','.dlwa-minus',function(){ var key=$(this).data('key'); var cur=parseInt($('#qty-d-'+key).text(),10)||0; if(cur>0) updateQty(key,cur-1).done(function(r){ if(r&&r.success){ renderDrawer($body,r.data); } }); });
  $(document).on('click','.dlwa-remove',function(){ var key=$(this).data('key'); removeItem(key).done(function(r){ if(r&&r.success){ renderDrawer($body,r.data); } }); });
  
  $(document).on('click', '#dlwa-drawer-wa', function(){
    // Validate minimum order
    if (!validateMinOrder(lastCartValue)) {
      showNotification('El pedido mínimo es ' + formatPrice(DLWA.min_order_amount), 'warning');
      return;
    }
    
    // Show loading state
    var $btn = $(this);
    var originalText = $btn.text();
    $btn.text('Generando enlace...').prop('disabled', true);
    
    var payload={ action:'dlwa_build_whatsapp', nonce:DLWA.nonce };
    $.post(DLWA.ajax_url, payload).done(function(res){
      $btn.text(originalText).prop('disabled', false);
      
      if (res && res.success && res.data && res.data.url){ 
        window.open(res.data.url, '_blank');
        trackEvent('whatsapp_sent_floating', { cart_value: lastCartValue });
        showNotification(DLWA.thank_you_message || '¡Gracias por tu pedido! Te contactaremos pronto.');
        closeDrawer();
      } else { 
        showNotification('No se pudo generar el enlace de WhatsApp.', 'error');
      }
    }).fail(function() {
      $btn.text(originalText).prop('disabled', false);
      showNotification('Error de conexión. Inténtalo de nuevo.', 'error');
    });
    });
  });
});
})(jQuery);