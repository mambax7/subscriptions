<{* Subscriptions - Plans listing *}>
<{assign var="xm_module_url" value="`$xoops_url`/modules/subscriptions"}>

<div class="subscriptions-wrap">

  <{* ── Access-denied banner ────────────────────────────────────── *}>
  <{if $xm_required_module}>
  <div class="xm-notice xm-notice--warning" role="alert">
    <span class="xm-notice__icon" aria-hidden="true">&#x1F512;</span>
    <div>
      <div class="xm-notice__title"><{$smarty.const._MD_SUBSCRIPTIONS_ACCESS_DENIED_TITLE}></div>
      <{$smarty.const._MD_SUBSCRIPTIONS_ACCESS_DENIED}>
    </div>
  </div>
  <{/if}>

  <{* ── Hero ────────────────────────────────────────────────────── *}>
  <section class="xm-hero" aria-label="Plans hero">
    <div class="xm-hero__eyebrow"><{$smarty.const._MD_SUBSCRIPTIONS_PLANS_EYEBROW}></div>
    <h1 class="xm-hero__title"><{$smarty.const._MD_SUBSCRIPTIONS_PLANS_TITLE}></h1>
    <p class="xm-hero__sub"><{$smarty.const._MD_SUBSCRIPTIONS_CHOOSE_PLAN}></p>

    <{* Billing toggle — only shown when at least one plan has an annual discount *}>
    <{assign var="has_annual" value=false}>
    <{foreach item=plan from=$xm_plans}>
      <{if $plan.discount_annual > 0}><{assign var="has_annual" value=true}><{/if}>
    <{/foreach}>
    <{if $has_annual}>
    <div class="xm-billing-toggle-wrap" role="group" aria-label="Billing frequency">
      <span class="xm-billing-toggle-label xm-billing-toggle-label--monthly" id="xm-lbl-monthly">
        <{$smarty.const._MD_SUBSCRIPTIONS_BILLING_MONTHLY}>
      </span>
      <label class="xm-toggle" for="xm-billing-toggle" aria-label="Switch to annual billing">
        <input type="checkbox" id="xm-billing-toggle" class="xm-toggle__input">
        <span class="xm-toggle__track"></span>
      </label>
      <span class="xm-billing-toggle-label xm-billing-toggle-label--annual" id="xm-lbl-annual">
        <{$smarty.const._MD_SUBSCRIPTIONS_BILLING_ANNUAL}>
        <span class="xm-save-pill"><{$smarty.const._MD_SUBSCRIPTIONS_SAVE_UP_TO}></span>
      </span>
    </div>
    <{/if}>
  </section>

  <{* ── Plans grid ──────────────────────────────────────────────── *}>
  <div class="xm-plans-grid">
    <{foreach item=plan from=$xm_plans}>

    <article class="xm-plan-card<{if $plan.is_featured}> xm-plan-card--featured<{/if}>"
             role="article" aria-label="<{$plan.name|escape}> plan">

      <{* Featured ribbon *}>
      <{if $plan.is_featured}>
      <div class="xm-plan-card__badge" role="note" aria-label="Most popular">
        <span aria-hidden="true">★</span> <{$smarty.const._MD_SUBSCRIPTIONS_FEATURED}>
      </div>
      <{/if}>

      <{* Header: name + price *}>
      <div class="xm-plan-card__header">
        <h2 class="xm-plan-card__name"><{$plan.name|escape}></h2>

        <{* Monthly price *}>
        <div class="xm-plan-card__price-wrap" data-price-monthly>
          <div class="xm-plan-card__price" aria-label="<{$plan.price}> per <{$plan.billing_cycle}>">
            <span class="xm-plan-card__amount"><{$plan.price}></span>
            <span class="xm-plan-card__cycle">/<{$plan.billing_cycle}></span>
          </div>
          <{if $plan.discount_annual > 0}>
          <div class="xm-plan-card__annual-hint"><{$plan.annual_save_badge|escape}> if billed annually</div>
          <{/if}>
        </div>

        <{* Annual price (hidden by default, shown by toggle) *}>
        <{if $plan.discount_annual > 0}>
        <div class="xm-plan-card__price-wrap" data-price-annual style="display:none">
          <div class="xm-plan-card__price">
            <span class="xm-plan-card__amount"><{$plan.annual_price}></span>
            <span class="xm-plan-card__cycle">/<{$smarty.const._MD_SUBSCRIPTIONS_CYCLE_ANNUAL}></span>
          </div>
          <div class="xm-plan-card__save-badge"><{$plan.annual_save_badge|escape}></div>
        </div>
        <{/if}>

        <{* Extras *}>
        <{if $plan.setup_fee_display}>
        <div class="xm-plan-card__setup">
          <{$smarty.const._MD_SUBSCRIPTIONS_SETUP_FEE}>: <{$plan.setup_fee_display}>
        </div>
        <{/if}>

        <{if $plan.trial_days > 0}>
        <div class="xm-plan-card__trial">
          <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24"
               fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"
               aria-hidden="true"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
          <{$plan.trial_badge|escape}>
        </div>
        <{/if}>
      </div>

      <{* Body: description + features *}>
      <div class="xm-plan-card__body">
        <{if $plan.description}>
        <p class="xm-plan-card__desc"><{$plan.description|escape}></p>
        <{/if}>

        <{if $plan.features}>
        <ul class="xm-feature-list" aria-label="<{$plan.name|escape}> features">
          <{foreach item=feat from=$plan.features}>
          <li class="xm-feature-list__item">
            <svg class="xm-feature-list__check" xmlns="http://www.w3.org/2000/svg"
                 width="14" height="14" viewBox="0 0 24 24"
                 fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"
                 aria-hidden="true"><polyline points="20 6 9 17 4 12"/></svg>
            <{$feat|escape}>
          </li>
          <{/foreach}>
        </ul>
        <{/if}>

        <{if $plan.pricing_model eq 'usage' or $plan.pricing_model eq 'hybrid'}>
        <p class="xm-plan-card__usage">
          + <{$plan.usage_price}> per <{$plan.usage_unit|escape}>
        </p>
        <{/if}>
      </div>

      <{* Footer: CTA *}>
      <div class="xm-plan-card__footer">
        <{if $xm_is_logged_in}>
          <{if $plan.trial_days > 0}>
          <a href="<{$plan.checkout_url}>"
             class="xm-btn xm-btn--primary xm-btn--full<{if $plan.is_featured}> xm-btn--featured<{/if}>"
             aria-label="<{$plan.trial_badge|escape}> — <{$plan.name|escape}>">
            <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 24 24"
                 fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"
                 aria-hidden="true"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
            <{$smarty.const._MD_SUBSCRIPTIONS_FREE_TRIAL}>
          </a>
          <{else}>
          <a href="<{$plan.checkout_url}>"
             class="xm-btn xm-btn--primary xm-btn--full<{if $plan.is_featured}> xm-btn--featured<{/if}>"
             aria-label="<{$smarty.const._MD_SUBSCRIPTIONS_SUBSCRIBE}> — <{$plan.name|escape}>">
            <{$smarty.const._MD_SUBSCRIPTIONS_SUBSCRIBE}>
            <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 24 24"
                 fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"
                 aria-hidden="true"><line x1="5" y1="12" x2="19" y2="12"/><polyline points="12 5 19 12 12 19"/></svg>
          </a>
          <{/if}>
        <{else}>
          <a href="<{$xoops_url}>/user.php?xoops_redirect=<{$xm_module_url}>/checkout.php?plan=<{$plan.plan_id}>"
             class="xm-btn xm-btn--primary xm-btn--full<{if $plan.is_featured}> xm-btn--featured<{/if}>">
            <{$smarty.const._MD_SUBSCRIPTIONS_LOGIN_TO_SUBSCRIBE}>
          </a>
        <{/if}>
      </div>

    </article>

    <{foreachelse}>
    <div class="xm-empty-state" role="status">
      <div class="xm-empty-state__icon" aria-hidden="true">&#x1F4CB;</div>
      <p><{$smarty.const._MD_SUBSCRIPTIONS_NO_SUBSCRIPTION}></p>
    </div>
    <{/foreach}>
  </div>

  <{* ── Trust bar ─────────────────────────────────────────────── *}>
  <div class="xm-trust-bar" aria-label="Security and trust indicators">
    <div class="xm-trust-bar__item">
      <span class="xm-trust-bar__icon" aria-hidden="true">&#x1F512;</span>
      <span><{$smarty.const._MD_SUBSCRIPTIONS_TRUST_SSL}></span>
    </div>
    <div class="xm-trust-bar__item">
      <span class="xm-trust-bar__icon" aria-hidden="true">↩</span>
      <span><{$smarty.const._MD_SUBSCRIPTIONS_TRUST_CANCEL}></span>
    </div>
    <div class="xm-trust-bar__item">
      <span class="xm-trust-bar__icon" aria-hidden="true">&#x1F9FE;</span>
      <span><{$smarty.const._MD_SUBSCRIPTIONS_TRUST_INVOICE}></span>
    </div>
    <div class="xm-trust-bar__item">
      <span class="xm-trust-bar__icon" aria-hidden="true">&#x1F504;</span>
      <span><{$smarty.const._MD_SUBSCRIPTIONS_TRUST_RENEWS}></span>
    </div>
  </div>

