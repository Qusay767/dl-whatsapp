(function($){
'use strict';
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
function renderCart($list, data){
  $list.empty();
  if (!data.items || !data.items.length){
    $list.append('<div class="dlwa-empty">Tu carrito está vacío.</div>');
    $('#dlwa-total').text('—'); return;
  }
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
}
function refreshCart($list){ return $.post(DLWA.ajax_url, { action:'dlwa_get_cart', nonce:DLWA.nonce }).done(function(res){ if (res && res.success){ renderCart($list, res.data); }}); }
function updateQty(key, qty, $list){ return $.post(DLWA.ajax_url, { action:'dlwa_update_qty', nonce:DLWA.nonce, cart_item_key:key, qty:qty }).done(function(res){ if (res && res.success){ renderCart($list, res.data); }}); }
function removeItem(key, $list){ return $.post(DLWA.ajax_url, { action:'dlwa_remove_item', nonce:DLWA.nonce, cart_item_key:key }).done(function(res){ if (res && res.success){ renderCart($list, res.data); }}); }
$(function(){
  if (typeof DLWA === 'undefined') return;
  var $list = $('#dlwa-cart-list'); if (!$list.length) return;
  refreshCart($list);
  $(document).on('click','.dlwa-plus',function(){ var key=$(this).data('key'); var cur=parseInt($('#qty-'+key).text(),10)||0; updateQty(key,cur+1,$list); });
  $(document).on('click','.dlwa-minus',function(){ var key=$(this).data('key'); var cur=parseInt($('#qty-'+key).text(),10)||0; if(cur>0) updateQty(key,cur-1,$list); });
  $(document).on('click','.dlwa-remove',function(){ var key=$(this).data('key'); removeItem(key,$list); });
  $('#dlwa-whatsapp').on('click', function(){
    var payload={ action:'dlwa_build_whatsapp', nonce:DLWA.nonce };
    $('#dlwa-form').serializeArray().forEach(function(f){ payload[f.name]=f.value; });
    $.post(DLWA.ajax_url, payload).done(function(res){
      if (res && res.success && res.data && res.data.url){ window.open(res.data.url, '_blank'); }
      else { alert('No se pudo generar el enlace de WhatsApp.'); }
    });
  });
});
})(jQuery);