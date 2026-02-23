<div class="subscriptions-admin">

  <h1 class="xm-admin-title"><{$smarty.const._AM_SUBSCRIPTIONS_REPORT_REVENUE}></h1>

  <!-- Period selector -->
  <nav class="xm-filter-nav" aria-label="Report period">
    <a href="reports.php?period=this_month" class="xm-filter-btn<{if $xm_period eq 'this_month'}> xm-filter-btn--active<{/if}>"><{$smarty.const._AM_SUBSCRIPTIONS_THIS_MONTH}></a>
    <a href="reports.php?period=last_month" class="xm-filter-btn<{if $xm_period eq 'last_month'}> xm-filter-btn--active<{/if}>"><{$smarty.const._AM_SUBSCRIPTIONS_LAST_MONTH}></a>
    <a href="reports.php?period=all_time"   class="xm-filter-btn<{if $xm_period eq 'all_time'}> xm-filter-btn--active<{/if}>"><{$smarty.const._AM_SUBSCRIPTIONS_ALL_TIME}></a>
  </nav>

  <div class="xm-stat-list">

    <div class="xm-stat-row xm-stat-row--orange">
      <span class="xm-stat-row__icon" aria-hidden="true">&#x1F4B0;</span>
      <span class="xm-stat-row__label"><{$smarty.const._AM_SUBSCRIPTIONS_MONTH_REVENUE}></span>
      <span class="xm-stat-row__value"><{$xm_revenue}></span>
    </div>

    <div class="xm-stat-row xm-stat-row--blue">
      <span class="xm-stat-row__icon" aria-hidden="true">&#x1F9FE;</span>
      <span class="xm-stat-row__label">Payments</span>
      <span class="xm-stat-row__value"><{$xm_pay_count}></span>
    </div>

    <div class="xm-stat-row xm-stat-row--green">
      <span class="xm-stat-row__icon" aria-hidden="true">&#x1F465;</span>
      <span class="xm-stat-row__label"><{$smarty.const._AM_SUBSCRIPTIONS_ACTIVE_SUBS}></span>
      <span class="xm-stat-row__value"><{$xm_active_subs}></span>
    </div>

    <div class="xm-stat-row xm-stat-row--purple">
      <span class="xm-stat-row__icon" aria-hidden="true">&#x2728;</span>
      <span class="xm-stat-row__label">New Subscriptions</span>
      <span class="xm-stat-row__value"><{$xm_new_subs}></span>
    </div>

  </div>

</div>