</div>

<{* ── Inline CSS for plans-page-specific styles ─────────────────── *}>
<style>
/* ── Hero ── */
.xm-hero {
  text-align: center;
  padding: 3.5rem 1rem 2.5rem;
}
.xm-hero__eyebrow {
  display: inline-block;
  font-size: 0.75rem;
  font-weight: 700;
  text-transform: uppercase;
  letter-spacing: 0.1em;
  color: var(--xm-primary);
  background: rgba(59,130,246,.08);
  border: 1px solid rgba(59,130,246,.2);
  border-radius: 9999px;
  padding: 0.3rem 1rem;
  margin-bottom: 1rem;
}
.xm-hero__title {
  font-size: clamp(2rem, 5vw, 3rem);
  font-weight: 800;
  margin-bottom: 0.75rem;
  color: var(--xm-text);
  line-height: 1.15;
  letter-spacing: -0.02em;
}
.xm-hero__sub {
  font-size: 1.125rem;
  color: var(--xm-text-muted);
  margin-bottom: 1.75rem;
}

/* ── Billing toggle ── */
.xm-billing-toggle-wrap {
  display: inline-flex;
  align-items: center;
  gap: 0.75rem;
  background: var(--xm-bg-soft);
  border: 1.5px solid var(--xm-border);
  border-radius: 9999px;
  padding: 0.4rem 1.25rem;
}
.xm-billing-toggle-label {
  font-size: 0.875rem;
  font-weight: 600;
  color: var(--xm-text-muted);
}
.xm-billing-toggle-label--annual {
  display: flex;
  align-items: center;
  gap: 0.4rem;
}
.xm-save-pill {
  display: inline-block;
  background: var(--xm-success);
  color: #fff;
  font-size: 0.675rem;
  font-weight: 700;
  border-radius: 9999px;
  padding: 0.1rem 0.5rem;
  line-height: 1.6;
}
.xm-toggle {
  position: relative;
  width: 44px;
  height: 24px;
  cursor: pointer;
  flex-shrink: 0;
}
.xm-toggle__input {
  position: absolute;
  opacity: 0;
  width: 0; height: 0;
}
.xm-toggle__track {
  position: absolute;
  inset: 0;
  background: var(--xm-border);
  border-radius: 9999px;
  transition: background 0.2s;
}
.xm-toggle__track::after {
  content: '';
  position: absolute;
  top: 3px; left: 3px;
  width: 18px; height: 18px;
  background: #fff;
  border-radius: 50%;
  transition: transform 0.2s;
  box-shadow: 0 1px 3px rgba(0,0,0,.2);
}
.xm-toggle__input:checked + .xm-toggle__track { background: var(--xm-primary); }
.xm-toggle__input:checked + .xm-toggle__track::after { transform: translateX(20px); }
.xm-toggle__input:focus-visible + .xm-toggle__track {
  outline: 3px solid var(--xm-primary);
  outline-offset: 2px;
}

