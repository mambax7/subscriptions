<{* Subscriptions - Member Dashboard *}>
<{assign var="xm_module_url" value="`$xoops_url`/modules/subscriptions"}>

<div class="subscriptions-wrap">

  <{* ── Page header ─────────────────────────────────────────────── *}>
  <div class="xm-dash-header">
    <div>
      <h1 class="xm-dash-title"><{$smarty.const._MD_SUBSCRIPTIONS_DASHBOARD_TITLE}></h1>
      <p class="xm-dash-subtitle"><{$smarty.const._MD_SUBSCRIPTIONS_DASHBOARD_SUB}></p>
    </div>
    <a href="<{$xm_module_url}>/invoices.php" class="xm-btn xm-btn--secondary xm-btn--sm">
      <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24"
           fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
           aria-hidden="true"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/></svg>
      <{$smarty.const._MD_SUBSCRIPTIONS_INVOICES_TITLE}>
    </a>
  </div>

  <{* ── Current subscription ────────────────────────────────────── *}>
  <section aria-labelledby="xm-sub-heading" class="xm-dash-section">
    <h2 id="xm-sub-heading" class="xm-dash-section__title">
      <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24"
           fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
           aria-hidden="true"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
      <{$smarty.const._MD_SUBSCRIPTIONS_MY_SUBSCRIPTION}>
    </h2>

    <{if $xm_subscription}>

    <div class="xm-sub-panel xm-sub-panel--<{$xm_subscription.status_raw}>">

      <{* Status strip *}>
      <div class="xm-sub-panel__status-bar">
        <span class="xm-badge xm-badge--<{$xm_subscription.status_raw}>"><{$xm_subscription.status}></span>
        <{if $xm_subscription.is_trial}>
        <span class="xm-badge xm-badge--trial">
          <{$smarty.const._MD_SUBSCRIPTIONS_TRIAL_ENDS}> <{$xm_subscription.trial_ends_at}>
        </span>
        <{/if}>
        <{if $xm_subscription.cancel_at_period_end}>
        <span class="xm-badge xm-badge--past_due">
          <{$smarty.const._MD_SUBSCRIPTIONS_CANCELS_ON}> <{$xm_subscription.period_end}>
        </span>
        <{/if}>
      </div>

      <{* Main info + actions *}>
      <div class="xm-sub-panel__body">
        <div class="xm-sub-panel__info">
          <div class="xm-sub-panel__plan"><{$xm_subscription.plan_name|escape}></div>
          <div class="xm-sub-panel__meta">
            <span class="xm-sub-meta-item">
              <svg xmlns="http://www.w3.org/2000/svg" width="13" height="13" viewBox="0 0 24 24"
                   fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                   aria-hidden="true"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
              <{$xm_subscription.billing_cycle}>
            </span>
            <span class="xm-sub-meta-item">
              <svg xmlns="http://www.w3.org/2000/svg" width="13" height="13" viewBox="0 0 24 24"
                   fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                   aria-hidden="true"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
              <{if $xm_subscription.cancel_at_period_end}>
                <{$smarty.const._MD_SUBSCRIPTIONS_EXPIRES_ON}> <{$xm_subscription.period_end}>
              <{else}>
                <{$smarty.const._MD_SUBSCRIPTIONS_RENEWS_ON}> <{$xm_subscription.period_end}>
              <{/if}>
            </span>
            <span class="xm-sub-meta-item xm-sub-meta-item--<{if $xm_subscription.auto_renew}>on<{else}>off<{/if}>">
              <{if $xm_subscription.auto_renew}>
                <svg xmlns="http://www.w3.org/2000/svg" width="13" height="13" viewBox="0 0 24 24"
                     fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"
                     aria-hidden="true"><polyline points="20 6 9 17 4 12"/></svg>
                <{$smarty.const._MD_SUBSCRIPTIONS_AUTO_RENEW_ON}>
              <{else}>
                <svg xmlns="http://www.w3.org/2000/svg" width="13" height="13" viewBox="0 0 24 24"
                     fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"
                     aria-hidden="true"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
                <{$smarty.const._MD_SUBSCRIPTIONS_AUTO_RENEW_OFF}>
              <{/if}>
            </span>
          </div>
        </div>

        <div class="xm-sub-panel__actions">
          <{if $xm_subscription.cancel_at_period_end}>
          <button type="button" class="xm-btn xm-btn--primary xm-btn--sm"
                  onclick="document.getElementById('xm-reactivate-form').style.display='block';this.closest('.xm-sub-panel__actions').style.display='none'">
            <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24"
                 fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"
                 aria-hidden="true"><polyline points="1 4 1 10 7 10"/><path d="M3.51 15a9 9 0 1 0 .49-4.5"/></svg>
            <{$smarty.const._MD_SUBSCRIPTIONS_REACTIVATE}>
          </button>
          <{else}>
          <button type="button" class="xm-btn xm-btn--text xm-btn--sm xm-cancel-trigger"
                  onclick="document.getElementById('xm-cancel-form').style.display='block';this.closest('.xm-sub-panel__actions').style.display='none'">
            <{$smarty.const._MD_SUBSCRIPTIONS_CANCEL_SUBSCRIPTION}>
          </button>
          <{/if}>
        </div>
      </div>

      <{* Cancel form *}>
      <div id="xm-cancel-form" class="xm-inline-dialog" style="display:none;"
           role="dialog" aria-modal="true" aria-labelledby="cancel-heading">
        <h3 id="cancel-heading" class="xm-inline-dialog__title xm-inline-dialog__title--danger">
          <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24"
               fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"
               aria-hidden="true"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
          <{$smarty.const._MD_SUBSCRIPTIONS_CONFIRM_CANCEL}>
        </h3>
        <form method="post" action="<{$xm_module_url}>/dashboard.php">
          <input type="hidden" name="op"    value="cancel">
          <input type="hidden" name="token" value="<{$xm_subscription.cancel_token}>">
          <div class="xm-form-group">
            <label for="cancel-reason" class="xm-label"><{$smarty.const._MD_SUBSCRIPTIONS_CANCEL_REASON}></label>
            <textarea id="cancel-reason" name="reason" class="xm-input xm-textarea" rows="2"></textarea>
          </div>
          <fieldset class="xm-fieldset">
            <legend class="xm-label"><{$smarty.const._MD_SUBSCRIPTIONS_CANCEL_WHEN}></legend>
            <label class="xm-radio-label">
              <input type="radio" name="cancel_when" value="period_end" checked>
              <{$smarty.const._MD_SUBSCRIPTIONS_CANCEL_AT_PERIOD_END}> (<{$xm_subscription.period_end}>)
            </label>
            <label class="xm-radio-label">
              <input type="radio" name="cancel_when" value="now">
              <{$smarty.const._MD_SUBSCRIPTIONS_CANCEL_NOW}>
            </label>
          </fieldset>
          <div class="xm-inline-dialog__actions">
            <button type="submit" class="xm-btn xm-btn--danger xm-btn--sm">
              <{$smarty.const._MD_SUBSCRIPTIONS_CANCEL_SUBSCRIPTION}>
            </button>
            <button type="button" class="xm-btn xm-btn--text xm-btn--sm"
                    onclick="document.getElementById('xm-cancel-form').style.display='none';document.querySelector('.xm-sub-panel__actions').style.display=''">
              <{$smarty.const._AM_SUBSCRIPTIONS_CANCEL}>
            </button>
          </div>
        </form>
      </div>

      <{* Reactivate form *}>
      <{if $xm_subscription.cancel_at_period_end}>
      <div id="xm-reactivate-form" class="xm-inline-dialog" style="display:none;"
           role="dialog" aria-modal="true" aria-labelledby="reactivate-heading">
        <h3 id="reactivate-heading" class="xm-inline-dialog__title">
          <{$smarty.const._MD_SUBSCRIPTIONS_CONFIRM_REACTIVATE}>
        </h3>
        <p class="xm-inline-dialog__desc"><{$smarty.const._MD_SUBSCRIPTIONS_REACTIVATE_DESC}></p>
        <form method="post" action="<{$xm_module_url}>/dashboard.php">
          <input type="hidden" name="op"    value="reactivate">
          <input type="hidden" name="token" value="<{$xm_subscription.reactivate_token}>">
          <div class="xm-inline-dialog__actions">
            <button type="submit" class="xm-btn xm-btn--primary xm-btn--sm">
              <{$smarty.const._MD_SUBSCRIPTIONS_REACTIVATE}>
            </button>
            <button type="button" class="xm-btn xm-btn--text xm-btn--sm"
                    onclick="document.getElementById('xm-reactivate-form').style.display='none';document.querySelector('.xm-sub-panel__actions').style.display=''">
              <{$smarty.const._AM_SUBSCRIPTIONS_CANCEL}>
            </button>
          </div>
        </form>
      </div>
      <{/if}>

    </div>

    <{else}>

    <div class="xm-empty-state">
      <div class="xm-empty-state__icon" aria-hidden="true">&#x1F4CB;</div>
      <p><{$smarty.const._MD_SUBSCRIPTIONS_NO_SUBSCRIPTION}></p>
      <a href="<{$xm_plans_url}>" class="xm-btn xm-btn--primary">
        <{$smarty.const._MD_SUBSCRIPTIONS_PLANS_TITLE}>
        <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24"
             fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"
             aria-hidden="true"><line x1="5" y1="12" x2="19" y2="12"/><polyline points="12 5 19 12 12 19"/></svg>
      </a>
    </div>

    <{/if}>
  </section>

  <{* ── Usage summary ────────────────────────────────────────────── *}>
  <{if $xm_subscription && $xm_subscription.usage_summary}>
  <section aria-labelledby="xm-usage-heading" class="xm-dash-section">
    <h2 id="xm-usage-heading" class="xm-dash-section__title">
      <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24"
           fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
           aria-hidden="true"><line x1="18" y1="20" x2="18" y2="10"/><line x1="12" y1="20" x2="12" y2="4"/><line x1="6" y1="20" x2="6" y2="14"/></svg>
      <{$smarty.const._MD_SUBSCRIPTIONS_USAGE_THIS_PERIOD}>
    </h2>
    <div class="xm-table-wrap">
      <table class="xm-table" data-sortable>
        <thead>
          <tr>
            <th scope="col"><{$smarty.const._MD_SUBSCRIPTIONS_USAGE_EVENT}></th>
            <th scope="col"><{$smarty.const._MD_SUBSCRIPTIONS_USAGE_QUANTITY}></th>
            <th scope="col"><{$smarty.const._MD_SUBSCRIPTIONS_USAGE_COST}></th>
            <th scope="col"><{$smarty.const._MD_SUBSCRIPTIONS_USAGE_LAST}></th>
          </tr>
        </thead>
        <tbody>
          <{foreach item=u from=$xm_subscription.usage_summary}>
          <tr>
            <td><{$u.event_type|escape|capitalize}></td>
            <td><{$u.total_qty|string_format:"%.0f"}></td>
            <td><{if $u.total_cost > 0}><{$u.total_cost|string_format:"%.2f"}><{else}>—<{/if}></td>
            <td><{$u.last_event}></td>
          </tr>
          <{/foreach}>
        </tbody>
        <{if $xm_subscription.usage_unbilled_total > 0}>
        <tfoot>
          <tr>
            <th colspan="2"><{$smarty.const._MD_SUBSCRIPTIONS_USAGE_UNBILLED}></th>
            <td><strong><{$xm_subscription.usage_unbilled_total|string_format:"%.2f"}></strong></td>
            <td></td>
          </tr>
        </tfoot>
        <{/if}>
      </table>
    </div>
  </section>
  <{/if}>

  <{* ── Recent invoices ──────────────────────────────────────────── *}>
  <section aria-labelledby="xm-inv-heading" class="xm-dash-section">
    <h2 id="xm-inv-heading" class="xm-dash-section__title">
      <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24"
           fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
           aria-hidden="true"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/></svg>
      <{$smarty.const._MD_SUBSCRIPTIONS_INVOICES_TITLE}>
      <a href="<{$xm_invoices_url}>" class="xm-dash-section__link">
        <{$smarty.const._MD_SUBSCRIPTIONS_VIEW_ALL}> →
      </a>
    </h2>

    <{if $xm_invoices}>
    <div class="xm-inv-list" role="list">
      <{foreach item=inv from=$xm_invoices}>
      <div class="xm-inv-row-card xm-inv-row-card--<{$inv.status}>" role="listitem">
        <div class="xm-inv-row-card__icon" aria-hidden="true">
          <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24"
               fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/>
            <polyline points="14 2 14 8 20 8"/>
          </svg>
        </div>
        <div class="xm-inv-row-card__number"><{$inv.invoice_number|escape}></div>
        <div class="xm-inv-row-card__date"><{$inv.created_at}></div>
        <div class="xm-inv-row-card__status">
          <span class="xm-badge xm-badge--<{$inv.status}>"><{$inv.status|capitalize}></span>
        </div>
        <div class="xm-inv-row-card__total"><{$inv.total}></div>
        <div class="xm-inv-row-card__action">
          <a href="<{$inv.view_url}>" class="xm-btn xm-btn--default xm-btn--xs">
            <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24"
                 fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"
                 aria-hidden="true"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
            <{$smarty.const._MD_SUBSCRIPTIONS_INVOICE_VIEW}>
          </a>
        </div>
      </div>
      <{/foreach}>
    </div>
    <{else}>
    <p class="xm-empty"><{$smarty.const._MD_SUBSCRIPTIONS_NO_INVOICES}></p>
    <{/if}>
  </section>

  <{* ── Subscription history ─────────────────────────────────────── *}>
  <{if $xm_sub_history|@count > 1}>
  <section aria-labelledby="xm-hist-heading" class="xm-dash-section">
    <h2 id="xm-hist-heading" class="xm-dash-section__title">
      <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24"
           fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
           aria-hidden="true"><polyline points="1 4 1 10 7 10"/><path d="M3.51 15a9 9 0 1 0 .49-4.5"/></svg>
      <{$smarty.const._MD_SUBSCRIPTIONS_SUBSCRIPTION_HISTORY}>
    </h2>
    <div class="xm-hist-list" role="list">
      <{foreach item=h from=$xm_sub_history}>
      <div class="xm-hist-row" role="listitem">
        <div class="xm-hist-row__dot" aria-hidden="true"></div>
        <div class="xm-hist-row__plan"><{$h.plan_name|escape}></div>
        <div class="xm-hist-row__status">
          <span class="xm-badge"><{$h.status}></span>
        </div>
        <div class="xm-hist-row__dates">
          <span><{$h.started_at}></span>
          <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24"
               fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"
               aria-hidden="true"><line x1="5" y1="12" x2="19" y2="12"/><polyline points="12 5 19 12 12 19"/></svg>
          <span><{$h.period_end}></span>
        </div>
      </div>
      <{/foreach}>
    </div>
  </section>
  <{/if}>

