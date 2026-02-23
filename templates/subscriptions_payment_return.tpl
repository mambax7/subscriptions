<div class="subscriptions-wrap">

  <{if $xm_success}>
  <div class="xm-notice xm-notice--success" role="alert">
    <span class="xm-notice__icon" aria-hidden="true">✅</span>
    <div>
      <h1 class="xm-notice__title"><{$xm_message|escape}></h1>
      <a href="<{$xm_dashboard_url}>" class="xm-btn xm-btn--primary"><{$smarty.const._MD_SUBSCRIPTIONS_DASHBOARD_TITLE}></a>
    </div>
  </div>
  <{elseif $xm_failed}>
  <div class="xm-notice xm-notice--error" role="alert">
    <span class="xm-notice__icon" aria-hidden="true">❌</span>
    <div>
      <h1 class="xm-notice__title"><{$xm_message|escape}></h1>
      <a href="<{$xm_plans_url}>" class="xm-btn xm-btn--primary"><{$smarty.const._MD_SUBSCRIPTIONS_PLANS_TITLE}></a>
      <a href="<{$xm_dashboard_url}>" class="xm-btn xm-btn--text"><{$smarty.const._MD_SUBSCRIPTIONS_DASHBOARD_TITLE}></a>
    </div>
  </div>
  <{else}>
  <div class="xm-notice xm-notice--warning" role="alert">
    <span class="xm-notice__icon" aria-hidden="true">⏳</span>
    <div>
      <h1 class="xm-notice__title"><{$xm_message|escape}></h1>
      <a href="<{$xm_dashboard_url}>" class="xm-btn xm-btn--secondary"><{$smarty.const._MD_SUBSCRIPTIONS_DASHBOARD_TITLE}></a>
      <a href="<{$xm_plans_url}>" class="xm-btn xm-btn--text"><{$smarty.const._MD_SUBSCRIPTIONS_PLANS_TITLE}></a>
    </div>
  </div>
  <{/if}>

</div>