/* ── Plans grid ── */
.xm-plans-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
  gap: 1.5rem;
  margin-top: 1rem;
  align-items: stretch;
}

/* ── Plan card ── */
.xm-plan-card {
  background: var(--xm-bg);
  border: 2px solid var(--xm-border);
  border-radius: var(--xm-radius-lg);
  display: flex;
  flex-direction: column;
  position: relative;
  overflow: hidden;
  transition: box-shadow 0.2s ease, transform 0.2s ease, border-color 0.2s ease;
}
.xm-plan-card:hover {
  box-shadow: var(--xm-shadow-lg);
  transform: translateY(-3px);
  border-color: #cbd5e1;
}
.xm-plan-card--featured {
  border-color: var(--xm-primary);
  box-shadow: 0 0 0 4px rgba(59,130,246,.12), var(--xm-shadow-md);
}
.xm-plan-card--featured:hover {
  box-shadow: 0 0 0 4px rgba(59,130,246,.2), var(--xm-shadow-lg);
  border-color: var(--xm-primary);
}

/* featured ribbon */
.xm-plan-card__badge {
  background: var(--xm-primary);
  color: #fff;
  font-size: 0.6875rem;
  font-weight: 700;
  letter-spacing: 0.06em;
  text-transform: uppercase;
  padding: 0.3rem 1.25rem 0.3rem 1rem;
  position: absolute;
  top: 0; right: 0;
  border-radius: 0 var(--xm-radius-lg) 0 var(--xm-radius-lg);
  display: flex;
  align-items: center;
  gap: 0.3rem;
}