</div>

<style>
/* ── Dashboard header ── */
.xm-dash-header {
  display: flex;
  align-items: flex-start;
  justify-content: space-between;
  gap: 1rem;
  flex-wrap: wrap;
  margin-bottom: 2rem;
}
.xm-dash-title {
  font-size: clamp(1.5rem, 3vw, 2rem);
  font-weight: 800;
  margin: 0 0 0.25rem;
  color: var(--xm-text);
  letter-spacing: -0.02em;
}
.xm-dash-subtitle {
  font-size: 0.9375rem;
  color: var(--xm-text-muted);
  margin: 0;
}

/* ── Dashboard sections ── */
.xm-dash-section {
  margin-bottom: 2.5rem;
}
.xm-dash-section__title {
  display: flex;
  align-items: center;
  gap: 0.5rem;
  font-size: 1rem;
  font-weight: 700;
  text-transform: uppercase;
  letter-spacing: 0.06em;
  color: var(--xm-text-muted);
  margin-bottom: 1rem;
  padding-bottom: 0.625rem;
  border-bottom: 2px solid var(--xm-border);
}
.xm-dash-section__link {
  margin-left: auto;
  font-size: 0.8125rem;
  font-weight: 600;
  font-style: normal;
  text-transform: none;
  letter-spacing: 0;
  color: var(--xm-primary);
  text-decoration: none;
}
.xm-dash-section__link:hover { text-decoration: underline; }

