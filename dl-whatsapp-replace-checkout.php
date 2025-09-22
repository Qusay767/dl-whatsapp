/* Base UI */
.dlwa-wrap{max-width:900px;margin:24px auto;padding:16px}
.dlwa-title{font-size:22px;margin-bottom:16px}
.dlwa-cart-list{display:grid;gap:12px}
.dlwa-card{display:grid;grid-template-columns:80px 1fr auto;gap:12px;align-items:center;background:#fff;border:1px solid #eee;border-radius:16px;padding:12px;box-shadow:0 1px 2px rgba(0,0,0,.04)}
.dlwa-card-img{width:80px;height:80px;border-radius:10px;overflow:hidden;background:#f6f6f6;display:flex;align-items:center;justify-content:center}
.dlwa-card-img img{width:100%;height:100%;object-fit:cover}
.dlwa-card-title{font-weight:600;margin:0 0 4px;font-size:15px}
.dlwa-card-price{color:#222;font-weight:500;margin:0 0 8px;font-size:14px}
.dlwa-controls{display:flex;gap:8px;align-items:center}
.dlwa-btn{display:inline-flex;align-items:center;justify-content:center;height:32px;min-width:32px;padding:0 10px;border-radius:999px;border:1px solid #ccc;background:#fff;cursor:pointer}
.dlwa-qty{min-width:28px;text-align:center;font-weight:700}
.dlwa-line-total{font-weight:700}
.dlwa-summary{margin-top:16px;border-top:1px solid #eee;padding-top:12px}
.dlwa-row{display:flex;justify-content:space-between;align-items:center;margin:6px 0}
.dlwa-row span{color:#222;font-weight:600}
.dlwa-row strong{color:#111;font-weight:800}
.dlwa-total strong{font-size:18px}
.dlwa-shipping-note{color:#555;font-size:13px}
.dlwa-form{margin-top:16px}
.dlwa-grid{display:grid;grid-template-columns:1fr 1fr;gap:12px}
.dlwa-grid-colspan{grid-column:span 2}
.dlwa-form input, .dlwa-form textarea{width:100%;padding:10px;border:1px solid #ddd;border-radius:10px}
.dlwa-button-primary{display:inline-flex;gap:8px;align-items:center;justify-content:center;padding:12px 16px;border-radius:999px;background:var(--dlwa-btn,#F48FB1);color:#fff;border:none;text-decoration:none;cursor:pointer;margin-top:10px}
/* Floating */
.dlwa-fab{position:fixed;right:18px;bottom:18px;width:56px;height:56px;border-radius:50%;background:var(--dlwa-fab,#C8A34E);color:#fff;display:flex;align-items:center;justify-content:center;box-shadow:0 10px 20px rgba(0,0,0,.15);cursor:pointer;z-index:9999}
.dlwa-fab-badge{position:absolute;top:-6px;left:-6px;background:#e11d48;color:#fff;border-radius:999px;padding:2px 6px;font-size:12px;font-weight:700}
.dlwa-drawer-overlay{position:fixed;inset:0;background:rgba(0,0,0,.35);opacity:0;pointer-events:none;transition:.2s;z-index:9998}
.dlwa-drawer{position:fixed;top:0;right:-420px;width:380px;max-width:88vw;height:100vh;background:#fff;box-shadow:-10px 0 24px rgba(0,0,0,.1);z-index:9999;display:flex;flex-direction:column;transition:right .25s}
.dlwa-drawer.open{right:0}
.dlwa-drawer-overlay.open{opacity:1;pointer-events:auto}
.dlwa-drawer-header{display:flex;justify-content:space-between;align-items:center;padding:14px 16px;border-bottom:1px solid #eee}
.dlwa-drawer-body{padding:12px;flex:1;overflow:auto;display:grid;gap:8px}
.dlwa-drawer-footer{padding:12px;border-top:1px solid #eee;display:flex;align-items:flex-start;justify-content:space-between}
.dlwa-drawer-close{background:transparent;border:none;font-size:24px;cursor:pointer}

/* v1.6.9 — compact drawer & shipping line */
.dlwa-drawer-body .dlwa-card{grid-template-columns:56px 1fr auto;gap:10px;padding:10px;border-radius:14px}
.dlwa-drawer-body .dlwa-card-img{width:56px;height:56px;border-radius:8px}
.dlwa-drawer-body .dlwa-card-title{font-size:14px;line-height:1.25;margin-bottom:2px;display:-webkit-box;-webkit-line-clamp:2;-webkit-box-orient:vertical;overflow:hidden}
.dlwa-drawer-body .dlwa-card-price{font-size:13px;margin-bottom:6px;opacity:.9}
.dlwa-drawer-body .dlwa-controls .dlwa-btn{min-width:28px;height:28px;padding:0 8px}
.dlwa-drawer-body .dlwa-qty{min-width:22px}
.dlwa-drawer-totals,.dlwa-totals{display:flex;flex-direction:column;gap:4px}
#dlwa-drawer-shipping{font-size:12px;color:#555;margin-top:2px}
.dlwa-drawer-body{padding-bottom:8px}