/* header */
.xm-plan-card__header {
  padding: 2rem 1.5rem 1rem;
  text-align: center;
}
.xm-plan-card--featured .xm-plan-card__header { padding-top: 2.5rem; }
.xm-plan-card__name {
  font-size: 1.1875rem;
  font-weight: 700;
  margin-bottom: 1.25rem;
  color: var(--xm-text);
}
.xm-plan-card__price-wrap { margin-bottom: 0.25rem; }
.xm-plan-card__price {
  display: flex;
  align-items: baseline;
  justify-content: center;
  gap: 0.25rem;
}
.xm-plan-card__amount {
  font-size: 2.75rem;
  font-weight: 800;
  color: var(--xm-primary);
  line-height: 1;
  letter-spacing: -0.03em;
}
.xm-plan-card__cycle {
  font-size: 0.9375rem;
  color: var(--xm-text-muted);
  font-weight: 500;
  align-self: flex-end;
  padding-bottom: 0.2rem;
}
.xm-plan-card__annual-hint {
  font-size: 0.8rem;
  color: var(--xm-success);
  font-weight: 500;
  margin-top: 0.25rem;
}
.xm-plan-card__save-badge {
  display: inline-block;
  background: #dcfce7;
  color: #166534;
  border-radius: 9999px;
  font-size: 0.75rem;
  font-weight: 700;
  padding: 0.2rem 0.75rem;
  margin-top: 0.375rem;
}
.xm-plan-card__trial {
  display: inline-flex;
  align-items: center;
  gap: 0.35rem;
  background: #eff6ff;
  color: #1e40af;
  border: 1px solid #bfdbfe;
  font-size: 0.8rem;
  font-weight: 600;
  border-radius: 9999px;
  padding: 0.25rem 0.875rem;
  margin-top: 0.625rem;
}
.xm-plan-card__setup {
  font-size: 0.8125rem;
  color: var(--xm-text-muted);
  margin-top: 0.375rem;
}

/* body */
.xm-plan-card__body {
  flex: 1;
  padding: 0.75rem 1.5rem 1rem;
  border-top: 1px solid var(--xm-border);
}
.xm-plan-card__desc {
  color: var(--xm-text-muted);
  font-size: 0.9375rem;
  margin-bottom: 1rem;
  line-height: 1.55;
}
.xm-plan-card__usage {
  font-size: 0.875rem;
  color: var(--xm-text-muted);
  font-style: italic;
  margin-top: 0.75rem;
}

/* feature list — SVG checkmark variant */
.xm-feature-list {
  list-style: none;
  padding: 0;
  margin: 0;
}
.xm-feature-list__item {
  display: flex;
  align-items: flex-start;
  gap: 0.625rem;
  padding: 0.4rem 0;
  font-size: 0.9375rem;
  border-bottom: 1px solid var(--xm-border);
}
.xm-feature-list__item:last-child { border-bottom: none; }
.xm-feature-list__check {
  color: var(--xm-success);
  flex-shrink: 0;
  margin-top: 0.2rem;
}

/* footer */
.xm-plan-card__footer {
  padding: 1rem 1.5rem 1.5rem;
}

/* featured CTA */
.xm-btn--featured {
  background: linear-gradient(135deg, var(--xm-primary) 0%, #6366f1 100%);
  box-shadow: 0 4px 14px rgba(59,130,246,.35);
}
.xm-btn--featured:hover, .xm-btn--featured:visited {
  background: linear-gradient(135deg, var(--xm-primary-dark) 0%, #4f46e5 100%);
  box-shadow: 0 6px 20px rgba(59,130,246,.45);
  color: #fff !important;
}

/* ── Trust bar ── */
.xm-trust-bar {
  display: flex;
  flex-wrap: wrap;
  justify-content: center;
  gap: 1rem 2.5rem;
  margin-top: 2.5rem;
  padding-top: 2rem;
  border-top: 1px solid var(--xm-border);
}
.xm-trust-bar__item {
  display: flex;
  align-items: center;
  gap: 0.5rem;
  font-size: 0.8125rem;
  color: var(--xm-text-muted);
  font-weight: 500;
}
.xm-trust-bar__icon { font-size: 1rem; }

/* ── Responsive ── */
@media (max-width: 640px) {
  .xm-plans-grid { grid-template-columns: 1fr; }
  .xm-hero { padding: 2rem 0.5rem 1.5rem; }
}
</style>

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
