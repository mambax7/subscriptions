<{* Subscriptions – Invoice detail *}>
<{assign var="xm_module_url" value="`$xoops_url`/modules/subscriptions"}>

<div class="subscriptions-wrap">

  <{* ── Screen-only toolbar ─────────────────────────────────────── *}>
  <div class="xm-inv-toolbar no-print">
    <a href="<{$xm_module_url}>/invoices.php" class="xm-back-link">
      <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24"
           fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"
           aria-hidden="true"><polyline points="15 18 9 12 15 6"/></svg>
      <{$smarty.const._MD_SUBSCRIPTIONS_INVOICES_TITLE}>
    </a>
    <button type="button" class="xm-btn xm-btn--secondary xm-btn--sm" onclick="window.print()">
      <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24"
           fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
           aria-hidden="true"><polyline points="6 9 6 2 18 2 18 9"/><path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"/><rect x="6" y="14" width="12" height="8"/></svg>
      <{$smarty.const._MD_SUBSCRIPTIONS_PRINT_INVOICE}>
    </button>
  </div>

  <{* ── Invoice card ─────────────────────────────────────────────── *}>
  <div class="xm-inv-card">

    <{* Header: brand + invoice meta *}>
    <header class="xm-inv-card__header">
      <div class="xm-inv-card__brand">
        <div class="xm-inv-brand-logo" aria-hidden="true">&#x1F9FE;</div>
        <div>
          <div class="xm-inv-brand-name"><{$xm_site_name|escape}></div>
          <div class="xm-inv-brand-tagline"><{$smarty.const._MD_SUBSCRIPTIONS_INVOICE_OFFICIAL}></div>
        </div>
      </div>
      <div class="xm-inv-card__meta">
        <div class="xm-inv-meta-label"><{$smarty.const._MD_SUBSCRIPTIONS_INVOICES_TITLE}></div>
        <div class="xm-inv-number-large">#<{$xm_invoice.invoice_number|escape}></div>
        <div class="xm-inv-meta-row">
          <span class="xm-badge xm-badge--<{$xm_invoice.status}>"><{$xm_invoice.status|capitalize}></span>
        </div>
      </div>
    </header>

    <{* Colour accent stripe *}>
    <div class="xm-inv-card__stripe"></div>

    <{* Date + billing info row *}>
    <div class="xm-inv-info-row">
      <div class="xm-inv-info-block">
        <div class="xm-inv-info-block__label"><{$smarty.const._MD_SUBSCRIPTIONS_INV_BILLED_TO}></div>
        <div class="xm-inv-info-block__value"><{$xm_username|escape}></div>
      </div>
      <div class="xm-inv-info-block">
        <div class="xm-inv-info-block__label"><{$smarty.const._MD_SUBSCRIPTIONS_INVOICE_DATE}></div>
        <div class="xm-inv-info-block__value"><{$xm_invoice.created_at}></div>
      </div>
      <{if $xm_invoice.due_date neq '—'}>
      <div class="xm-inv-info-block">
        <div class="xm-inv-info-block__label"><{$smarty.const._MD_SUBSCRIPTIONS_INVOICE_DUE}></div>
        <div class="xm-inv-info-block__value"><{$xm_invoice.due_date}></div>
      </div>
      <{/if}>
      <{if $xm_invoice.period_start neq '—'}>
      <div class="xm-inv-info-block">
        <div class="xm-inv-info-block__label"><{$smarty.const._MD_SUBSCRIPTIONS_BILLING_PERIOD}></div>
        <div class="xm-inv-info-block__value">
          <{$xm_invoice.period_start}> – <{$xm_invoice.period_end}>
        </div>
      </div>
      <{/if}>
    </div>

    <{* Line items *}>
    <div class="xm-inv-items">
      <table class="xm-inv-table" role="table" data-sortable>
        <thead>
          <tr>
            <th scope="col" class="xm-inv-col--desc"><{$smarty.const._MD_SUBSCRIPTIONS_INV_DESCRIPTION}></th>
            <th scope="col" class="xm-inv-col--qty"><{$smarty.const._MD_SUBSCRIPTIONS_INV_QTY}></th>
            <th scope="col" class="xm-inv-col--price"><{$smarty.const._MD_SUBSCRIPTIONS_INV_UNIT_PRICE}></th>
            <th scope="col" class="xm-inv-col--amount"><{$smarty.const._MD_SUBSCRIPTIONS_INVOICE_TOTAL}></th>
          </tr>
        </thead>
        <tbody>
          <{foreach item=item from=$xm_items}>
          <tr>
            <td class="xm-inv-col--desc"><{$item.description|escape}></td>
            <td class="xm-inv-col--qty"><{$item.quantity}></td>
            <td class="xm-inv-col--price"><{$item.unit_price}></td>
            <td class="xm-inv-col--amount"><{$item.amount}></td>
          </tr>
          <{/foreach}>
        </tbody>
      </table>
    </div>

    <{* Totals *}>
    <div class="xm-inv-totals-wrap">
      <div class="xm-inv-totals">
        <div class="xm-inv-totals__row">
          <span><{$smarty.const._MD_SUBSCRIPTIONS_SUBTOTAL}></span>
          <span><{$xm_invoice.subtotal}></span>
        </div>
        <{if $xm_invoice.tax_rate > 0}>
        <div class="xm-inv-totals__row">
          <span><{$smarty.const._MD_SUBSCRIPTIONS_TAX}> (<{$xm_invoice.tax_rate}>%)</span>
          <span><{$xm_invoice.tax_amount}></span>
        </div>
        <{/if}>
        <{if $xm_invoice.discount_amount_raw > 0}>
        <div class="xm-inv-totals__row xm-inv-totals__row--discount">
          <span><{$smarty.const._MD_SUBSCRIPTIONS_DISCOUNT}></span>
          <span>−<{$xm_invoice.discount_amount}></span>
        </div>
        <{/if}>
        <div class="xm-inv-totals__row xm-inv-totals__row--total">
          <span><{$smarty.const._MD_SUBSCRIPTIONS_TOTAL}></span>
          <span><{$xm_invoice.total}></span>
        </div>
      </div>
    </div>

    <{* Notes *}>
    <{if $xm_invoice.notes}>
    <div class="xm-inv-notes">
      <div class="xm-inv-notes__label"><{$smarty.const._MD_SUBSCRIPTIONS_INV_NOTES}></div>
      <p><{$xm_invoice.notes|escape}></p>
    </div>
    <{/if}>

    <{* Footer *}>
    <div class="xm-inv-footer">
      <{$smarty.const._MD_SUBSCRIPTIONS_INV_THANKYOU}>
    </div>

  </div>
