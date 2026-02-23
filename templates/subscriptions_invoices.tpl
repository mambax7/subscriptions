<{* Subscriptions - Invoices list *}>
<{assign var="xm_module_url" value="`$xoops_url`/modules/subscriptions"}>

<div class="subscriptions-wrap">

  <{* ── Page header ─────────────────────────────────────────────── *}>
  <div class="xm-inv-header">
    <div>
      <a href="<{$xm_dashboard_url}>" class="xm-back-link">
        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24"
             fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"
             aria-hidden="true"><polyline points="15 18 9 12 15 6"/></svg>
        <{$smarty.const._MD_SUBSCRIPTIONS_DASHBOARD_TITLE}>
      </a>
      <h1 class="xm-dash-title"><{$smarty.const._MD_SUBSCRIPTIONS_INVOICES_TITLE}></h1>
    </div>
  </div>

  <{* ── Invoice list ─────────────────────────────────────────────── *}>
  <{if $xm_invoices}>

  <div class="xm-table-wrap">
    <table class="xm-table" data-sortable>
      <thead>
        <tr>
          <th scope="col"><{$smarty.const._MD_SUBSCRIPTIONS_INVOICE_NUMBER}></th>
          <th scope="col"><{$smarty.const._MD_SUBSCRIPTIONS_INVOICE_DATE}></th>
          <th scope="col"><{$smarty.const._MD_SUBSCRIPTIONS_INVOICE_DUE}></th>
          <th scope="col"><{$smarty.const._MD_SUBSCRIPTIONS_INVOICE_STATUS}></th>
          <th scope="col" class="xm-text-right"><{$smarty.const._MD_SUBSCRIPTIONS_INVOICE_TOTAL}></th>
          <th scope="col" data-no-sort><span class="sr-only"><{$smarty.const._MD_SUBSCRIPTIONS_INVOICE_VIEW}></span></th>
        </tr>
      </thead>
      <tbody>
        <{foreach item=inv from=$xm_invoices}>
        <tr class="xm-inv-row xm-inv-row--<{$inv.status}>">
          <td class="xm-inv-number"><{$inv.invoice_number|escape}></td>
          <td class="xm-inv-date"><{$inv.created_at}></td>
          <td class="xm-inv-date"><{$inv.due_date}></td>
          <td>
            <span class="xm-badge xm-badge--<{$inv.status}>"><{$inv.status|capitalize}></span>
          </td>
          <td class="xm-text-right xm-inv-amount">
            <strong><{$inv.total}></strong>
          </td>
          <td class="xm-actions">
            <a href="<{$inv.view_url}>" class="xm-btn xm-btn--default xm-btn--xs">
              <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24"
                   fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"
                   aria-hidden="true"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
              <{$smarty.const._MD_SUBSCRIPTIONS_INVOICE_VIEW}>
            </a>
          </td>
        </tr>
        <{/foreach}>
      </tbody>
    </table>
  </div>

  <{* Pagination if available *}>
  <{if isset($xm_pagenav) && $xm_pagenav}>
  <div class="xm-pagenav"><{$xm_pagenav nofilter}></div>
  <{/if}>

  <{else}>

  <div class="xm-empty-state">
    <div class="xm-empty-state__icon" aria-hidden="true">&#x1F9FE;</div>
    <p><{$smarty.const._MD_SUBSCRIPTIONS_NO_INVOICES}></p>
    <a href="<{$xm_dashboard_url}>" class="xm-btn xm-btn--primary">
      <{$smarty.const._MD_SUBSCRIPTIONS_DASHBOARD_TITLE}>
    </a>
  </div>

  <{/if}>

</div>

<style>
/* ── Invoices header ── */
.xm-inv-header {
  margin-bottom: 1.75rem;
}

/* ── Invoice table enhancements ── */
.xm-inv-row--paid { }
.xm-inv-row--open { background: #fffbeb; }
.xm-inv-row--void { opacity: 0.65; }
.xm-inv-number {
  font-family: var(--xm-font-mono);
  font-size: 0.875rem;
  font-weight: 600;
  color: var(--xm-text);
}
.xm-inv-date {
  font-size: 0.9rem;
  color: var(--xm-text-muted);
}
.xm-inv-amount {
  font-size: 1rem;
  color: var(--xm-text);
}

/* ── Back link (shared with checkout) ── */
.xm-back-link {
  display: inline-flex;
  align-items: center;
  gap: 0.375rem;
  font-size: 0.875rem;
  color: var(--xm-text-muted);
  text-decoration: none;
  margin-bottom: 0.5rem;
  transition: color var(--xm-transition);
}
.xm-back-link:hover { color: var(--xm-primary); }

/* ── Shared dash-title (if not already defined on this page) ── */
.xm-dash-title {
  font-size: clamp(1.5rem, 3vw, 2rem);
  font-weight: 800;
  margin: 0;
  color: var(--xm-text);
  letter-spacing: -0.02em;
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