/* ── Subscription panel ── */
.xm-sub-panel {
  background: var(--xm-bg);
  border: 2px solid var(--xm-border);
  border-radius: var(--xm-radius-lg);
  overflow: hidden;
  box-shadow: var(--xm-shadow-md);
  transition: border-color 0.2s;
}
.xm-sub-panel--active { border-color: var(--xm-success); }
.xm-sub-panel--trial  { border-color: var(--xm-primary); }
.xm-sub-panel--past_due { border-color: var(--xm-warning); }
.xm-sub-panel--cancelled,
.xm-sub-panel--expired { border-color: var(--xm-border); }

.xm-sub-panel__status-bar {
  display: flex;
  align-items: center;
  flex-wrap: wrap;
  gap: 0.5rem;
  padding: 0.6rem 1.25rem;
  background: var(--xm-bg-soft);
  border-bottom: 1px solid var(--xm-border);
}
.xm-sub-panel__body {
  display: flex;
  align-items: flex-start;
  justify-content: space-between;
  gap: 1rem;
  flex-wrap: wrap;
  padding: 1.5rem 1.25rem;
}
.xm-sub-panel__plan {
  font-size: 1.375rem;
  font-weight: 800;
  color: var(--xm-text);
  margin-bottom: 0.625rem;
  letter-spacing: -0.01em;
}
.xm-sub-panel__meta {
  display: flex;
  flex-wrap: wrap;
  gap: 0.5rem 1.25rem;
}
.xm-sub-meta-item {
  display: flex;
  align-items: center;
  gap: 0.35rem;
  font-size: 0.875rem;
  color: var(--xm-text-muted);
}
.xm-sub-meta-item--on { color: var(--xm-success); }
.xm-sub-meta-item--off { color: var(--xm-danger); }
.xm-sub-panel__actions { flex-shrink: 0; }

