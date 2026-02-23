<{* Subscriptions - Checkout *}>
<{assign var="xm_module_url" value="`$xoops_url`/modules/subscriptions"}>

<div class="subscriptions-wrap">

  <{* ── Page header ─────────────────────────────────────────────── *}>
  <div class="xm-checkout-header">
    <a href="<{$xm_module_url}>/plans.php" class="xm-back-link">
      <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24"
           fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"
           aria-hidden="true"><polyline points="15 18 9 12 15 6"/></svg>
      <{$smarty.const._MD_SUBSCRIPTIONS_BACK_TO_PLANS}>
    </a>
    <h1 class="xm-checkout-title"><{$smarty.const._MD_SUBSCRIPTIONS_CHECKOUT_TITLE}></h1>
  </div>

  <div class="xm-checkout-layout">

    <{* ── LEFT: Payment panel ────────────────────────────────────── *}>
    <main class="xm-checkout-main">

      <{* Error notice *}>
      <{if $xm_error}>
      <div class="xm-notice xm-notice--error" role="alert">
        <span class="xm-notice__icon" aria-hidden="true">⚠</span>
        <span><{$xm_error|escape}></span>
      </div>
      <{/if}>

      <{* Coupon section *}>
      <div class="xm-checkout-section">
        <h2 class="xm-checkout-section__title">
          <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24"
               fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
               aria-hidden="true"><path d="M20.59 13.41l-7.17 7.17a2 2 0 0 1-2.83 0L2 12V2h10l8.59 8.59a2 2 0 0 1 0 2.82z"/><line x1="7" y1="7" x2="7.01" y2="7"/></svg>
          <{$smarty.const._MD_SUBSCRIPTIONS_HAVE_COUPON}>
        </h2>

        <form method="post" action="<{$xm_module_url}>/checkout.php?plan=<{$xm_plan.plan_id}>" class="xm-coupon-form" id="coupon-form">
          <input type="hidden" name="token" value="<{$xm_token}>">
          <div class="xm-coupon-row">
            <input type="text" id="coupon_code" name="coupon_code"
                   class="xm-input xm-coupon-input<{if $xm_discount_raw > 0}> xm-input--success<{/if}><{if $xm_error}> xm-input--error<{/if}>"
                   value="<{$xm_coupon_code|escape}>"
                   placeholder="e.g. SAVE20"
                   autocomplete="off" maxlength="50"
                   aria-label="<{$smarty.const._MD_SUBSCRIPTIONS_COUPON_CODE}>">
            <button type="submit" class="xm-btn xm-btn--secondary xm-coupon-btn">
              <{if $xm_discount_raw > 0}>
                <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24"
                     fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"
                     aria-hidden="true"><polyline points="20 6 9 17 4 12"/></svg>
                <{$smarty.const._MD_SUBSCRIPTIONS_APPLIED}>
              <{else}>
                <{$smarty.const._MD_SUBSCRIPTIONS_APPLY_COUPON}>
              <{/if}>
            </button>
          </div>
          <{if $xm_discount_raw > 0}>
          <p class="xm-coupon-success">
            <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24"
                 fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"
                 aria-hidden="true"><polyline points="20 6 9 17 4 12"/></svg>
            <{$smarty.const._MD_SUBSCRIPTIONS_COUPON_APPLIED}> — <strong><{$xm_coupon_code|escape}></strong>
          </p>
          <{/if}>
        </form>
      </div>

      <{* Payment section *}>
      <div class="xm-checkout-section xm-checkout-section--pay">
        <h2 class="xm-checkout-section__title">
          <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24"
               fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
               aria-hidden="true"><rect x="1" y="4" width="22" height="16" rx="2" ry="2"/><line x1="1" y1="10" x2="23" y2="10"/></svg>
          <{$smarty.const._MD_SUBSCRIPTIONS_PAYMENT_DETAILS}>
        </h2>

        <form method="post" action="<{$xm_module_url}>/payment.php" class="xm-pay-form" id="pay-form">
          <input type="hidden" name="token"        value="<{$xm_pay_token}>">
          <input type="hidden" name="plan_id"      value="<{$xm_plan.plan_id|escape:'html'}>">
          <input type="hidden" name="coupon_id"    value="<{$xm_coupon_id|escape:'html'}>">
          <input type="hidden" name="discount_raw" value="<{$xm_discount_raw}>">

          <{* Gateway selector (visual, not functional if only 1) *}>
          <div class="xm-gateway-pill">
            <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24"
                 fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                 aria-hidden="true"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
            <{$smarty.const._MD_SUBSCRIPTIONS_GATEWAY}>: <strong><{$xm_gateway|capitalize}></strong>
          </div>

          <button type="submit" class="xm-btn xm-btn--primary xm-btn--full xm-pay-btn" id="pay-btn">
            <span class="xm-pay-btn__icon" aria-hidden="true">&#x1F512;</span>
            <span class="xm-pay-btn__label">
              <{$smarty.const._MD_SUBSCRIPTIONS_PROCEED_PAYMENT}>
            </span>
            <span class="xm-pay-btn__amount"><{$xm_total}></span>
          </button>

          <p class="xm-secure-note">
            <svg xmlns="http://www.w3.org/2000/svg" width="13" height="13" viewBox="0 0 24 24"
                 fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                 aria-hidden="true"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>
            <{$smarty.const._MD_SUBSCRIPTIONS_SECURE_NOTE}>
          </p>
        </form>
      </div>

      <{* Trust badges *}>
      <div class="xm-trust-row">
        <div class="xm-trust-badge">
          <span class="xm-trust-badge__icon" aria-hidden="true">&#x1F512;</span>
          <span><{$smarty.const._MD_SUBSCRIPTIONS_TRUST_SSL}></span>
        </div>
        <div class="xm-trust-badge">
          <span class="xm-trust-badge__icon" aria-hidden="true">↩</span>
          <span><{$smarty.const._MD_SUBSCRIPTIONS_TRUST_CANCEL}></span>
        </div>
        <div class="xm-trust-badge">
          <span class="xm-trust-badge__icon" aria-hidden="true">&#x1F9FE;</span>
          <span><{$smarty.const._MD_SUBSCRIPTIONS_TRUST_INVOICE}></span>
        </div>
      </div>

    </main>

    <{* ── RIGHT: Order summary sidebar ──────────────────────────── *}>
    <aside class="xm-order-summary" aria-label="Order summary">

      <div class="xm-order-summary__plan-name">
        <{$xm_plan_name|escape}>
      </div>

      <{if $xm_trial_days > 0}>
      <div class="xm-trial-badge">
        <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24"
             fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
             aria-hidden="true"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
        <{$xm_trial_badge|escape}>
      </div>
      <{/if}>

      <div class="xm-price-breakdown">
        <div class="xm-price-row">
          <span><{$smarty.const._MD_SUBSCRIPTIONS_SUBTOTAL}></span>
          <span><{$xm_price}></span>
        </div>

        <{if $xm_discount_raw > 0}>
        <div class="xm-price-row xm-price-row--discount">
          <span>
            <{$smarty.const._MD_SUBSCRIPTIONS_DISCOUNT}>
            <{if $xm_coupon_code}>
            <span class="xm-coupon-tag"><{$xm_coupon_code|escape}></span>
            <{/if}>
          </span>
          <span>−<{$xm_discount}></span>
        </div>
        <{/if}>

        <{if $xm_tax}>
        <div class="xm-price-row">
          <span><{$smarty.const._MD_SUBSCRIPTIONS_TAX}></span>
          <span><{$xm_tax}></span>
        </div>
        <{/if}>

        <div class="xm-price-row xm-price-row--total">
          <span><strong><{$smarty.const._MD_SUBSCRIPTIONS_TOTAL}></strong></span>
          <span><strong><{$xm_total}></strong></span>
        </div>
      </div>

      <{* What's included *}>
      <{if isset($xm_plan_features) && $xm_plan_features}>
      <div class="xm-summary-features">
        <p class="xm-summary-features__label"><{$smarty.const._MD_SUBSCRIPTIONS_WHATS_INCLUDED}>:</p>
        <ul class="xm-feature-list">
          <{foreach item=feat from=$xm_plan_features}>
          <li class="xm-feature-list__item">
            <span class="xm-feature-list__check" aria-hidden="true">✓</span>
            <{$feat|escape}>
          </li>
          <{/foreach}>
        </ul>
      </div>
      <{/if}>

    </aside>

  </div>
</div>

<{* ── Inline CSS for new checkout-specific styles ──────────────── *}>
<style>
/* ── Checkout header ── */
.xm-checkout-header {
  margin-bottom: 2rem;
}
.xm-back-link {
  display: inline-flex;
  align-items: center;
  gap: 0.375rem;
  font-size: 0.875rem;
  color: var(--xm-text-muted);
  text-decoration: none;
  margin-bottom: 1rem;
  transition: color var(--xm-transition);
}
.xm-back-link:hover { color: var(--xm-primary); }
.xm-checkout-title {
  font-size: clamp(1.5rem, 3vw, 2rem);
  font-weight: 800;
  margin: 0;
  color: var(--xm-text);
}

/* ── Layout override (sidebar on right for checkout) ── */
.xm-checkout-layout {
  display: grid;
  grid-template-columns: 2fr 1fr;
  gap: 2rem;
  align-items: start;
}
@media (max-width: 720px) {
  .xm-checkout-layout {
    grid-template-columns: 1fr;
  }
  .xm-order-summary {
    order: -1;  /* summary above form on mobile */
  }
}

/* ── Checkout sections (card wrappers) ── */
.xm-checkout-section {
  background: var(--xm-bg);
  border: 1.5px solid var(--xm-border);
  border-radius: var(--xm-radius-lg);
  padding: 1.5rem;
  margin-bottom: 1.25rem;
  box-shadow: var(--xm-shadow);
}
.xm-checkout-section--pay {
  border-color: var(--xm-primary);
  box-shadow: 0 0 0 3px rgba(59,130,246,.08), var(--xm-shadow-md);
}
.xm-checkout-section__title {
  display: flex;
  align-items: center;
  gap: 0.5rem;
  font-size: 0.9375rem;
  font-weight: 700;
  margin: 0 0 1.25rem;
  color: var(--xm-text);
  text-transform: uppercase;
  letter-spacing: 0.04em;
}

/* ── Coupon ── */
.xm-coupon-input { font-family: var(--xm-font-mono); letter-spacing: 0.05em; text-transform: uppercase; }
.xm-coupon-btn   { white-space: nowrap; flex-shrink: 0; }
.xm-input--success { border-color: var(--xm-success) !important; }
.xm-input--error   { border-color: var(--xm-danger)  !important; }
.xm-coupon-success {
  display: flex;
  align-items: center;
  gap: 0.375rem;
  margin-top: 0.5rem;
  font-size: 0.875rem;
  color: var(--xm-success);
  font-weight: 600;
}
.xm-coupon-success svg { flex-shrink: 0; }

/* ── Gateway pill ── */
.xm-gateway-pill {
  display: inline-flex;
  align-items: center;
  gap: 0.375rem;
  font-size: 0.8125rem;
  color: var(--xm-text-muted);
  background: var(--xm-bg-soft);
  border: 1px solid var(--xm-border);
  border-radius: 9999px;
  padding: 0.3rem 0.875rem;
  margin-bottom: 1.25rem;
}

/* ── Pay button ── */
.xm-pay-btn {
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 0.625rem;
  padding: 1rem 1.75rem;
  font-size: 1.0625rem;
  font-weight: 700;
  border-radius: var(--xm-radius-lg);
  background: linear-gradient(135deg, var(--xm-primary) 0%, #6366f1 100%);
  box-shadow: 0 4px 14px rgba(59,130,246,.4);
  transition: transform 0.12s ease, box-shadow 0.12s ease, filter 0.12s ease;
  letter-spacing: 0.01em;
  position: relative;
  overflow: hidden;
}
.xm-pay-btn::after {
  content: '';
  position: absolute;
  inset: 0;
  background: linear-gradient(180deg, rgba(255,255,255,.12) 0%, transparent 100%);
  pointer-events: none;
}
.xm-pay-btn:hover, .xm-pay-btn:focus-visible {
  transform: translateY(-1px);
  box-shadow: 0 6px 20px rgba(59,130,246,.5);
  filter: brightness(1.05);
  background: linear-gradient(135deg, var(--xm-primary) 0%, #6366f1 100%);
  color: #fff !important;
}
.xm-pay-btn:active {
  transform: translateY(0);
  box-shadow: 0 3px 10px rgba(59,130,246,.35);
}
.xm-pay-btn__icon  { font-size: 1.1rem; }
.xm-pay-btn__label { flex: 1; text-align: center; }
.xm-pay-btn__amount {
  background: rgba(255,255,255,.2);
  border-radius: 0.375rem;
  padding: 0.15rem 0.625rem;
  font-size: 0.9375rem;
  font-weight: 700;
  white-space: nowrap;
}

/* ── Secure note ── */
.xm-secure-note {
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 0.375rem;
  margin-top: 0.875rem;
  font-size: 0.8125rem;
  color: var(--xm-text-muted);
}
.xm-secure-note svg { flex-shrink: 0; }

/* ── Trust badges ── */
.xm-trust-row {
  display: flex;
  gap: 0.75rem;
  flex-wrap: wrap;
  justify-content: center;
  margin-top: 0.25rem;
}
.xm-trust-badge {
  display: flex;
  align-items: center;
  gap: 0.35rem;
  font-size: 0.75rem;
  color: var(--xm-text-muted);
  background: var(--xm-bg-soft);
  border: 1px solid var(--xm-border);
  border-radius: 9999px;
  padding: 0.3rem 0.75rem;
}
.xm-trust-badge__icon { font-size: 0.875rem; }

/* ── Order summary sidebar ── */
.xm-order-summary {
  background: var(--xm-bg-soft);
  border: 1.5px solid var(--xm-border);
  border-radius: var(--xm-radius-lg);
  padding: 1.5rem;
  position: sticky;
  top: 1rem;
  box-shadow: var(--xm-shadow-md);
}
.xm-order-summary__plan-name {
  font-size: 1.375rem;
  font-weight: 800;
  color: var(--xm-text);
  margin-bottom: 0.75rem;
  line-height: 1.25;
}

/* ── Trial badge ── */
.xm-trial-badge {
  display: inline-flex;
  align-items: center;
  gap: 0.375rem;
  background: #dcfce7;
  color: #166534;
  border: 1px solid #86efac;
  border-radius: 9999px;
  font-size: 0.8125rem;
  font-weight: 600;
  padding: 0.3rem 0.875rem;
  margin-bottom: 1rem;
}

/* ── Price breakdown ── */
.xm-price-breakdown {
  margin-bottom: 1.25rem;
  border-top: 1px solid var(--xm-border);
  padding-top: 1rem;
}
.xm-price-row {
  display: flex;
  justify-content: space-between;
  align-items: baseline;
  gap: 0.5rem;
  padding: 0.4rem 0;
  font-size: 0.9375rem;
  color: var(--xm-text-muted);
}
.xm-price-row--discount {
  color: var(--xm-success);
  font-weight: 600;
}
.xm-price-row--total {
  border-top: 2px solid var(--xm-border);
  margin-top: 0.5rem;
  padding-top: 0.75rem;
  font-size: 1.1875rem;
  color: var(--xm-text);
}
.xm-coupon-tag {
  display: inline-block;
  background: #dcfce7;
  color: #166534;
  border-radius: 0.25rem;
  font-size: 0.7rem;
  font-family: var(--xm-font-mono);
  padding: 0.05rem 0.375rem;
  font-weight: 700;
  letter-spacing: 0.04em;
  vertical-align: middle;
  margin-left: 0.25rem;
}

/* ── Summary features ── */
.xm-summary-features {
  border-top: 1px solid var(--xm-border);
  padding-top: 1rem;
}
.xm-summary-features__label {
  font-size: 0.75rem;
  font-weight: 700;
  text-transform: uppercase;
  letter-spacing: 0.06em;
  color: var(--xm-text-muted);
  margin-bottom: 0.625rem;
}

/* ── Spinner on pay-form submit ── */
@keyframes xm-spin { to { transform: rotate(360deg); } }
.xm-pay-btn.is-loading .xm-pay-btn__label::after {
  content: '';
  display: inline-block;
  width: 14px; height: 14px;
  border: 2px solid rgba(255,255,255,.4);
  border-top-color: #fff;
  border-radius: 50%;
  animation: xm-spin 0.65s linear infinite;
  margin-left: 0.5rem;
  vertical-align: middle;
}
</style>

<{* ── Pay button loading state ─────────────────────────────────── *}>
<script>
(function () {
  var form = document.getElementById('pay-form');
  var btn  = document.getElementById('pay-btn');
  if (!form || !btn) return;
  form.addEventListener('submit', function () {
    btn.classList.add('is-loading');
    btn.disabled = true;
  });
})();
</script>

<{if $xm_is_admin}>
<div class="xm-admin-bar">
  <a href="<{$xm_admin_url}>" class="xm-admin-bar__link">
    <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24"
         fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
         aria-hidden="true"><line x1="4" y1="6" x2="20" y2="6"/><line x1="4" y1="12" x2="20" y2="12"/><line x1="4" y1="18" x2="20" y2="18"/><circle cx="8" cy="6" r="2"/><circle cx="16" cy="12" r="2"/><circle cx="8" cy="18" r="2"/></svg>
    Subscriptions Admin
  </a>
</div>
<{/if}>