</div>

<style>
/* ── Screen toolbar ── */
.xm-inv-toolbar {
  display: flex;
  align-items: center;
  justify-content: space-between;
  margin-bottom: 1.5rem;
  flex-wrap: wrap;
  gap: 0.75rem;
}

/* ── Back link ── */
.xm-back-link {
  display: inline-flex;
  align-items: center;
  gap: 0.375rem;
  font-size: 0.875rem;
  color: var(--xm-text-muted);
  text-decoration: none;
  transition: color var(--xm-transition);
}
.xm-back-link:hover { color: var(--xm-primary); }

/* ── Invoice card ── */
.xm-inv-card {
  max-width: 820px;
  margin: 0 auto;
  background: var(--xm-bg);
  border: 1px solid var(--xm-border);
  border-radius: var(--xm-radius-lg);
  box-shadow: var(--xm-shadow-lg);
  overflow: hidden;
}

/* ── Dark header ── */
.xm-inv-card__header {
  display: flex;
  align-items: flex-start;
  justify-content: space-between;
  gap: 1.5rem;
  flex-wrap: wrap;
  padding: 2rem;
  background: linear-gradient(135deg, #1e3a5f 0%, #1d4ed8 100%);
  color: #fff;
}
.xm-inv-card__brand {
  display: flex;
  align-items: center;
  gap: 1rem;
}
.xm-inv-brand-logo {
  font-size: 1.75rem;
  background: rgba(255,255,255,.15);
  width: 52px; height: 52px;
  border-radius: 12px;
  display: flex;
  align-items: center;
  justify-content: center;
  flex-shrink: 0;
}
.xm-inv-brand-name {
  font-size: 1.375rem;
  font-weight: 800;
  letter-spacing: -0.01em;
}
.xm-inv-brand-tagline {
  font-size: 0.8125rem;
  opacity: 0.65;
  margin-top: 0.2rem;
}
.xm-inv-card__meta { text-align: right; }
.xm-inv-meta-label {
  font-size: 0.6875rem;
  font-weight: 700;
  text-transform: uppercase;
  letter-spacing: 0.1em;
  opacity: 0.65;
  margin-bottom: 0.3rem;
}
.xm-inv-number-large {
  font-size: 1.75rem;
  font-weight: 800;
  font-family: var(--xm-font-mono);
  letter-spacing: -0.02em;
  margin-bottom: 0.5rem;
}
.xm-inv-meta-row {
  display: flex;
  justify-content: flex-end;
}
/* badge on dark header */
.xm-inv-card__header .xm-badge        { background: rgba(255,255,255,.2); color: #fff; border: 1px solid rgba(255,255,255,.25); }
.xm-inv-card__header .xm-badge--paid  { background: rgba(34,197,94,.35); border-color: rgba(34,197,94,.5); }
.xm-inv-card__header .xm-badge--open  { background: rgba(245,158,11,.35); border-color: rgba(245,158,11,.5); }
.xm-inv-card__header .xm-badge--void  { background: rgba(107,114,128,.3); }

/* ── Gradient accent stripe ── */
.xm-inv-card__stripe {
  height: 4px;
  background: linear-gradient(90deg, #3b82f6 0%, #6366f1 50%, #06b6d4 100%);
}

/* ── Info row ── */
.xm-inv-info-row {
  display: flex;
  flex-wrap: wrap;
  gap: 1.25rem 2.5rem;
  padding: 1.5rem 2rem;
  border-bottom: 1px solid var(--xm-border);
  background: var(--xm-bg-soft);
}
.xm-inv-info-block__label {
  font-size: 0.6875rem;
  font-weight: 700;
  text-transform: uppercase;
  letter-spacing: 0.08em;
  color: var(--xm-text-muted);
  margin-bottom: 0.25rem;
}
.xm-inv-info-block__value {
  font-size: 0.9375rem;
  font-weight: 600;
  color: var(--xm-text);
}

/* ── Line-items table ── */
.xm-inv-items { padding: 0 2rem; }
.xm-inv-table {
  width: 100%;
  border-collapse: collapse;
  margin: 1.25rem 0;
}
.xm-inv-table thead tr { border-bottom: 2px solid var(--xm-border); }
.xm-inv-table th {
  font-size: 0.6875rem;
  font-weight: 700;
  text-transform: uppercase;
  letter-spacing: 0.08em;
  color: var(--xm-text-muted);
  padding: 0.5rem 0.75rem;
}
.xm-inv-table td {
  padding: 0.875rem 0.75rem;
  font-size: 0.9375rem;
  border-bottom: 1px solid var(--xm-border);
  vertical-align: middle;
}
.xm-inv-table tbody tr:last-child td { border-bottom: none; }
.xm-inv-table tbody tr:hover { background: var(--xm-bg-soft); }
.xm-inv-col--desc  { text-align: left; }
.xm-inv-col--qty   { text-align: center; width: 80px; color: var(--xm-text-muted); }
.xm-inv-col--price { text-align: right; width: 110px; color: var(--xm-text-muted); }
.xm-inv-col--amount{ text-align: right; width: 110px; font-weight: 700; }

/* ── Totals ── */
.xm-inv-totals-wrap {
  display: flex;
  justify-content: flex-end;
  padding: 0 2rem 1.5rem;
}
.xm-inv-totals {
  width: 100%;
  max-width: 300px;
  border: 1.5px solid var(--xm-border);
  border-radius: var(--xm-radius);
  overflow: hidden;
}
.xm-inv-totals__row {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 0.575rem 1rem;
  font-size: 0.9375rem;
  color: var(--xm-text-muted);
  border-bottom: 1px solid var(--xm-border);
}
.xm-inv-totals__row:last-child { border-bottom: none; }
.xm-inv-totals__row--discount { color: var(--xm-success); font-weight: 600; }
.xm-inv-totals__row--total {
  background: var(--xm-bg-soft);
  font-size: 1.0625rem;
  font-weight: 800;
  color: var(--xm-text);
}

/* ── Notes ── */
.xm-inv-notes {
  margin: 0 2rem 1.5rem;
  padding: 1rem 1.25rem;
  background: var(--xm-bg-soft);
  border-radius: var(--xm-radius);
  border: 1px solid var(--xm-border);
}
.xm-inv-notes__label {
  font-size: 0.6875rem;
  font-weight: 700;
  text-transform: uppercase;
  letter-spacing: 0.08em;
  color: var(--xm-text-muted);
  margin-bottom: 0.375rem;
}
.xm-inv-notes p { margin: 0; font-size: 0.9375rem; }

/* ── Footer ── */
.xm-inv-footer {
  text-align: center;
  padding: 1.25rem 2rem 1.75rem;
  font-size: 0.875rem;
  color: var(--xm-text-muted);
  border-top: 1px solid var(--xm-border);
  background: var(--xm-bg-soft);
}

/* ── Print ── */
@media print {
  body, .subscriptions-wrap { padding: 0 !important; margin: 0 !important; }
  .xm-inv-card { box-shadow: none; border: none; border-radius: 0; }
  .xm-inv-card__header {
    background: #1e3a5f !important;
    color: #fff !important;
    -webkit-print-color-adjust: exact;
    print-color-adjust: exact;
  }
  .xm-inv-card__stripe {
    background: #3b82f6 !important;
    -webkit-print-color-adjust: exact;
    print-color-adjust: exact;
  }
  .xm-inv-toolbar { display: none !important; }
}

/* ── Responsive ── */
@media (max-width: 600px) {
  .xm-inv-card__header { padding: 1.25rem; }
  .xm-inv-card__meta { text-align: left; }
  .xm-inv-meta-row { justify-content: flex-start; }
  .xm-inv-items { padding: 0 1rem; }
  .xm-inv-info-row { padding: 1rem; }
  .xm-inv-totals-wrap { padding: 0 1rem 1.25rem; }
  .xm-inv-totals { max-width: 100%; }
  .xm-inv-notes { margin: 0 1rem 1.25rem; }
  .xm-inv-footer { padding: 1rem; }
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
