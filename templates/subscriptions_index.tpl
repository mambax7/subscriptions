<{* Subscriptions – Module landing page *}>
<{assign var="xm_module_url" value="`$xoops_url`/modules/subscriptions"}>

<div class="subscriptions-wrap xm-landing">

  <{* ── Active-subscriber banner ──────────────────────────────── *}>
  <{if $xm_current_sub}>
  <div class="xm-notice xm-notice--success xm-sub-banner" role="status">
    <span class="xm-notice__icon" aria-hidden="true">✓</span>
    <div>
      <strong><{$xm_current_sub.plan_name|escape}></strong> — <{$xm_current_sub.status}>
      &nbsp;·&nbsp; <{$smarty.const._MD_SUBSCRIPTIONS_RENEWS_ON}> <{$xm_current_sub.period_end}>
    </div>
    <a href="<{$xm_current_sub.dashboard_url}>" class="xm-btn xm-btn--sm xm-btn--secondary xm-sub-banner__btn">
      <{$smarty.const._MD_SUBSCRIPTIONS_DASHBOARD_TITLE}> →
    </a>
  </div>
  <{/if}>

  <{* ── Hero ──────────────────────────────────────────────────── *}>
  <section class="xm-landing-hero" aria-label="Membership hero">
    <div class="xm-landing-hero__inner">
      <div class="xm-hero__eyebrow"><{$smarty.const._MD_SUBSCRIPTIONS_PLANS_EYEBROW}></div>
      <h1 class="xm-landing-hero__title"><{$smarty.const._MD_SUBSCRIPTIONS_INDEX_HEADLINE}></h1>
      <p class="xm-landing-hero__sub"><{$smarty.const._MD_SUBSCRIPTIONS_INDEX_SUBHEADLINE}></p>
      <div class="xm-landing-hero__ctas">
        <a href="#xm-plans" class="xm-btn xm-btn--hero-primary">
          <{$smarty.const._MD_SUBSCRIPTIONS_INDEX_CTA_PRIMARY}>
          <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24"
               fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"
               aria-hidden="true"><line x1="12" y1="5" x2="12" y2="19"/><polyline points="19 12 12 19 5 12"/></svg>
        </a>
        <{if !$xm_is_logged_in}>
        <a href="<{$xoops_url}>/user.php" class="xm-btn xm-btn--hero-secondary">
          <{$smarty.const._MD_SUBSCRIPTIONS_INDEX_CTA_LOGIN}>
        </a>
        <{/if}>
      </div>
    </div>
    <div class="xm-landing-hero__bg" aria-hidden="true"></div>
  </section>

  <{* ── Value props ────────────────────────────────────────────── *}>
  <div class="xm-value-props" role="list">
    <div class="xm-value-prop" role="listitem">
      <div class="xm-value-prop__icon" aria-hidden="true">⚡</div>
      <div class="xm-value-prop__text">
        <strong><{$smarty.const._MD_SUBSCRIPTIONS_PROP1_TITLE}></strong>
        <span><{$smarty.const._MD_SUBSCRIPTIONS_PROP1_DESC}></span>
      </div>
    </div>
    <div class="xm-value-prop" role="listitem">
      <div class="xm-value-prop__icon" aria-hidden="true">&#x1F512;</div>
      <div class="xm-value-prop__text">
        <strong><{$smarty.const._MD_SUBSCRIPTIONS_PROP2_TITLE}></strong>
        <span><{$smarty.const._MD_SUBSCRIPTIONS_PROP2_DESC}></span>
      </div>
    </div>
    <div class="xm-value-prop" role="listitem">
      <div class="xm-value-prop__icon" aria-hidden="true">↩</div>
      <div class="xm-value-prop__text">
        <strong><{$smarty.const._MD_SUBSCRIPTIONS_PROP3_TITLE}></strong>
        <span><{$smarty.const._MD_SUBSCRIPTIONS_PROP3_DESC}></span>
      </div>
    </div>
  </div>

  <{* ── Section heading ────────────────────────────────────────── *}>
  <div class="xm-plans-heading" id="xm-plans">
    <h2 class="xm-plans-heading__title"><{$smarty.const._MD_SUBSCRIPTIONS_CHOOSE_PLAN}></h2>
    <p class="xm-plans-heading__sub"><{$smarty.const._MD_SUBSCRIPTIONS_INDEX_PLANS_SUB}></p>
  </div>

  <{* ── Plan cards ─────────────────────────────────────────────── *}>
  <div class="xm-plans-grid">
    <{foreach item=plan from=$xm_plans}>

    <article class="xm-plan-card<{if $plan.is_featured}> xm-plan-card--featured<{/if}>"
             role="article" aria-label="<{$plan.name|escape}> plan">

      <{if $plan.is_featured}>
      <div class="xm-plan-card__badge" role="note">
        <span aria-hidden="true">★</span> <{$smarty.const._MD_SUBSCRIPTIONS_FEATURED}>
      </div>
      <{/if}>

      <div class="xm-plan-card__header">
        <h3 class="xm-plan-card__name"><{$plan.name|escape}></h3>
        <div class="xm-plan-card__price">
          <span class="xm-plan-card__amount"><{$plan.price}></span>
          <span class="xm-plan-card__cycle">/<{$plan.billing_cycle}></span>
        </div>
        <{if $plan.trial_days > 0}>
        <div class="xm-plan-card__trial">
          <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24"
               fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"
               aria-hidden="true"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
          <{$plan.trial_badge|escape}>
        </div>
        <{/if}>
      </div>

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
                 fill="none" stroke="currentColor" stroke-width="3"
                 stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
              <polyline points="20 6 9 17 4 12"/>
            </svg>
            <{$feat|escape}>
          </li>
          <{/foreach}>
        </ul>
        <{/if}>
      </div>

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
          <a href="<{$xoops_url}>/user.php?xoops_redirect=<{$xm_module_url}>/plans.php"
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

  <{* ── Trust bar ──────────────────────────────────────────────── *}>
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