/* cancel trigger looks like a link, not a scary button */
.xm-cancel-trigger {
  color: var(--xm-danger) !important;
}

/* ── Inline dialog (cancel / reactivate) ── */
.xm-inline-dialog {
  margin: 0 1.25rem 1.25rem;
  padding: 1.25rem;
  background: var(--xm-bg-soft);
  border: 1.5px solid var(--xm-border);
  border-radius: var(--xm-radius-lg);
  animation: xm-slide-in 0.15s ease;
}
@keyframes xm-slide-in {
  from { opacity: 0; transform: translateY(-6px); }
  to   { opacity: 1; transform: translateY(0); }
}
.xm-inline-dialog__title {
  display: flex;
  align-items: center;
  gap: 0.5rem;
  font-size: 0.9375rem;
  font-weight: 700;
  margin: 0 0 1rem;
}
.xm-inline-dialog__title--danger { color: var(--xm-danger); }
.xm-inline-dialog__desc {
  font-size: 0.9375rem;
  color: var(--xm-text-muted);
  margin-bottom: 1rem;
}
.xm-inline-dialog__actions {
  display: flex;
  gap: 0.5rem;
  flex-wrap: wrap;
  margin-top: 1rem;
}
.xm-fieldset {
  border: none;
  padding: 0;
  margin: 0 0 1rem;
}
.xm-radio-label {
  display: flex;
  align-items: center;
  gap: 0.5rem;
  font-size: 0.9375rem;
  cursor: pointer;
  padding: 0.375rem 0;
}

