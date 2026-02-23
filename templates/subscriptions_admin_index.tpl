<div class="subscriptions-admin">

  <h1 class="xm-admin-title"><{$smarty.const._AM_SUBSCRIPTIONS_DASHBOARD}></h1>

  <{if $xm_testdata_buttons}><{$xm_testdata_buttons nofilter}><{/if}>

  <!-- Subscriptions -->
  <h2 class="xm-stats-group-label"><{$smarty.const._AM_SUBSCRIPTIONS_SUBSCRIPTIONS}></h2>
  <div class="xm-stat-list">

    <div class="xm-stat-row xm-stat-row--blue">
      <span class="xm-stat-row__icon" aria-hidden="true">&#x1F465;</span>
      <span class="xm-stat-row__label"><{$smarty.const._AM_SUBSCRIPTIONS_ACTIVE_SUBS}></span>
      <span class="xm-stat-row__value"><{$xm_total_active}></span>
      <a href="subscriptions.php" class="xm-stat-row__link"><{$smarty.const._AM_SUBSCRIPTIONS_VIEW_ALL}></a>
    </div>

    <div class="xm-stat-row xm-stat-row--teal">
      <span class="xm-stat-row__icon" aria-hidden="true">&#x1F52C;</span>
      <span class="xm-stat-row__label"><{$smarty.const._AM_SUBSCRIPTIONS_TRIAL_SUBS}></span>
      <span class="xm-stat-row__value"><{$xm_total_trials}></span>
      <a href="subscriptions.php" class="xm-stat-row__link"><{$smarty.const._AM_SUBSCRIPTIONS_VIEW_ALL}></a>
    </div>

    <div class="xm-stat-row xm-stat-row--green">
      <span class="xm-stat-row__icon" aria-hidden="true">&#x1F4CB;</span>
      <span class="xm-stat-row__label"><{$smarty.const._AM_SUBSCRIPTIONS_ACTIVE_PLANS}></span>
      <span class="xm-stat-row__value"><{$xm_total_plans}></span>
      <a href="plans.php" class="xm-stat-row__link"><{$smarty.const._AM_SUBSCRIPTIONS_VIEW_ALL}></a>
    </div>

    <div class="xm-stat-row xm-stat-row--orange">
      <span class="xm-stat-row__icon" aria-hidden="true">&#x1F4B0;</span>
      <span class="xm-stat-row__label"><{$smarty.const._AM_SUBSCRIPTIONS_MONTH_REVENUE}></span>
      <span class="xm-stat-row__value"><{$xm_month_revenue}></span>
      <a href="payments.php" class="xm-stat-row__link"><{$smarty.const._AM_SUBSCRIPTIONS_VIEW_ALL}></a>
    </div>

  </div>

  <!-- Integrations -->
  <h2 class="xm-stats-group-label"><{$smarty.const._AM_SUBSCRIPTIONS_INTEGRATIONS}></h2>
  <div class="xm-stat-list">

    <div class="xm-stat-row xm-stat-row--purple">
      <span class="xm-stat-row__icon" aria-hidden="true">&#x1F39F;</span>
      <span class="xm-stat-row__label"><{$smarty.const._AM_SUBSCRIPTIONS_ACTIVE_COUPONS}></span>
      <span class="xm-stat-row__value"><{$xm_total_coupons}></span>
      <a href="coupons.php" class="xm-stat-row__link"><{$smarty.const._AM_SUBSCRIPTIONS_VIEW_ALL}></a>
    </div>

    <div class="xm-stat-row xm-stat-row--indigo">
      <span class="xm-stat-row__icon" aria-hidden="true">&#x1F50C;</span>
      <span class="xm-stat-row__label"><{$smarty.const._AM_SUBSCRIPTIONS_CONNECTED_MODULES}></span>
      <span class="xm-stat-row__value">
        <{$xm_total_modules_active}><{if $xm_total_modules_inactive > 0}> <span class="xm-text-muted">(+<{$xm_total_modules_inactive}> <{$smarty.const._AM_SUBSCRIPTIONS_INACTIVE}>)</span><{/if}>
      </span>
      <a href="modules.php" class="xm-stat-row__link"><{$smarty.const._AM_SUBSCRIPTIONS_VIEW_ALL}></a>
    </div>

    <div class="xm-stat-row xm-stat-row--gray">
      <span class="xm-stat-row__icon" aria-hidden="true">&#x1F514;</span>
      <span class="xm-stat-row__label"><{$smarty.const._AM_SUBSCRIPTIONS_ACTIVE_WEBHOOKS}></span>
      <span class="xm-stat-row__value"><{$xm_total_webhooks}></span>
      <a href="webhooks.php" class="xm-stat-row__link"><{$smarty.const._AM_SUBSCRIPTIONS_VIEW_ALL}></a>
    </div>

  </div>

  <!-- Quick Links -->
  <div class="xm-toolbar xm-toolbar--gap">
    <a href="plans.php?op=edit"    class="xm-btn xm-btn--sm xm-btn--primary"><{$smarty.const._AM_SUBSCRIPTIONS_PLAN_ADD}></a>
    <a href="coupons.php?op=edit"  class="xm-btn xm-btn--sm xm-btn--secondary"><{$smarty.const._AM_SUBSCRIPTIONS_COUPON_ADD}></a>
    <a href="modules.php?op=edit"  class="xm-btn xm-btn--sm xm-btn--secondary"><{$smarty.const._AM_SUBSCRIPTIONS_MODULE_ADD}></a>
    <a href="webhooks.php?op=edit" class="xm-btn xm-btn--sm xm-btn--secondary"><{$smarty.const._AM_SUBSCRIPTIONS_WEBHOOK_ADD}></a>
  </div>

  <!-- Cron tip: module-specific advice, shown above the standard checks -->
  <{if $xm_cron_tip}>
    <{$xm_cron_tip nofilter}>
  <{/if}>

  <!-- Two-column grid: Configuration Check (left) + Server Status (right)  -->
  <!-- Values appear inline next to their labels — no eye travel to far right -->
  <{if $xm_config_check || $xm_server_stats}>
    <div style="display:grid;grid-template-columns:3fr 2fr;gap:1.5rem;align-items:start;">

      <div>
        <{if $xm_config_check}>
          <h2 class="xm-stats-group-label"><{$smarty.const._AM_SUBSCRIPTIONS_CONFIG_CHECK}></h2>
          <{$xm_config_check nofilter}>
        <{/if}>
      </div>

      <div>
        <{if $xm_server_stats}>
          <h2 class="xm-stats-group-label"><{$smarty.const._AM_SUBSCRIPTIONS_SERVER_STATUS}></h2>
          <{$xm_server_stats nofilter}>
        <{/if}>
      </div>

    </div>
  <{/if}>

</div>