<style>
/* ── Landing wrapper ── */
.xm-landing { padding-top: 0; }

/* ── Subscriber banner ── */
.xm-sub-banner {
  display: flex;
  align-items: center;
  gap: 1rem;
  flex-wrap: wrap;
  margin: 1rem 0 0;
  border-radius: var(--xm-radius);
}
.xm-sub-banner__btn { margin-left: auto; flex-shrink: 0; }

/* ── Hero ── */
.xm-landing-hero {
  position: relative;
  text-align: center;
  padding: 4rem 1rem 3rem;
  overflow: hidden;
  margin-bottom: 0;
}
.xm-landing-hero__bg {
  position: absolute;
  inset: 0;
  background: linear-gradient(160deg, #eff6ff 0%, #eef2ff 50%, #f0fdf4 100%);
  z-index: 0;
}
.xm-landing-hero__inner {
  position: relative;
  z-index: 1;
  max-width: 680px;
  margin: 0 auto;
}
.xm-hero__eyebrow {
  display: inline-block;
  font-size: 0.75rem;
  font-weight: 700;
  text-transform: uppercase;
  letter-spacing: 0.1em;
  color: var(--xm-primary);
  background: rgba(59,130,246,.1);
  border: 1px solid rgba(59,130,246,.2);
  border-radius: 9999px;
  padding: 0.3rem 1rem;
  margin-bottom: 1.25rem;
}
.xm-landing-hero__title {
  font-size: clamp(2rem, 5.5vw, 3.25rem);
  font-weight: 900;
  line-height: 1.1;
  letter-spacing: -0.03em;
  color: var(--xm-text);
  margin-bottom: 1rem;
}
.xm-landing-hero__sub {
  font-size: 1.125rem;
  color: var(--xm-text-muted);
  line-height: 1.6;
  margin-bottom: 2rem;
  max-width: 520px;
  margin-left: auto;
  margin-right: auto;
}
.xm-landing-hero__ctas {
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 0.875rem;
  flex-wrap: wrap;
}

/* Hero CTAs */
.xm-btn--hero-primary {
  display: inline-flex;
  align-items: center;
  gap: 0.5rem;
  padding: 0.875rem 2rem;
  font-size: 1.0625rem;
  font-weight: 700;
  border-radius: var(--xm-radius-lg);
  background: linear-gradient(135deg, var(--xm-primary) 0%, #6366f1 100%);
  color: #fff !important;
  text-decoration: none !important;
  box-shadow: 0 4px 16px rgba(59,130,246,.4);
  transition: transform 0.15s ease, box-shadow 0.15s ease;
  border: none;
  cursor: pointer;
}
.xm-btn--hero-primary:hover {
  transform: translateY(-2px);
  box-shadow: 0 6px 22px rgba(59,130,246,.5);
  color: #fff !important;
}
.xm-btn--hero-secondary {
  display: inline-flex;
  align-items: center;
  gap: 0.5rem;
  padding: 0.875rem 1.75rem;
  font-size: 1.0625rem;
  font-weight: 600;
  border-radius: var(--xm-radius-lg);
  background: rgba(255,255,255,.9);
  color: var(--xm-text) !important;
  text-decoration: none !important;
  border: 1.5px solid var(--xm-border);
  box-shadow: var(--xm-shadow);
  transition: background 0.15s ease, border-color 0.15s ease;
  cursor: pointer;
}
.xm-btn--hero-secondary:hover {
  background: #fff;
  border-color: var(--xm-primary);
  color: var(--xm-primary) !important;
}

/* ── Value props ── */
.xm-value-props {
  display: flex;
  gap: 0;
  background: var(--xm-bg);
  border-top: 1px solid var(--xm-border);
  border-bottom: 1px solid var(--xm-border);
  margin-bottom: 3rem;
}
.xm-value-prop {
  flex: 1;
  display: flex;
  align-items: flex-start;
  gap: 0.875rem;
  padding: 1.25rem 1.5rem;
  border-right: 1px solid var(--xm-border);
}
.xm-value-prop:last-child { border-right: none; }
.xm-value-prop__icon {
  font-size: 1.5rem;
  line-height: 1;
  flex-shrink: 0;
  margin-top: 0.1rem;
}
.xm-value-prop__text {
  display: flex;
  flex-direction: column;
  gap: 0.2rem;
  font-size: 0.875rem;
}
.xm-value-prop__text strong {
  color: var(--xm-text);
  font-weight: 700;
  font-size: 0.9375rem;
}
.xm-value-prop__text span { color: var(--xm-text-muted); }

/* ── Plans section heading ── */
.xm-plans-heading {
  text-align: center;
  margin-bottom: 2rem;
  scroll-margin-top: 1.5rem;
}
.xm-plans-heading__title {
  font-size: clamp(1.375rem, 3vw, 1.875rem);
  font-weight: 800;
  letter-spacing: -0.02em;
  color: var(--xm-text);
  margin-bottom: 0.5rem;
}
.xm-plans-heading__sub {
  font-size: 1rem;
  color: var(--xm-text-muted);
  margin: 0;
}

/* ── Plan grid & cards — inherit from plans.tpl styles ── */
.xm-plans-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
  gap: 1.5rem;
  margin-bottom: 2.5rem;
  align-items: stretch;
}
.xm-plan-card {
  background: var(--xm-bg);
  border: 2px solid var(--xm-border);
  border-radius: var(--xm-radius-lg);
  display: flex;
  flex-direction: column;
  position: relative;
  overflow: hidden;
  transition: box-shadow 0.2s, transform 0.2s, border-color 0.2s;
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
}
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
.xm-plan-card__footer { padding: 1rem 1.5rem 1.5rem; }
.xm-feature-list { list-style: none; padding: 0; margin: 0; }
.xm-feature-list__item {
  display: flex;
  align-items: flex-start;
  gap: 0.625rem;
  padding: 0.4rem 0;
  font-size: 0.9375rem;
  border-bottom: 1px solid var(--xm-border);
}
.xm-feature-list__item:last-child { border-bottom: none; }
.xm-feature-list__check { color: var(--xm-success); flex-shrink: 0; margin-top: 0.2rem; }

/* Featured CTA */
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
  padding-top: 2rem;
  border-top: 1px solid var(--xm-border);
  margin-top: 0.5rem;
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
@media (max-width: 720px) {
  .xm-value-props { flex-direction: column; }
  .xm-value-prop  { border-right: none; border-bottom: 1px solid var(--xm-border); }
  .xm-value-prop:last-child { border-bottom: none; }
  .xm-plans-grid { grid-template-columns: 1fr; }
  .xm-landing-hero { padding: 2.5rem 1rem 2rem; }
}

/* ── Dark mode ── */
@media (prefers-color-scheme: dark) {
  .xm-landing-hero__bg {
    background: linear-gradient(160deg, rgba(59,130,246,.08) 0%, rgba(99,102,241,.08) 50%, rgba(34,197,94,.05) 100%);
  }
  .xm-btn--hero-secondary {
    background: rgba(255,255,255,.07);
    border-color: rgba(255,255,255,.15);
    color: var(--xm-text) !important;
  }
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