/* ── Invoice card-row list ── */
.xm-inv-list {
  display: flex;
  flex-direction: column;
  gap: 0.5rem;
}
.xm-inv-row-card {
  display: grid;
  grid-template-columns: 2rem 1fr auto auto auto auto;
  align-items: center;
  gap: 0.75rem 1rem;
  padding: 0.875rem 1rem;
  background: var(--xm-bg);
  border: 1.5px solid var(--xm-border);
  border-radius: var(--xm-radius);
  transition: border-color 0.15s, box-shadow 0.15s;
}
.xm-inv-row-card:hover {
  border-color: var(--xm-primary-muted, #93c5fd);
  box-shadow: var(--xm-shadow-md);
}
.xm-inv-row-card--paid   { border-left: 4px solid var(--xm-success); }
.xm-inv-row-card--open   { border-left: 4px solid var(--xm-warning); }
.xm-inv-row-card--void   { border-left: 4px solid var(--xm-border); opacity: 0.65; }

.xm-inv-row-card__icon {
  display: flex;
  align-items: center;
  justify-content: center;
  width: 2rem;
  height: 2rem;
  background: var(--xm-bg-soft);
  border-radius: var(--xm-radius-sm, 6px);
  color: var(--xm-text-muted);
  flex-shrink: 0;
}
.xm-inv-row-card__number {
  font-family: var(--xm-font-mono);
  font-size: 0.875rem;
  font-weight: 600;
  color: var(--xm-text);
  min-width: 0;
  overflow: hidden;
  text-overflow: ellipsis;
  white-space: nowrap;
}
.xm-inv-row-card__date {
  font-size: 0.8125rem;
  color: var(--xm-text-muted);
  white-space: nowrap;
}
.xm-inv-row-card__status { white-space: nowrap; }
.xm-inv-row-card__total {
  font-size: 0.9375rem;
  font-weight: 700;
  color: var(--xm-text);
  white-space: nowrap;
  text-align: right;
}
.xm-inv-row-card__action { flex-shrink: 0; }

/* ── Subscription history timeline ── */
.xm-hist-list {
  display: flex;
  flex-direction: column;
  gap: 0;
  border-left: 2px solid var(--xm-border);
  margin-left: 0.5rem;
  padding-left: 0;
}
.xm-hist-row {
  display: grid;
  grid-template-columns: auto 1fr auto auto;
  align-items: center;
  gap: 0.5rem 1rem;
  position: relative;
  padding: 0.75rem 0.75rem 0.75rem 1.5rem;
}
.xm-hist-row + .xm-hist-row {
  border-top: 1px solid var(--xm-border);
}
.xm-hist-row__dot {
  position: absolute;
  left: -0.45rem;
  top: 50%;
  transform: translateY(-50%);
  width: 0.625rem;
  height: 0.625rem;
  border-radius: 50%;
  background: var(--xm-primary);
  border: 2px solid var(--xm-bg);
  box-shadow: 0 0 0 2px var(--xm-primary);
  flex-shrink: 0;
}
.xm-hist-row__plan {
  font-size: 0.9375rem;
  font-weight: 600;
  color: var(--xm-text);
  min-width: 0;
  overflow: hidden;
  text-overflow: ellipsis;
  white-space: nowrap;
}
.xm-hist-row__status { white-space: nowrap; }
.xm-hist-row__dates {
  display: flex;
  align-items: center;
  gap: 0.375rem;
  font-size: 0.8125rem;
  color: var(--xm-text-muted);
  white-space: nowrap;
}

/* ── Responsive ── */
@media (max-width: 680px) {
  .xm-inv-row-card {
    grid-template-columns: 2rem 1fr auto;
    grid-template-rows: auto auto;
  }
  .xm-inv-row-card__date   { grid-column: 2; grid-row: 2; }
  .xm-inv-row-card__status { grid-column: 3; grid-row: 2; }
  .xm-inv-row-card__total  { grid-column: 2; grid-row: 2; display: none; }
  .xm-inv-row-card__action { grid-column: 3; grid-row: 1; }
  .xm-hist-row {
    grid-template-columns: 1fr auto;
    grid-template-rows: auto auto;
  }
  .xm-hist-row__status { grid-column: 2; grid-row: 1; }
  .xm-hist-row__dates  { grid-column: 1 / -1; grid-row: 2; }
}
@media (max-width: 600px) {
  .xm-sub-panel__body { flex-direction: column; }
  .xm-dash-header { flex-direction: column; align-items: flex-start; }
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
